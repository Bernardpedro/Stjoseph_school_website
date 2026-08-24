<?php

namespace App\Controllers\Api\V1;

use App\Models\AnnouncementModel;
use App\Models\EventModel;
use App\Models\ProjectModel;
use App\Models\RequirementLevelModel;
use App\Services\ContentCache;
use App\Services\I18n;

class SearchController extends BaseApiController
{
    public function index()
    {
        $q = trim((string) ($this->request->getGet('q') ?? ''));
        if (strlen($q) < 2) {
            return $this->ok([
                'query'   => $q,
                'results' => [],
                'total'   => 0,
            ]);
        }

        $payload = ContentCache::remember(
            'search',
            ContentCache::localeSuffix(md5($q)),
            function () use ($q) {
                $results = [];

                foreach ((new EventModel())->groupStart()->like('title', $q)->orLike('description', $q)->orLike('location', $q)->groupEnd()->findAll(10) as $row) {
                    $results[] = [
                        'id'          => 'event-' . $row['id'],
                        'type'        => 'event',
                        'category'    => I18n::known('category', 'events'),
                        'title'       => I18n::stored($row['title'] ?? ''),
                        'description' => I18n::stored($row['description'] ?? ''),
                        'path'        => '/events',
                    ];
                }

                foreach ((new ProjectModel())->groupStart()->like('title', $q)->orLike('description', $q)->orLike('partner', $q)->groupEnd()->findAll(10) as $row) {
                    $results[] = [
                        'id'          => 'project-' . $row['id'],
                        'type'        => 'project',
                        'category'    => I18n::known('category', 'projects'),
                        'title'       => I18n::stored($row['title'] ?? ''),
                        'description' => I18n::stored($row['description'] ?? ''),
                        'path'        => '/projects/' . $row['id'],
                    ];
                }

                foreach ((new AnnouncementModel())->groupStart()->like('title', $q)->orLike('message', $q)->groupEnd()->findAll(10) as $row) {
                    $results[] = [
                        'id'          => 'announcement-' . $row['id'],
                        'type'        => 'announcement',
                        'category'    => I18n::known('category', 'announcements'),
                        'title'       => I18n::stored($row['title'] ?? ''),
                        'description' => I18n::stored($row['message'] ?? ''),
                        'path'        => $row['link'] ?: '/admission',
                    ];
                }

                foreach ((new RequirementLevelModel())->groupStart()->like('name', $q)->orLike('description', $q)->orLike('code', $q)->groupEnd()->findAll(10) as $row) {
                    $results[] = [
                        'id'          => 'requirement-' . $row['id'],
                        'type'        => 'requirement',
                        'category'    => I18n::known('category', 'academics'),
                        'title'       => $row['name'],
                        'description' => $row['description'] ?? '',
                        'path'        => '/academics',
                    ];
                }

                return [
                    'query'   => $q,
                    'results' => $results,
                    'total'   => count($results),
                ];
            },
            ContentCache::TTL_SEARCH
        );

        return $this->ok($payload);
    }
}
