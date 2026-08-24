<?php

namespace App\Controllers\Api\V1;

use App\Models\AnnouncementModel;
use App\Models\UserModel;
use App\Services\ContentCache;
use App\Services\I18n;
use App\Services\JwtService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AnnouncementController extends BaseApiController
{
    protected AnnouncementModel $model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new AnnouncementModel();
    }

    public function index()
    {
        $all = $this->request->getGet('all');
        $keepOriginal = $all && $this->isAdminRequest();
        $load = function () use ($all, $keepOriginal) {
            $builder = $this->model->orderBy('sort_order', 'ASC')->orderBy('id', 'DESC');
            if (!$all || !$this->isAdminRequest()) {
                $builder->where('is_active', 1);
            }

            return array_map(static function ($row) use ($keepOriginal) {
                $row['id'] = (int) $row['id'];
                $row['is_active'] = (int) $row['is_active'];
                $row = I18n::localizeRow($row, ['title', 'message', 'cta_text']);
                if (!$keepOriginal) {
                    $row['title'] = $row['title_i18n'] !== '' ? $row['title_i18n'] : $row['title'];
                    $row['message'] = $row['message_i18n'] !== '' ? $row['message_i18n'] : $row['message'];
                    $cta = trim((string) ($row['cta_text'] ?? ''));
                    if ($cta === '' || strcasecmp($cta, 'Apply Now') === 0) {
                        $row['cta_text'] = I18n::content('Content.applyNow');
                    } elseif ($row['cta_text_i18n'] !== '') {
                        $row['cta_text'] = $row['cta_text_i18n'];
                    }
                }
                return $row;
            }, $builder->findAll());
        };

        $rows = $keepOriginal
            ? $load()
            : ContentCache::remember('announcements', ContentCache::localeSuffix('public'), $load);

        return $this->ok($rows);
    }

    public function create()
    {
        $payload = $this->payload();
        if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $payload;
        }
        $id = $this->model->insert($payload, true);
        ContentCache::forget('announcements', 'search', 'sync');
        return $this->ok($this->model->find($id), 'Api.announcementCreated', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.announcementNotFound', 404);
        }
        $payload = $this->payload();
        if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $payload;
        }
        $this->model->update($id, $payload);
        ContentCache::forget('announcements', 'search', 'sync');
        return $this->ok($this->model->find($id), 'Api.announcementUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.announcementNotFound', 404);
        }
        $this->model->delete($id);
        ContentCache::forget('announcements', 'search', 'sync');
        return $this->ok(null, 'Api.announcementDeleted');
    }

    protected function isAdminRequest(): bool
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return false;
        }

        try {
            $payload = (new JwtService())->verifyAccessToken(substr($header, 7));
            if (($payload->role ?? null) === 'admin') {
                return true;
            }
            if (!empty($payload->sub)) {
                $user = (new UserModel())->find($payload->sub);
                return ($user['role'] ?? null) === 'admin';
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    protected function payload()
    {
        $data = $this->body();
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return $this->fail('Api.titleRequired', 422);
        }

        return [
            'title'      => $title,
            'message'    => $data['message'] ?? '',
            'cta_text'   => $data['cta_text'] ?? 'Apply Now',
            'link'       => $data['link'] ?? '/admission',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active'  => (int) ($data['is_active'] ?? 1),
        ];
    }
}
