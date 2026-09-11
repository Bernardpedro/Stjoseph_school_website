<?php

namespace App\Services;

use Config\Services;

class SwiftQomSmsService
{
    protected const DEFAULT_ENDPOINT = 'https://swiftqom.io/api/dev/api/v1/send_sms';

    /**
     * @return array{ok: bool, status: string, providerMessageId: ?string, raw: string, error: ?string}
     */
    public function send(string $recipient, string $message, ?string $senderId = null): array
    {
        $apiKey = trim((string) env('SWIFTQOM_API_KEY'));
        if ($apiKey === '') {
            return [
                'ok' => false,
                'status' => 'failed',
                'providerMessageId' => null,
                'raw' => '',
                'error' => 'Api.smsNotConfigured',
            ];
        }

        $senderId = $senderId !== null && trim($senderId) !== '' ? trim($senderId) : trim((string) env('SWIFTQOM_SENDER_ID'));

        // SwiftQom expects a plain digits phone number (no leading "+"), e.g. "250784559337".
        $phone = ltrim($recipient, '+');

        $payload = [
            'phone'   => $phone,
            'message' => $message,
        ];
        if ($senderId !== '') {
            $payload['sender_id'] = $senderId;
        }

        $client = Services::curlrequest([
            'http_errors' => false,
            'timeout'     => 15,
        ]);

        $endpoint = trim((string) env('SWIFTQOM_API_URL')) ?: self::DEFAULT_ENDPOINT;

        try {
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-api-key'    => $apiKey,
                ],
                'json' => $payload,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SwiftQom SMS request failed: {error}', ['error' => $e->getMessage()]);
            return [
                'ok' => false,
                'status' => 'failed',
                'providerMessageId' => null,
                'raw' => '',
                'error' => 'Api.smsSendFailed',
            ];
        }

        $httpStatus = $response->getStatusCode();
        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);

        $ok = $httpStatus >= 200 && $httpStatus < 300
            && is_array($decoded)
            && ($decoded['message'] ?? null) === 'success';

        if (!$ok) {
            $errorMessage = is_array($decoded) ? ($decoded['message'] ?? null) : null;
            return [
                'ok' => false,
                'status' => 'failed',
                'providerMessageId' => null,
                'raw' => $body,
                'error' => $errorMessage ?: 'Api.smsSendFailed',
            ];
        }

        return [
            'ok' => true,
            'status' => 'sent',
            'providerMessageId' => isset($decoded['message_id']) ? (string) $decoded['message_id'] : null,
            'raw' => $body,
            'error' => null,
        ];
    }
}
