<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseApiController extends BaseController
{
    protected function ok(mixed $data = null, string $message = 'OK', int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function fail(string $message, int $code = 400, mixed $errors = null): ResponseInterface
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return $this->response->setStatusCode($code)->setJSON($payload);
    }

    protected function body(): array
    {
        $request = $this->request;
        if (!$request instanceof IncomingRequest) {
            return [];
        }

        $json = $request->getJSON(true);
        if (is_array($json) && $json !== []) {
            return $json;
        }

        $post = $request->getPost();
        return is_array($post) ? $post : [];
    }

    protected function id(): ?string
    {
        $request = $this->request;
        if (!$request instanceof IncomingRequest) {
            return null;
        }

        $id = $request->getGet('id') ?? $request->getPost('id');
        return $id !== null && $id !== '' ? (string) $id : null;
    }

    /**
     * @param array<int|string, mixed> $default
     * @return array<int|string, mixed>
     */
    protected function decodeJson(mixed $value, array $default = []): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return $default;
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : $default;
    }
}
