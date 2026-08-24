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
        ContentCache::forget('sync');
    }

    /**
     * @param array<string, string> $params
     */
    public function createKeyed(string $titleKey, string $messageKey, array $params = [], ?string $link = null): void
    {
        $this->create(
            'i18n:' . $titleKey,
            json_encode(['key' => $messageKey, 'params' => $params], JSON_UNESCAPED_UNICODE),
            $link
        );
    }

    public static function present(array $row): array
    {
        $row['id'] = (int) ($row['id'] ?? 0);
        $row['is_read'] = (int) ($row['is_read'] ?? 0);

        $title = (string) ($row['title'] ?? '');
        if (str_starts_with($title, 'i18n:')) {
            $row['title'] = I18n::content(substr($title, 5));
        } elseif ($title === 'New admission application') {
            $row['title'] = I18n::content('Api.admissionNotificationTitle');
        }

        $message = (string) ($row['message'] ?? '');
        $decoded = json_decode($message, true);
        if (is_array($decoded) && !empty($decoded['key'])) {
            $params = is_array($decoded['params'] ?? null) ? $decoded['params'] : [];
            $program = I18n::known('program', (string) ($params['program'] ?? ''));
            $params['forProgram'] = $program !== ''
                ? I18n::content('Api.forProgram', ['program' => $program])
                : '';
            $row['message'] = I18n::content((string) $decoded['key'], $params);
        }

        return $row;
    }
}
