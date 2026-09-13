<?php

namespace App\Services;

use Config\Services;

class MistaSmsService
{
    protected const DEFAULT_ENDPOINT = 'https://api.mista.io/sms';

    /**
     * @return array{ok: bool, status: string, providerMessageId: ?string, raw: string, error: ?string}
     */
    public function send(string $recipient, string $message, ?string $senderId = null): array
    {
        $apiKey = trim((string) env('MISTA_API_KEY'));
        if ($apiKey === '') {
            return [
                'ok' => false,
                'status' => 'failed',
                'providerMessageId' => null,
                'raw' => '',
                'error' => 'Api.smsNotConfigured',
            ];
        }

        $senderId = $senderId !== null && trim($senderId) !== '' ? trim($senderId) : trim((string) env('MISTA_SENDER_ID'));

        // Mista expects a plain digits phone number (no leading "+"), e.g. "250784559337".
        $phone = ltrim($recipient, '+');

        $payload = [
            'recipient' => $phone,
            'type'      => 'plain',
            'message'   => $message,
        ];
        if ($senderId !== '') {
            $payload['sender_id'] = $senderId;
        }

        $client = Services::curlrequest([
            'http_errors' => false,
            'timeout'     => 15,
        ]);

        $endpoint = trim((string) env('MISTA_API_URL')) ?: self::DEFAULT_ENDPOINT;

        try {
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => $payload,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Mista SMS request failed: {error}', ['error' => $e->getMessage()]);
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
            && ($decoded['error'] ?? null) === null
            && !in_array(strtolower((string) ($decoded['status'] ?? '')), ['error', 'failed'], true);

        if (!$ok) {
            $errorMessage = is_array($decoded) ? ($decoded['error'] ?? $decoded['message'] ?? null) : null;
            return [
                'ok' => false,
                'status' => 'failed',
                'providerMessageId' => null,
                'raw' => $body,
                'error' => $errorMessage ?: 'Api.smsSendFailed',
            ];
        }

        $providerMessageId = $decoded['message_id']
            ?? $decoded['campaign_id']
            ?? $decoded['id']
            ?? null;

        return [
            'ok' => true,
            'status' => 'sent',
            'providerMessageId' => $providerMessageId !== null ? (string) $providerMessageId : null,
            'raw' => $body,
            'error' => null,
        ];
    }
}
