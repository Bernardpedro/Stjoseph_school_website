<?php

namespace App\Controllers\Api\V1;

use App\Models\EventModel;
use App\Services\ContentCache;
use App\Services\I18n;
use App\Services\MediaUrlService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class EventController extends BaseApiController
{
    protected EventModel $model;
    protected UploadService $uploads;
    protected MediaUrlService $media;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new EventModel();
        $this->uploads = new UploadService();
        $this->media = new MediaUrlService();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = ContentCache::remember('events', ContentCache::localeSuffix('id_' . $id), function () use ($id) {
                $found = $this->model->find($id);
                return $found ? $this->present($found) : null;
            });
            return $row ? $this->ok($row) : $this->fail('Api.eventNotFound', 404);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 0);
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $rows = ContentCache::remember(
            'events',
            ContentCache::localeSuffix('list_' . $limit . '_' . $page),
            function () use ($limit, $page) {
                $builder = $this->model->orderBy('date', 'DESC')->orderBy('id', 'DESC');
                if ($limit > 0) {
                    $builder->limit($limit, ($page - 1) * $limit);
                }
                return array_map([$this, 'present'], $builder->findAll());
            }
        );

        return $this->ok($rows);
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.eventCreated', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.eventIdRequired', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.eventUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.eventNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('events', 'search', 'sync');
        return $this->ok(null, 'Api.eventDeleted');
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return $this->fail('Api.titleRequired', 422);
        }

        $images = [];
        if ($id) {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->fail('Api.eventNotFound', 404);
            }
            $images = $this->decodeJson($existing['images'] ?? null, []);
        }

        $keep = $this->existingImagesFromRequest($data);
        if ($keep !== null) {
            $images = array_values(array_filter(array_map([$this->uploads, 'toRelative'], $keep)));
        }

        $new = $this->uploads->saveMany($this->uploads->collect('images'), 'events');
        $images = array_values(array_merge($images, $new));

        $payload = [
            'title'       => $title,
            'description' => $data['description'] ?? '',
            'date'        => $data['date'] ?? '',
            'time'        => $data['time'] ?? '',
            'location'    => $data['location'] ?? '',
            'type'        => $data['type'] ?? '',
            'status'      => $data['status'] ?? '',
            'organizer'   => $data['organizer'] ?? '',
            'youtubeLink' => $data['youtubeLink'] ?? '',
            'images'      => json_encode($images),
        ];

        if ($id) {
            $this->model->update($id, $payload);
            $saved = $this->present($this->model->find($id));
            ContentCache::forget('events', 'search', 'sync');
            return $saved;
        }

        $newId = $this->model->insert($payload, true);
        $saved = $this->present($this->model->find($newId));
        ContentCache::forget('events', 'search', 'sync');
        return $saved;
    }

    protected function present(array $row): array
    {
        $images = $this->decodeJson($row['images'] ?? null, []);
        $hint = trim(($row['title'] ?? '') . ' ' . ($row['type'] ?? ''));
        $row['images'] = $this->media->resolveMany($images, $hint);
        $row['image'] = $row['images'][0] ?? '';
        $row['id'] = (int) $row['id'];
        $row['type_label'] = I18n::known('eventType', $row['type'] ?? '');
        $row['status_label'] = I18n::known('status', $row['status'] ?? '') ?: I18n::known('projectStatus', $row['status'] ?? '');
        return I18n::localizeRow($row, ['title', 'description', 'location']);
    }

    /**
     * @return list<string>|null null when the client did not send a keep-list
     */
    protected function existingImagesFromRequest(array $data): ?array
    {
        $keep = $data['existingImages'] ?? $this->request->getPost('existingImages');
        if ($keep === null) {
            $keep = $this->request->getPost('existingImages[]');
        }
        if ($keep === null) {
            return null;
        }
        if (is_string($keep)) {
            $decoded = json_decode($keep, true);
            if (is_array($decoded)) {
                return array_values($decoded);
            }
            return $keep !== '' ? [$keep] : [];
        }

        return is_array($keep) ? array_values($keep) : [];
    }
}
