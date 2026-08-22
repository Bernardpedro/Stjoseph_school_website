<?php

namespace App\Controllers\Api\V1;

use App\Models\RequirementLevelModel;
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
        $rows = $this->model->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
        return $this->ok(array_map([$this, 'present'], $rows));
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Level created.', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Level id is required.', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Level updated.');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Level not found', 404);
        }
        $this->model->delete($id);
        return $this->ok(null, 'Level deleted.');
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
            return $this->ok($saved, 'Section title saved.');
        }

        return $this->ok($this->settings->getGroup('requirements'));
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $code = trim((string) ($data['code'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));
        if ($code === '' || $name === '') {
            return $this->fail('Code and name are required.', 422);
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
                return $this->fail('Level not found', 404);
            }
            $this->model->update($id, $payload);
            return $this->present($this->model->find($id));
        }

        $newId = $this->model->insert($payload, true);
        return $this->present($this->model->find($newId));
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
        return $row;
    }
}
