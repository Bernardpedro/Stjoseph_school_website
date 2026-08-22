<?php

namespace App\Controllers\Api\V1;

use App\Models\AdmissionModel;
use App\Models\AdmissionRequirementModel;
use App\Services\NotificationService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AdmissionController extends BaseApiController
{
    protected AdmissionModel $model;
    protected AdmissionRequirementModel $reqModel;
    protected UploadService $uploads;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new AdmissionModel();
        $this->reqModel = new AdmissionRequirementModel();
        $this->uploads = new UploadService();
    }

    public function index()
    {
        $id = $this->id();
        if ($id) {
            $row = $this->model->find($id);
            return $row ? $this->ok($this->present($row)) : $this->fail('Application not found', 404);
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
            return $this->fail('Student name, parent name and phone are required.', 422);
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

        (new NotificationService())->create(
            'New admission application',
            $student . ' applied' . (!empty($data['program']) ? ' for ' . $data['program'] : '') . '.',
            '/dashboard/admin/admissions'
        );

        return $this->ok($this->present($this->model->find($id)), 'Application submitted successfully.', 201);
    }

    public function update()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Application id is required.', 422);
        }

        $row = $this->model->find($id);
        if (!$row) {
            return $this->fail('Application not found', 404);
        }

        $data = $this->body();
        $allowed = ['status', 'level', 'program', 'student_name', 'parent_name', 'parent_phone', 'parent_email', 'province', 'district', 'address'];
        $update = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = $data[$field];
            }
        }

        if ($update === []) {
            return $this->fail('No fields to update.', 422);
        }

        $this->model->update($id, $update);
        return $this->ok($this->present($this->model->find($id)), 'Application updated.');
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id) {
            return $this->fail('Application id is required.', 422);
        }

        if (!$this->model->find($id)) {
            return $this->fail('Application not found', 404);
        }

        $this->model->delete($id);
        return $this->ok(null, 'Application deleted.');
    }

    public function requirements()
    {
        $items = $this->reqModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

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
            return $this->fail('Items must be an array.', 422);
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

        return $this->ok($this->reqModel->orderBy('sort_order', 'ASC')->findAll(), 'Requirements list saved.');
    }

    protected function present(array $row): array
    {
        $row['documents'] = $this->decodeJson($row['documents'] ?? null, ['bulletin' => [], 'other' => []]);
        $row['id'] = (int) $row['id'];
        return $row;
    }
}
