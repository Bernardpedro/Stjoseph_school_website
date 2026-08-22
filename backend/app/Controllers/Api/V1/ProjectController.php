<?php

namespace App\Controllers\Api\V1;

use App\Models\ProjectModel;
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
            $row = $this->model->find($id);
            return $row ? $this->ok($this->present($row)) : $this->fail('Project not found', 404);
        }

        $rows = $this->model->orderBy('created_at', 'DESC')->findAll();
        return $this->ok(array_map([$this, 'present'], $rows));
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Project created.', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Project id is required.', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Project updated.');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Project not found', 404);
        }
        $this->model->delete($id);
        return $this->ok(null, 'Project deleted.');
    }

    public function settings()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = $this->body();
            $saved = $this->settings->putGroup('projects', [
                'title'       => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
            ]);
            return $this->ok($saved, 'Homepage section title saved.');
        }

        return $this->ok($this->settings->getGroup('projects'));
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return $this->fail('Title is required.', 422);
        }

        $media = [];
        if ($id) {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->fail('Project not found', 404);
            }
            $media = $this->decodeJson($existing['media'] ?? null, []);
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
            return $this->present($this->model->find($id));
        }

        $newId = $this->model->insert($payload, true);
        return $this->present($this->model->find($newId));
    }

    protected function present(array $row): array
    {
        $row['media'] = $this->decodeJson($row['media'] ?? null, []);
        $row['id'] = (int) $row['id'];
        return $row;
    }
}
