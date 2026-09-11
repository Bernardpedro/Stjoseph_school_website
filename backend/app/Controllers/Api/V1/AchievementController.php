<?php

namespace App\Controllers\Api\V1;

use App\Models\AchievementModel;
use App\Models\UserModel;
use App\Services\ContentCache;
use App\Services\JwtService;
use App\Services\UserService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AchievementController extends BaseApiController
{
    protected AchievementModel $model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new AchievementModel();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = $this->model->find($id);
            return $row ? $this->ok($this->present($row)) : $this->fail('Api.achievementNotFound', 404);
        }

        $all = $this->request->getGet('all');
        $keepHidden = $all && $this->isAdminRequest();

        $load = function () use ($keepHidden) {
            $builder = $this->model->orderBy('academic_year', 'DESC')->orderBy('id', 'ASC');
            if (!$keepHidden) {
                $builder->where('is_published', 1);
            }
            return array_map([$this, 'present'], $builder->findAll());
        };

        $rows = $keepHidden ? $load() : ContentCache::remember('achievements', ContentCache::localeSuffix('public'), $load);

        return $this->ok($rows);
    }

    public function create()
    {
        $saved = $this->persist(null);
        if ($saved instanceof ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.achievementCreated', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.achievementIdRequired', 422);
        }
        $saved = $this->persist($id);
        if ($saved instanceof ResponseInterface) {
            return $saved;
        }
        return $this->ok($saved, 'Api.achievementUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.achievementNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('achievements', 'search', 'sync');
        return $this->ok(null, 'Api.achievementDeleted');
    }

    protected function persist(?string $id)
    {
        $data = $this->body();
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return $this->fail('Api.titleRequired', 422);
        }

        if ($id && !$this->model->find($id)) {
            return $this->fail('Api.achievementNotFound', 404);
        }

        $score = $data['score'] ?? '';
        $rank = trim((string) ($data['rank'] ?? ''));

        $payload = [
            'title'         => $title,
            'category'      => trim((string) ($data['category'] ?? '')),
            'academic_year' => trim((string) ($data['academic_year'] ?? '')),
            'score'         => $score !== '' && $score !== null ? (float) $score : null,
            'rank'          => $rank !== '' ? $rank : null,
            'is_published'  => $this->truthy($data['is_published'] ?? 1) ? 1 : 0,
            'is_featured'   => $this->truthy($data['is_featured'] ?? 0) ? 1 : 0,
        ];

        if ($id) {
            $this->model->update($id, $payload);
            $saved = $this->present($this->model->find($id));
            ContentCache::forget('achievements', 'search', 'sync');
            return $saved;
        }

        $newId = $this->model->insert($payload, true);
        $saved = $this->present($this->model->find($newId));
        ContentCache::forget('achievements', 'search', 'sync');
        return $saved;
    }

    protected function truthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    protected function present(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['is_published'] = (int) ($row['is_published'] ?? 1);
        $row['is_featured'] = (int) ($row['is_featured'] ?? 0);
        $row['score'] = $row['score'] !== null ? (float) $row['score'] : null;
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
