<?php

namespace App\Controllers\Api\V1;

use App\Models\TestimonialModel;
use App\Models\UserModel;
use App\Services\ContentCache;
use App\Services\JwtService;
use App\Services\MediaUrlService;
use App\Services\UploadService;
use App\Services\UserService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TestimonialController extends BaseApiController
{
    protected TestimonialModel $model;
    protected UploadService $uploads;
    protected MediaUrlService $media;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new TestimonialModel();
        $this->uploads = new UploadService();
        $this->media = new MediaUrlService();
    }

    public function index()
    {
        $all = $this->request->getGet('all');
        $keepPending = $all && $this->isAdminRequest();

        $load = function () use ($keepPending) {
            $builder = $this->model->orderBy('created_at', 'DESC');
            if (!$keepPending) {
                $builder->where('status', 'approved');
            }
            return array_map([$this, 'present'], $builder->findAll());
        };

        $rows = $keepPending ? $load() : ContentCache::remember('testimonials', ContentCache::localeSuffix('public'), $load);

        return $this->ok($rows);
    }

    public function create()
    {
        $data = $this->body();
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return $this->fail('Api.nameRequired', 422);
        }

        $rating = $data['rating'] ?? null;
        $rating = $rating !== null && $rating !== '' ? max(1, min(5, (int) $rating)) : null;

        $files = $this->uploads->collect('photo');
        $photo = $files !== [] ? $this->uploads->saveOne($files[0], 'testimonials') : null;

        $payload = [
            'name'    => $name,
            'email'   => trim((string) ($data['email'] ?? '')) ?: null,
            'role'    => trim((string) ($data['role'] ?? '')),
            'message' => trim((string) ($data['message'] ?? '')),
            'photo'   => $photo ?: '',
            'rating'  => $rating,
            'status'  => 'pending',
        ];

        $id = $this->model->insert($payload, true);
        ContentCache::forget('testimonials', 'search', 'sync');

        return $this->ok($this->present($this->model->find($id)), 'Api.testimonialCreated', 201);
    }

    public function approve()
    {
        return $this->setStatus('approved', 'Api.testimonialApproved');
    }

    public function reject()
    {
        return $this->setStatus('pending', 'Api.testimonialRejected');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.testimonialNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('testimonials', 'search', 'sync');
        return $this->ok(null, 'Api.testimonialDeleted');
    }

    protected function setStatus(string $status, string $message)
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.testimonialIdRequired', 422);
        }
        if (!$this->model->find($id)) {
            return $this->fail('Api.testimonialNotFound', 404);
        }

        $this->model->update($id, ['status' => $status]);
        ContentCache::forget('testimonials', 'search', 'sync');

        return $this->ok($this->present($this->model->find($id)), $message);
    }

    protected function present(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['rating'] = $row['rating'] !== null ? (int) $row['rating'] : null;
        $row['photo'] = $row['photo'] ? $this->media->resolve($row['photo'], 'testimonial') : '';
        return $row;
    }

    protected function isAdminRequest(): bool
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return false;
        }

        try {
            $payload = (new JwtService())->verifyAccessToken(substr($header, 7));
            $role = $payload->role ?? null;
            if (UserService::isStaffRole($role)) {
                return true;
            }
            if (!empty($payload->sub)) {
                $user = (new UserModel())->find($payload->sub);
                return UserService::isStaffRole($user['role'] ?? null);
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }
}
