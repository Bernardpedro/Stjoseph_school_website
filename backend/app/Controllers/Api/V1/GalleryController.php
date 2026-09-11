<?php

namespace App\Controllers\Api\V1;

use App\Models\GalleryModel;
use App\Services\ContentCache;
use App\Services\MediaUrlService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class GalleryController extends BaseApiController
{
    protected GalleryModel $model;
    protected UploadService $uploads;
    protected MediaUrlService $media;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new GalleryModel();
        $this->uploads = new UploadService();
        $this->media = new MediaUrlService();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = $this->model->find($id);
            return $row ? $this->ok($this->present($row)) : $this->fail('Api.galleryImageNotFound', 404);
        }

        $rows = ContentCache::remember('galleries', ContentCache::localeSuffix('list'), function () {
            $builder = $this->model->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            return array_map([$this, 'present'], $builder->findAll());
        });

        return $this->ok($rows);
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.galleryImageCreated', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.galleryImageIdRequired', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.galleryImageUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.galleryImageNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('galleries', 'search', 'sync');
        return $this->ok(null, 'Api.galleryImageDeleted');
    }

    protected function persist(?string $id)
    {
        $data = $this->body();

        $image = '';
        if ($id) {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->fail('Api.galleryImageNotFound', 404);
            }
            $image = (string) ($existing['image'] ?? '');
        }

        $keepImage = $data['existingImage'] ?? $this->request->getPost('existingImage');
        if ($keepImage !== null) {
            $image = (string) $this->uploads->toRelative((string) $keepImage);
        }

        $files = $this->uploads->collect('image');
        $uploaded = $files !== [] ? $this->uploads->saveOne($files[0], 'galleries') : null;
        if ($uploaded) {
            $image = $uploaded;
        }

        if ($image === '') {
            return $this->fail('Api.imageRequired', 422);
        }

        $payload = [
            'title'      => trim((string) ($data['title'] ?? '')),
            'image'      => $image,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($id) {
            $this->model->update($id, $payload);
            $saved = $this->present($this->model->find($id));
            ContentCache::forget('galleries', 'search', 'sync');
            return $saved;
        }

        $newId = $this->model->insert($payload, true);
        $saved = $this->present($this->model->find($newId));
        ContentCache::forget('galleries', 'search', 'sync');
        return $saved;
    }

    protected function present(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['sort_order'] = (int) $row['sort_order'];
        $row['image'] = $this->media->resolve($row['image'] ?? '', trim(($row['title'] ?? '') . ' gallery'));
        return $row;
    }
}
