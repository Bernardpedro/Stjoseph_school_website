<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Validation\AuthValidation;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authService = new AuthService();
    }

    public function register()
    {
        $request = $this->request;
        $data = $request instanceof IncomingRequest ? $request->getJSON(true) : null;
        if (!is_array($data) || $data === []) {
            $data = $request instanceof IncomingRequest ? $request->getPost() : null;
        }

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid request body.',
                ]);
        }

        try {
            $user = $this->authService->register($data);
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
                'message' => 'Registration successful',
                'data' => $user,
            ]);
    }

    public function login()
    {
        try {
            $request = $this->request;
            $data = $request instanceof IncomingRequest ? $request->getJSON(true) : null;
        } catch (\Throwable $e) {
            $data = null;
        }

        if (!is_array($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON request body.',
                ]);
        }

        if (!$this->validateData($data, AuthValidation::login())) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $this->validator->getErrors(),
                ]);
        }

        try {
            $result = $this->authService->login(
                $data['email'],
                $data['password']
            );

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'success' => true,
                    'message' => 'Login successful.',
                    'data' => $result,
                ]);
        } catch (\RuntimeException $e) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        }
    }
}
