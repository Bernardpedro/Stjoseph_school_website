<?php

namespace App\Services;

use App\Models\NotificationModel;

class NotificationService
{
    protected NotificationModel $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    public function create(string $title, string $message, ?string $link = null): void
    {
        $this->model->insert([
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'is_read' => 0,
        ]);
    }
}
