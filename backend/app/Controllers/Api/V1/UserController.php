<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\AuthContext;
use App\Services\I18n;
use App\Services\UserService;
use App\Validation\UserValidation;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class UserController extends BaseController
{
    protected UserService $userService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->userService = new UserService();
    }

    public function create(): ResponseInterface
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $data = $this->payload();

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.invalidJson'),
                ]);
        }

        if (empty($data['firstName']) && empty($data['name'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.nameRequired'),
                ]);
        }

        if (!$this->validateData($data, UserValidation::create())) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.validationFailed'),
                    'errors' => $this->validator->getErrors(),
                ]);
        }

        try {
            $user = $this->userService->createUser($data);
        } catch (\RuntimeException $e) {
            $code = str_contains(strtolower($e->getMessage()), 'already') ? 409 : 422;
            return $this->response
                ->setStatusCode($code)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line($e->getMessage()),
                ]);
        }

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'success' => true,
                'message' => I18n::line('Api.userCreated'),
                'data' => $user,
            ]);
    }

    public function index()
    {
        $users = $this->userService->getUsers();

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => I18n::line('Api.usersRetrieved'),
                'data' => $users,
            ]);
    }

    public function show(string $id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.userNotFound'),
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => I18n::line('Api.userRetrieved'),
                'data' => $user,
            ]);
    }

    public function delete(string $id)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        try {
            $deleted = $this->userService->deleteUser($id, $this->actorId());
        } catch (\RuntimeException $e) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line($e->getMessage()),
                ]);
        }

        if (!$deleted) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.userNotFound'),
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => I18n::line('Api.userDeleted'),
                'data' => null,
            ]);
    }

    public function update(?string $id = null)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $id = $id ?: '';
        if ($id === '') {
            $request = $this->request;
            if ($request instanceof IncomingRequest) {
                $id = (string) ($request->getGet('id') ?? $request->getPost('id') ?? '');
            }
        }
        $data = $this->payload();

        if ($id === '') {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.userIdRequired'),
                ]);
        }

        if (!is_array($data)) {
            $data = [];
        }

        if (!$this->validateData($data, UserValidation::update())) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.validationFailed'),
                    'errors' => $this->validator->getErrors(),
                ]);
        }

        try {
            $user = $this->userService->updateUser($id, $data, $this->actorId());
        } catch (\RuntimeException $e) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line($e->getMessage()),
                ]);
        }

        if (!$user) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => I18n::line('Api.userNotFound'),
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => I18n::line('Api.userUpdated'),
                'data' => $user,
            ]);
    }

    protected function payload(): ?array
    {
        $request = $this->request;
        if (!$request instanceof IncomingRequest) {
            return null;
        }

        try {
            $json = $request->getJSON(true);
        } catch (\Throwable $e) {
            $json = null;
        }
        if (is_array($json) && $json !== []) {
            return $json;
        }

        $post = $request->getPost();
        return is_array($post) ? $post : $json;
    }

    protected function actorId(): ?string
    {
        return AuthContext::id();
    }

    protected function denyUnlessAdmin(): ?ResponseInterface
    {
        $actorId = $this->actorId();
        $actor = $actorId ? $this->userService->getUserById($actorId) : null;

        if ($actor && UserService::isStaffRole($actor['role'] ?? null)) {
            return null;
        }

        return $this->response
            ->setStatusCode(403)
            ->setJSON([
                'success' => false,
                'message' => I18n::line('Api.userManagementForbidden'),
            ]);
    }
}
