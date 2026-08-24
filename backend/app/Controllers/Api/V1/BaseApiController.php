<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\I18n;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseApiController extends BaseController
{
    protected function ok(mixed $data = null, string $message = 'Api.ok', int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => I18n::line($message),
            'data' => $data,
        ]);
    }

    protected function fail(string $message, int $code = 400, mixed $errors = null): ResponseInterface
    {
        $payload = [
            'success' => false,
            'message' => I18n::line($message),
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

        $contentType = (string) $request->getHeaderLine('Content-Type');
        $isForm = stripos($contentType, 'multipart/form-data') !== false
            || stripos($contentType, 'application/x-www-form-urlencoded') !== false;

        if (!$isForm) {
            try {
                $json = $request->getJSON(true);
                if (is_array($json) && $json !== []) {
                    return $json;
                }
            } catch (\Throwable $e) {
                // Form uploads and empty bodies are not JSON.
            }
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
