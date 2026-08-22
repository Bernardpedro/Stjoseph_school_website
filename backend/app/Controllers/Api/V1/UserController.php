<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\AuthContext;
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
        $data = $this->payload();

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON request body.',
                ]);
        }

        if (empty($data['firstName']) && empty($data['name'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Name is required.',
                ]);
        }

        if (!$this->validateData($data, UserValidation::create())) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Validation failed.',
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
                    'message' => $e->getMessage(),
                ]);
        }

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'success' => true,
                'message' => 'User created successfully.',
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
                'message' => 'Users retrieved successfully',
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
                    'message' => 'User not found',
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user,
            ]);
    }

    public function delete(string $id)
    {
        try {
            $deleted = $this->userService->deleteUser($id, $this->actorId());
        } catch (\RuntimeException $e) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        }

        if (!$deleted) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'User not found',
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => 'User deleted successfully.',
                'data' => null,
            ]);
    }

    public function update(?string $id = null)
    {
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
                    'message' => 'User id is required.',
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
                    'message' => 'Validation failed',
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
                    'message' => $e->getMessage(),
                ]);
        }

        if (!$user) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'User not found',
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success' => true,
                'message' => 'User updated successfully.',
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
}
