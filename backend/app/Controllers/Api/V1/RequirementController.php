<?php

namespace App\Controllers\Api\V1;

use App\Models\RequirementLevelModel;
use App\Services\ContentCache;
use App\Services\I18n;
use App\Services\SettingService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class RequirementController extends BaseApiController
{
    protected RequirementLevelModel $model;
    protected SettingService $settings;
    protected UploadService $uploads;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new RequirementLevelModel();
        $this->settings = new SettingService();
        $this->uploads = new UploadService();
    }

    public function index()
    {
        $rows = ContentCache::remember('requirements', ContentCache::localeSuffix('list'), function () {
            return array_map(
                [$this, 'present'],
                $this->model->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll()
            );
        });

        return $this->ok($rows);
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.levelCreated', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.levelIdRequired', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.levelUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.levelNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('requirements', 'search', 'sync');
        return $this->ok(null, 'Api.levelDeleted');
    }

    public function settings()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = $this->body();
            $saved = $this->settings->putGroup('requirements', [
                'title'         => $data['title'] ?? '',
                'subtitle'      => $data['subtitle'] ?? '',
                'academic_year' => $data['academic_year'] ?? '',
            ]);
            ContentCache::forget('requirements', 'sync');
            return $this->ok($saved, 'Api.sectionTitleSaved');
        }

        return $this->ok(ContentCache::remember(
            'requirements',
            ContentCache::localeSuffix('settings'),
            function () {
                $saved = $this->settings->getGroup('requirements');
                $title = trim((string) ($saved['title'] ?? ''));
                $subtitle = trim((string) ($saved['subtitle'] ?? ''));
                if ($title === '') {
                    $saved['title'] = I18n::content('Content.requirementsTitle');
                }
                if ($subtitle === '') {
                    $saved['subtitle'] = I18n::content('Content.requirementsSubtitle');
                }
                return I18n::localizeRow($saved, ['title', 'subtitle']);
            }
        ));
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $code = trim((string) ($data['code'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));
        if ($code === '' || $name === '') {
            return $this->fail('Api.codeNameRequired', 422);
        }

        $urls = $data['urls'] ?? [];
        if (is_string($urls)) {
            $urls = $this->decodeJson($urls, []);
        }
        $normalized = [];
        foreach (is_array($urls) ? $urls : [] as $item) {
            if (is_array($item)) {
                $item = $item['url'] ?? '';
            }
            $item = trim((string) $item);
            if ($item !== '') {
                $normalized[] = $item;
            }
        }
        $urls = array_values($normalized);

        $files = $this->uploads->saveMany($this->uploads->collect('files'), 'requirements');
        foreach ($files as $path) {
            $urls[] = $path;
        }

        $payload = [
            'code'        => $code,
            'name'        => $name,
            'description' => $data['description'] ?? '',
            'sort_order'  => (int) ($data['sort_order'] ?? 0),
            'urls'        => json_encode(array_map(static fn ($url) => ['url' => $url], $urls)),
        ];

        if ($id) {
            if (!$this->model->find($id)) {
                return $this->fail('Api.levelNotFound', 404);
            }
            $this->model->update($id, $payload);
            $saved = $this->present($this->model->find($id));
            ContentCache::forget('requirements', 'search', 'sync');
            return $saved;
        }

        $newId = $this->model->insert($payload, true);
        $saved = $this->present($this->model->find($newId));
        ContentCache::forget('requirements', 'search', 'sync');
        return $saved;
    }

    protected function present(array $row): array
    {
        $urls = $this->decodeJson($row['urls'] ?? null, []);
        $row['urls'] = array_map(static function ($item) {
            if (is_string($item)) {
                return ['url' => $item];
            }
            return $item;
        }, $urls);
        $row['id'] = (int) $row['id'];
        $row['sort_order'] = (int) $row['sort_order'];
        $slug = I18n::slug('level', (string) ($row['code'] ?? ''));
        if (!in_array($slug, ['level1', 'level3', 'level4', 'level5'], true)) {
            $slug = I18n::slug('level', (string) ($row['name'] ?? ''));
        }
        $nameI18n = I18n::content('Content.level.' . $slug);
        $descI18n = I18n::content('Content.levelDesc.' . $slug);
        $row['name_i18n'] = str_starts_with($nameI18n, 'Content.') ? ($row['name'] ?? '') : $nameI18n;
        $row['description_i18n'] = str_starts_with($descI18n, 'Content.') ? ($row['description'] ?? '') : $descI18n;
        return $row;
    }
}
