<?php

namespace App\Controllers\Api\V1;

use App\Models\NotificationModel;
use App\Services\NotificationService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class NotificationController extends BaseApiController
{
    protected NotificationModel $model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new NotificationModel();
    }

    public function index()
    {
        $limit = max(1, min(50, (int) ($this->request->getGet('limit') ?? 20)));
        $items = $this->model->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->findAll($limit);
        $unread = (new NotificationModel())->where('is_read', 0)->countAllResults();

        $items = array_map([NotificationService::class, 'present'], $items);

        return $this->ok([
            'items'        => $items,
            'unread_count' => $unread,
        ]);
    }

    public function read()
    {
        $data = $this->body();
        if (!empty($data['all'])) {
            $this->model->where('is_read', 0)->set(['is_read' => 1])->update();
            return $this->ok(null, 'Api.notificationsAllRead');
        }

        $id = $data['id'] ?? $this->id();
        if (!$id) {
            return $this->fail('Api.notificationIdRequired', 422);
        }

        $this->model->update($id, ['is_read' => 1]);
        return $this->ok(null, 'Api.notificationRead');
    }
}
