<?php

namespace App\Controllers\Api\V1;

use App\Models\EventModel;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class EventController extends BaseApiController
{
    protected EventModel $model;
    protected UploadService $uploads;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new EventModel();
        $this->uploads = new UploadService();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = $this->model->find($id);
            return $row ? $this->ok($this->present($row)) : $this->fail('Event not found', 404);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 0);
        $builder = $this->model->orderBy('date', 'DESC')->orderBy('id', 'DESC');
        if ($limit > 0) {
            $builder->limit($limit, max(0, ((int) ($this->request->getGet('page') ?? 1) - 1) * $limit));
        }

        return $this->ok(array_map([$this, 'present'], $builder->findAll()));
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Event created successfully!', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Event id is required.', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Event updated.');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Event not found', 404);
        }
        $this->model->delete($id);
        return $this->ok(null, 'Event deleted');
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return $this->fail('Title is required.', 422);
        }

        $images = [];
        if ($id) {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->fail('Event not found', 404);
            }
            $images = $this->decodeJson($existing['images'] ?? null, []);
        }

        $keep = $this->request->getPost('existingImages');
        if ($keep === null) {
            $keep = $this->request->getPost('existingImages[]');
        }
        if (is_array($keep)) {
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
            return $this->present($this->model->find($id));
        }

        $newId = $this->model->insert($payload, true);
        return $this->present($this->model->find($newId));
    }

    protected function present(array $row): array
    {
        $images = $this->decodeJson($row['images'] ?? null, []);
        $row['images'] = $images;
        $row['image'] = $images[0] ?? '';
        $row['id'] = (int) $row['id'];
        return $row;
    }
}
