<?php

namespace App\Controllers\Api\V1;

use App\Models\ProjectModel;
use App\Services\ContentCache;
use App\Services\I18n;
use App\Services\SettingService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ProjectController extends BaseApiController
{
    protected ProjectModel $model;
    protected SettingService $settings;
    protected UploadService $uploads;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new ProjectModel();
        $this->settings = new SettingService();
        $this->uploads = new UploadService();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = ContentCache::remember('projects', ContentCache::localeSuffix('id_' . $id), function () use ($id) {
                $found = $this->model->find($id);
                return $found ? $this->present($found) : null;
            });
            return $row ? $this->ok($row) : $this->fail('Api.projectNotFound', 404);
        }

        $rows = ContentCache::remember('projects', ContentCache::localeSuffix('list'), function () {
            return array_map([$this, 'present'], $this->model->orderBy('created_at', 'DESC')->findAll());
        });

        return $this->ok($rows);
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.projectCreated', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.projectIdRequired', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.projectUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.projectNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('projects', 'search', 'sync');
        return $this->ok(null, 'Api.projectDeleted');
    }

    public function settings()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = $this->body();
            $saved = $this->settings->putGroup('projects', [
                'title'       => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
            ]);
            ContentCache::forget('projects', 'sync');
            return $this->ok($saved, 'Api.homepageTitleSaved');
        }

        return $this->ok(ContentCache::remember(
            'projects',
            ContentCache::localeSuffix('settings'),
            function () {
                $saved = $this->settings->getGroup('projects');
                $title = trim((string) ($saved['title'] ?? ''));
                $description = trim((string) ($saved['description'] ?? ''));
                if ($title === '' || $title === 'Our Projects') {
                    $saved['title'] = I18n::content('Content.projectsTitle');
                }
                if ($description === '' || $description === 'Partnerships and campus development at Saint Joseph TSS Nzuki.') {
                    $saved['description'] = I18n::content('Content.projectsDescription');
                }
                return $saved;
            }
        ));
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return $this->fail('Api.titleRequired', 422);
        }

        $media = [];
        if ($id) {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->fail('Api.projectNotFound', 404);
            }
            $media = $this->decodeJson($existing['media'] ?? null, []);
        }

        $keep = $data['existingMedia'] ?? $this->request->getPost('existingMedia');
        if (is_string($keep)) {
            $decoded = json_decode($keep, true);
            $keep = is_array($decoded) ? $decoded : null;
        }
        if (is_array($keep)) {
            $media = array_values(array_filter($keep, static fn ($item) => is_array($item) && !empty($item['url'])));
        }

        $images = $this->uploads->saveMany($this->uploads->collect('images'), 'projects');
        $captions = $this->request->getPost('captions');
        if (!is_array($captions)) {
            $captions = $captions !== null ? [$captions] : [];
        }
        foreach ($images as $i => $url) {
            $media[] = [
                'type'      => 'image',
                'url'       => $url,
                'caption'   => $captions[$i] ?? '',
                'thumbnail' => '',
            ];
        }

        $videos = $this->uploads->saveMany($this->uploads->collect('videos'), 'projects');
        $videoCaptions = $this->request->getPost('videoCaptions');
        if (!is_array($videoCaptions)) {
            $videoCaptions = $videoCaptions !== null ? [$videoCaptions] : [];
        }
        $thumbs = $this->uploads->saveMany($this->uploads->collect('videoThumbnails'), 'projects');
        foreach ($videos as $i => $url) {
            $media[] = [
                'type'      => 'video',
                'url'       => $url,
                'caption'   => $videoCaptions[$i] ?? '',
                'thumbnail' => $thumbs[$i] ?? '',
            ];
        }

        $payload = [
            'title'       => $title,
            'description' => $data['description'] ?? '',
            'partner'     => $data['partner'] ?? '',
            'year'        => $data['year'] ?? '',
            'status'      => $data['status'] ?? 'Ongoing',
            'category'    => $data['category'] ?? '',
            'media'       => json_encode($media),
        ];

        if ($id) {
            $this->model->update($id, $payload);
            $saved = $this->present($this->model->find($id));
            ContentCache::forget('projects', 'search', 'sync');
            return $saved;
        }

        $newId = $this->model->insert($payload, true);
        $saved = $this->present($this->model->find($newId));
        ContentCache::forget('projects', 'search', 'sync');
        return $saved;
    }

    protected function present(array $row): array
    {
        $row['media'] = $this->decodeJson($row['media'] ?? null, []);
        $row['id'] = (int) $row['id'];
        $row['status_label'] = I18n::known('projectStatus', $row['status'] ?? '');
        return I18n::localizeRow($row, ['title', 'description']);
    }
}
