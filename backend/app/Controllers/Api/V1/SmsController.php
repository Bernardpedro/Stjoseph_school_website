<?php

namespace App\Controllers\Api\V1;

use App\Models\SmsMessageModel;
use App\Services\AuthContext;
use App\Services\I18n;
use App\Services\MistaSmsService;
use App\Services\SwiftQomSmsService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;

class SmsController extends BaseApiController
{
    protected const IMPORT_MAX_ROWS = 500;
    protected const IMPORT_ALLOWED_EXTENSIONS = ['xlsx', 'xls', 'csv'];
    protected const IMPORT_PHONE_HEADERS = ['phone', 'phone number', 'number', 'recipient', 'msisdn'];
    protected const IMPORT_MESSAGE_HEADERS = ['message', 'text', 'sms', 'sms message'];

    protected SmsMessageModel $model;
    protected MistaSmsService|SwiftQomSmsService $sms;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->model = new SmsMessageModel();

        $provider = strtolower(trim((string) env('SMS_PROVIDER', 'swiftqom')));
        $this->sms = $provider === 'mista' ? new MistaSmsService() : new SwiftQomSmsService();
    }

    public function index()
    {
        $builder = $this->model->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');

        $status = trim((string) ($this->request->getGet('status') ?? ''));
        if ($status !== '') {
            $builder->where('status', $status);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $limit = $limit > 0 ? min($limit, 200) : 50;

        return $this->ok(array_map([$this, 'present'], $builder->findAll($limit)));
    }

    public function create()
    {
        $data = $this->body();

        $recipients = $this->normalizeRecipients($data);
        $message = trim((string) ($data['message'] ?? ''));
        $senderId = trim((string) ($data['sender_id'] ?? ''));

        if ($recipients === []) {
            return $this->fail('Api.smsRecipientRequired', 422);
        }
        if ($message === '') {
            return $this->fail('Api.smsMessageRequired', 422);
        }
        if (mb_strlen($message) > 918) {
            // 6 GSM-7 segments' worth — generous ceiling to stop obvious pastes-gone-wrong.
            return $this->fail('Api.smsMessageTooLong', 422);
        }

        $entries = array_map(
            static fn (string $recipient) => ['recipient' => $recipient, 'message' => $message],
            $recipients
        );

        [$results, $sentCount] = $this->sendBatch($entries, $senderId !== '' ? $senderId : null);
        $totalCount = count($entries);

        if ($sentCount === 0) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => $totalCount > 1
                    ? I18n::line('Api.smsSendFailedBulk', ['total' => $totalCount])
                    : I18n::line($results[0]['error_message'] ?? 'Api.smsSendFailed'),
                'data'    => $totalCount > 1 ? $results : $results[0],
            ]);
        }

        if ($totalCount === 1) {
            return $this->ok($results[0], 'Api.smsSent', 201);
        }

        $statusCode = $sentCount === $totalCount ? 201 : 207;

        return $this->response->setStatusCode($statusCode)->setJSON([
            'success' => true,
            'message' => I18n::line('Api.smsSentBulk', ['sent' => $sentCount, 'total' => $totalCount]),
            'data'    => $results,
        ]);
    }

    public function import()
    {
        $file = $this->request instanceof \CodeIgniter\HTTP\IncomingRequest ? $this->request->getFile('file') : null;

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->fail('Api.smsImportFileRequired', 422);
        }

        $ext = strtolower($file->getClientExtension() ?: $file->guessExtension() ?: '');
        if (!in_array($ext, self::IMPORT_ALLOWED_EXTENSIONS, true)) {
            return $this->fail('Api.smsImportInvalidFormat', 422);
        }

        $mode = strtolower(trim((string) $this->request->getPost('mode'))) === 'different' ? 'different' : 'same';
        $sharedMessage = trim((string) $this->request->getPost('message'));
        $senderId = trim((string) $this->request->getPost('sender_id'));

        if ($mode === 'same' && $sharedMessage === '') {
            return $this->fail('Api.smsMessageRequired', 422);
        }
        if ($mode === 'same' && mb_strlen($sharedMessage) > 918) {
            return $this->fail('Api.smsMessageTooLong', 422);
        }

        try {
            $readerType = match ($ext) {
                'csv' => 'Csv',
                'xls' => 'Xls',
                default => 'Xlsx',
            };
            $reader = IOFactory::createReader($readerType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getTempName());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            log_message('error', 'SMS import: could not read spreadsheet: {error}', ['error' => $e->getMessage()]);
            return $this->fail('Api.smsImportUnreadable', 422);
        }

        if (count($rows) < 2) {
            return $this->fail('Api.smsImportEmpty', 422);
        }

        $header = array_map(static fn ($h) => strtolower(trim((string) $h)), $rows[0]);
        $phoneCol = null;
        $messageCol = null;
        foreach ($header as $i => $label) {
            if ($phoneCol === null && in_array($label, self::IMPORT_PHONE_HEADERS, true)) {
                $phoneCol = $i;
            }
            if ($messageCol === null && in_array($label, self::IMPORT_MESSAGE_HEADERS, true)) {
                $messageCol = $i;
            }
        }

        if ($phoneCol === null) {
            return $this->fail('Api.smsImportMissingPhoneColumn', 422);
        }
        if ($mode === 'different' && $messageCol === null) {
            return $this->fail('Api.smsImportMissingMessageColumn', 422);
        }

        $entries = [];
        for ($i = 1, $rowCount = count($rows); $i < $rowCount; $i++) {
            $row = $rows[$i];
            $recipient = $this->normalizeRecipient((string) ($row[$phoneCol] ?? ''));
            if ($recipient === '') {
                continue;
            }

            $rowMessage = $mode === 'same' ? $sharedMessage : trim((string) ($row[$messageCol] ?? ''));
            if ($rowMessage === '' || mb_strlen($rowMessage) > 918) {
                continue;
            }

            $entries[] = ['recipient' => $recipient, 'message' => $rowMessage];

            if (count($entries) >= self::IMPORT_MAX_ROWS) {
                break;
            }
        }

        if ($entries === []) {
            return $this->fail('Api.smsImportNoValidRows', 422);
        }

        [$results, $sentCount] = $this->sendBatch($entries, $senderId !== '' ? $senderId : null);
        $totalCount = count($entries);
        $statusCode = $sentCount === 0 ? 502 : ($sentCount === $totalCount ? 201 : 207);

        return $this->response->setStatusCode($statusCode)->setJSON([
            'success' => $sentCount > 0,
            'message' => $sentCount === 0
                ? I18n::line('Api.smsSendFailedBulk', ['total' => $totalCount])
                : I18n::line('Api.smsSentBulk', ['sent' => $sentCount, 'total' => $totalCount]),
            'data'    => $results,
        ]);
    }

    public function delete()
    {
        $id = $this->id();
        if (!$id || !$this->model->find($id)) {
            return $this->fail('Api.smsNotFound', 404);
        }
        $this->model->delete($id);
        return $this->ok(null, 'Api.smsDeleted');
    }

    /**
     * Sends each {recipient, message} entry, recording one sms_messages row per send.
     *
     * @param list<array{recipient: string, message: string}> $entries
     * @return array{0: list<array<string, mixed>>, 1: int} [presented rows, count sent successfully]
     */
    protected function sendBatch(array $entries, ?string $senderId): array
    {
        $results = [];
        $sentCount = 0;

        foreach ($entries as $entry) {
            $result = $this->sms->send($entry['recipient'], $entry['message'], $senderId);

            $row = [
                'recipient'           => $entry['recipient'],
                'sender_id'           => $senderId,
                'message'             => $entry['message'],
                'status'              => $result['status'],
                'provider_message_id' => $result['providerMessageId'],
                'provider_response'   => $result['raw'] !== '' ? $result['raw'] : null,
                'error_message'       => $result['ok'] ? null : $result['error'],
                'sent_by'             => AuthContext::id(),
            ];

            $id = $this->model->insert($row, true);
            $results[] = $this->present($this->model->find($id));

            if ($result['ok']) {
                $sentCount++;
            }
        }

        return [$results, $sentCount];
    }

    /**
     * @param array<string, mixed> $data
     * @return string[]
     */
    protected function normalizeRecipients(array $data): array
    {
        $raw = $data['recipients'] ?? $data['recipient'] ?? '';

        $parts = is_array($raw)
            ? $raw
            : (preg_split('/[,;\n\r]+/', (string) $raw) ?: []);

        $recipients = [];
        foreach ($parts as $part) {
            $normalized = $this->normalizeRecipient((string) $part);
            if ($normalized !== '' && !in_array($normalized, $recipients, true)) {
                $recipients[] = $normalized;
            }
        }

        return $recipients;
    }

    protected function normalizeRecipient(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        $digits = preg_replace('/[^0-9+]/', '', $raw) ?? '';
        return $digits;
    }

    protected function present(array $row): array
    {
        $row['id'] = (int) $row['id'];
        unset($row['provider_response']);
        return $row;
    }
}
