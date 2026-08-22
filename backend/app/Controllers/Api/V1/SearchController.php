<?php

namespace App\Controllers\Api\V1;

use App\Models\AnnouncementModel;
use App\Models\EventModel;
use App\Models\ProjectModel;
use App\Models\RequirementLevelModel;

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

        $results = [];

        foreach ((new EventModel())->groupStart()->like('title', $q)->orLike('description', $q)->orLike('location', $q)->groupEnd()->findAll(10) as $row) {
            $results[] = [
                'id'          => 'event-' . $row['id'],
                'type'        => 'event',
                'category'    => 'Events',
                'title'       => $row['title'],
                'description' => $row['description'] ?? '',
                'path'        => '/events',
            ];
        }

        foreach ((new ProjectModel())->groupStart()->like('title', $q)->orLike('description', $q)->orLike('partner', $q)->groupEnd()->findAll(10) as $row) {
            $results[] = [
                'id'          => 'project-' . $row['id'],
                'type'        => 'project',
                'category'    => 'Projects',
                'title'       => $row['title'],
                'description' => $row['description'] ?? '',
                'path'        => '/projects/' . $row['id'],
            ];
        }

        foreach ((new AnnouncementModel())->groupStart()->like('title', $q)->orLike('message', $q)->groupEnd()->findAll(10) as $row) {
            $results[] = [
                'id'          => 'announcement-' . $row['id'],
                'type'        => 'announcement',
                'category'    => 'Announcements',
                'title'       => $row['title'],
                'description' => $row['message'] ?? '',
                'path'        => $row['link'] ?: '/admission',
            ];
        }

        foreach ((new RequirementLevelModel())->groupStart()->like('name', $q)->orLike('description', $q)->orLike('code', $q)->groupEnd()->findAll(10) as $row) {
            $results[] = [
                'id'          => 'requirement-' . $row['id'],
                'type'        => 'requirement',
                'category'    => 'Academics',
                'title'       => $row['name'],
                'description' => $row['description'] ?? '',
                'path'        => '/academics',
            ];
        }

        return $this->ok([
            'query'   => $q,
            'results' => $results,
            'total'   => count($results),
        ]);
    }
}
