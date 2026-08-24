<?php

namespace App\Controllers\Api\V1;

use App\Models\AdmissionModel;
use App\Models\AdmissionRequirementModel;
use App\Services\ContentCache;
use App\Services\I18n;
use App\Services\NotificationService;
use App\Services\SettingService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AdmissionController extends BaseApiController
{
    protected AdmissionModel $model;
    protected AdmissionRequirementModel $reqModel;
    protected UploadService $uploads;
    protected SettingService $settings;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new AdmissionModel();
        $this->reqModel = new AdmissionRequirementModel();
        $this->uploads = new UploadService();
        $this->settings = new SettingService();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = $this->model->find($id);
            return $row ? $this->ok($this->present($row)) : $this->fail('Api.applicationNotFound', 404);
        }

        $builder = $this->model->orderBy('created_at', 'DESC');
        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('status', $status);
        }

        return $this->ok(array_map([$this, 'present'], $builder->findAll()));
    }

    public function create()
    {
        $data = $this->body();
        $student = trim((string) ($data['student_name'] ?? ''));
        $parent = trim((string) ($data['parent_name'] ?? ''));
        $phone = trim((string) ($data['parent_phone'] ?? ''));

        if ($student === '' || $parent === '' || $phone === '') {
            return $this->fail('Api.studentParentPhoneRequired', 422);
        }

        $bulletin = $this->uploads->saveMany($this->uploads->collect('bulletin'), 'admissions');
        $other = $this->uploads->saveMany($this->uploads->collect('documents'), 'admissions');

        $dob = $data['date_of_birth'] ?? $data['dob'] ?? null;
        if ($dob === '') {
            $dob = null;
        }

        $id = $this->model->insert([
            'student_name'    => $student,
            'date_of_birth'   => $dob,
            'gender'          => $data['gender'] ?? null,
            'level'           => $data['level'] ?? null,
            'program'         => $data['program'] ?? null,
            'previous_school' => $data['previous_school'] ?? null,
            'parent_name'     => $parent,
            'parent_phone'    => $phone,
            'parent_email'    => $data['parent_email'] ?? null,
            'province'        => $data['province'] ?? null,
            'district'        => $data['district'] ?? null,
            'address'         => $data['address'] ?? null,
            'message'         => $data['message'] ?? null,
            'documents'       => json_encode(['bulletin' => $bulletin, 'other' => $other]),
            'status'          => 'pending',
        ], true);

        (new NotificationService())->createKeyed(
            'Api.admissionNotificationTitle',
            'Api.admissionNotificationBody',
            [
                'student' => $student,
                'program' => (string) ($data['program'] ?? ''),
            ],
            '/dashboard/admin/admissions'
        );

        ContentCache::forget('sync');
        return $this->ok($this->present($this->model->find($id)), 'Api.applicationSubmitted', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.applicationIdRequired', 422);
        }

        $row = $this->model->find($id);
        if (!$row) {
            return $this->fail('Api.applicationNotFound', 404);
        }

        $data = $this->body();
        $allowed = [
            'status', 'level', 'program', 'student_name', 'date_of_birth', 'gender',
            'previous_school', 'parent_name', 'parent_phone', 'parent_email',
            'province', 'district', 'address', 'message',
        ];
        $update = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = $data[$field];
            }
        }

        if (array_key_exists('date_of_birth', $update) && $update['date_of_birth'] === '') {
            $update['date_of_birth'] = null;
        }

        if ($update === []) {
            return $this->fail('Api.noFieldsToUpdate', 422);
        }

        $this->model->update($id, $update);
        ContentCache::forget('sync');
        return $this->ok($this->present($this->model->find($id)), 'Api.applicationUpdated');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Api.applicationIdRequired', 422);
        }

        if (!$this->model->find($id)) {
            return $this->fail('Api.applicationNotFound', 404);
        }

        $this->model->delete($id);
        ContentCache::forget('sync');
        return $this->ok(null, 'Api.applicationDeleted');
    }

    public function requirements()
    {
        $items = ContentCache::remember('admissions', 'requirements_public', function () {
            return $this->reqModel
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
        });

        return $this->ok($items);
    }

    public function requirementsAll()
    {
        return $this->ok(
            $this->reqModel->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll()
        );
    }

    public function saveRequirements()
    {
        $data = $this->body();
        $items = $data['items'] ?? [];
        if (!is_array($items)) {
            return $this->fail('Api.itemsMustBeArray', 422);
        }

        $this->reqModel->truncate();
        foreach ($items as $i => $item) {
            $text = is_array($item) ? ($item['item_text'] ?? '') : (string) $item;
            $text = trim($text);
            if ($text === '') {
                continue;
            }
            $this->reqModel->insert([
                'item_text'  => $text,
                'sort_order' => (int) (is_array($item) ? ($item['sort_order'] ?? $i + 1) : $i + 1),
                'is_active'  => (int) (is_array($item) ? ($item['is_active'] ?? 1) : 1),
            ]);
        }

        ContentCache::forget('admissions', 'sync');
        return $this->ok($this->reqModel->orderBy('sort_order', 'ASC')->findAll(), 'Api.requirementsSaved');
    }

    public function settings()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = $this->body();
            $saved = $this->settings->putGroup('admissions', [
                'applications_open' => !empty($data['applications_open']) ? '1' : '0',
                'notice'            => trim((string) ($data['notice'] ?? '')),
                'fees_text'         => trim((string) ($data['fees_text'] ?? '')),
            ]);
            ContentCache::forget('admissions', 'sync');
            return $this->ok($saved, 'Api.admissionSettingsSaved');
        }

        return $this->ok(ContentCache::remember('admissions', 'settings', function () {
            $saved = $this->settings->getGroup('admissions');
            return [
                'applications_open' => ($saved['applications_open'] ?? '1') !== '0',
                'notice'            => $saved['notice'] ?? '',
                'fees_text'         => $saved['fees_text'] ?? '',
            ];
        }));
    }

    protected function present(array $row): array
    {
        $row['documents'] = $this->decodeJson($row['documents'] ?? null, ['bulletin' => [], 'other' => []]);
        $row['id'] = (int) $row['id'];
        $row['status_label'] = I18n::known('status', $row['status'] ?? '');
        $row['gender_label'] = I18n::known('gender', $row['gender'] ?? '');
        $row['level_label'] = I18n::known('level', $row['level'] ?? '');
        $row['program_label'] = I18n::known('program', $row['program'] ?? '');
        return $row;
    }
}
