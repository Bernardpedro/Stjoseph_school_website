<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class AdmissionPdfService
{
    public function renderOne(array $application): string
    {
        return $this->render([$application], false);
    }

    /**
     * @param list<array<string, mixed>> $applications
     */
    public function renderMany(array $applications): string
    {
        return $this->render($applications, true);
    }

    /**
     * @param list<array<string, mixed>> $applications
     */
    protected function render(array $applications, bool $includeIndex): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->html($applications, $includeIndex));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output() ?: '';
    }

    /**
     * @param list<array<string, mixed>> $applications
     */
    protected function html(array $applications, bool $includeIndex): string
    {
        $pages = [];
        if ($includeIndex && count($applications) > 1) {
            $pages[] = $this->indexPage($applications);
        }

        $last = count($applications) - 1;
        foreach ($applications as $i => $app) {
            $pages[] = $this->applicationPage($app, $i !== $last);
        }

        return '<!DOCTYPE html><html><head><meta charset="UTF-8">'
            . $this->styles()
            . '</head><body>'
            . implode('', $pages)
            . '</body></html>';
    }

    /**
     * @param list<array<string, mixed>> $applications
     */
    protected function indexPage(array $applications): string
    {
        $rows = '';
        foreach ($applications as $i => $app) {
            $rows .= '<tr>'
                . '<td>' . ($i + 1) . '</td>'
                . '<td>' . $this->e($app['registration_number'] ?? '') . '</td>'
                . '<td>' . $this->e($app['student_name'] ?? '') . '</td>'
                . '<td>' . $this->e($app['parent_name'] ?? '') . '</td>'
                . '<td>' . $this->e($app['parent_phone'] ?? '') . '</td>'
                . '<td>' . $this->e($app['status_label'] ?? $app['status'] ?? '') . '</td>'
                . '</tr>';
        }

        return '<div class="page page-break">'
            . $this->header('All admission applications')
            . '<p class="meta">Generated ' . $this->e(date('Y-m-d H:i')) . ' — ' . count($applications) . ' applicant(s)</p>'
            . '<table class="list"><thead><tr>'
            . '<th>#</th><th>Registration</th><th>Student</th><th>Parent</th><th>Phone</th><th>Status</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table>'
            . '</div>';
    }

    /**
     * @param array<string, mixed> $app
     */
    protected function applicationPage(array $app, bool $breakAfter): string
    {
        $address = trim((string) ($app['address'] ?? ''));
        if ($address === '') {
            $address = implode(', ', array_filter([
                (string) ($app['district'] ?? ''),
                (string) ($app['province'] ?? ''),
            ]));
        }

        $rows = [
            'Registration number' => $app['registration_number'] ?? '',
            'Status'              => $app['status_label'] ?? $app['status'] ?? '',
            'Student name'        => $app['student_name'] ?? '',
            'Date of birth'       => $app['date_of_birth'] ?? '',
            'Gender'              => $app['gender_label'] ?? $app['gender'] ?? '',
            'Level'               => $app['level_label'] ?? $app['level'] ?? '',
            'Program'             => $app['program_label'] ?? $app['program'] ?? '',
            'Previous school'     => $app['previous_school'] ?? '',
            'Parent / guardian'   => $app['parent_name'] ?? '',
            'Phone'               => $app['parent_phone'] ?? '',
            'Email'               => $app['parent_email'] ?? '',
            'Address'             => $address,
            'Message'             => $app['message'] ?? '',
            'Submitted'           => $this->formatDate($app['created_at'] ?? ''),
        ];

        $body = '';
        foreach ($rows as $label => $value) {
            $body .= '<tr><th>' . $this->e($label) . '</th><td>' . $this->e($value !== '' ? (string) $value : '—') . '</td></tr>';
        }

        $break = $breakAfter ? ' page-break' : '';

        return '<div class="page' . $break . '">'
            . $this->header('Admission application')
            . '<table class="details">' . $body . '</table>'
            . '<div class="sign">'
            . '<div><p>Parent / guardian signature</p><div class="line"></div></div>'
            . '<div><p>School officer</p><div class="line"></div></div>'
            . '</div>'
            . '</div>';
    }

    protected function header(string $title): string
    {
        $logo = 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752774384/TssLogo_hoag31.jpg';

        return '<div class="head">'
            . '<img src="' . $logo . '" alt="Logo" />'
            . '<div>'
            . '<h1>Saint Joseph TSS Nzuki</h1>'
            . '<p>Kirwa Village, Bihembe Cell, Kabagali Sector, Ruhango District</p>'
            . '<h2>' . $this->e($title) . '</h2>'
            . '</div>'
            . '</div>';
    }

    protected function styles(): string
    {
        return '<style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
            .page { padding: 8px 4px 16px; }
            .page-break { page-break-after: always; }
            .head { border-bottom: 2px solid #1D4ED8; padding-bottom: 10px; margin-bottom: 14px; }
            .head img { width: 52px; height: 52px; float: left; margin-right: 12px; border-radius: 26px; }
            .head h1 { margin: 0; font-size: 18px; color: #1D4ED8; }
            .head h2 { margin: 6px 0 0; font-size: 14px; color: #0B1F3A; }
            .head p { margin: 2px 0 0; font-size: 10px; color: #444; }
            .meta { margin: 0 0 10px; font-size: 11px; color: #555; }
            table { width: 100%; border-collapse: collapse; }
            .details th { width: 34%; text-align: left; background: #EEF3FA; padding: 7px 8px; border: 1px solid #D6E0F0; font-weight: 600; }
            .details td { padding: 7px 8px; border: 1px solid #D6E0F0; }
            .list th { background: #1D4ED8; color: #fff; text-align: left; padding: 6px 8px; font-size: 11px; }
            .list td { padding: 6px 8px; border-bottom: 1px solid #E5E7EB; }
            .sign { margin-top: 36px; width: 100%; }
            .sign div { width: 46%; display: inline-block; }
            .sign p { margin: 0 0 28px; font-size: 11px; color: #444; }
            .line { border-bottom: 1px solid #333; height: 1px; }
        </style>';
    }

    protected function formatDate(mixed $value): string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return '';
        }
        $time = strtotime($text);

        return $time ? date('Y-m-d H:i', $time) : $text;
    }

    protected function e(mixed $value): string
    {
        return htmlspecialchars(trim((string) $value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
