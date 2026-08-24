<?php

namespace App\Services;

/**
 * Maps stored English database text to translation keys.
 */
class StoredCatalog
{
    /**
     * @var array<string, string>
     */
    protected const MAP = [
        'call for admission 2025-2026' => 'admissionCallTitle',
        'submit your application form with required documents: birth certificate, school bulletin, photos, and parent id' => 'admissionCallBody',
        'applications now open' => 'applicationsOpenTitle',
        'saint joseph tss nzuki is accepting new student applications. secure your place today' => 'applicationsOpenBody',
        'join saint joseph tss nzuki' => 'joinSchoolTitle',
        'quality technical education in automobile technology, building construction, and short courses' => 'joinSchoolBody',
        'admissions are open' => 'admissionsOpenTitle',
        'apply now for level 1 short courses and levels 3-5 technical programs' => 'admissionsOpenBody',
        'apply now' => 'applyNow',
        'start application' => 'startApplication',
        'apply here' => 'applyHere',
        'open day at saint joseph tss nzuki' => 'openDayTitle',
        'visit our workshops and meet instructors for automobile, construction, tailoring and carpentry' => 'openDayBody',
        'school campus, nzuki' => 'campusLocation',
        'skills exhibition' => 'skillsExhibitionTitle',
        'students present practical work from technical programs' => 'skillsExhibitionBody',
        'main hall' => 'mainHall',
        'rheinland-pfalz partnership' => 'rlpTitle',
        'long-term cooperation with berufsbildende schule lahnstein and the rheinland-pfalz / rwanda partnership. the project supports workshop equipment, teacher exchange, and student skills in technical trades' => 'rlpBody',
        'automobile workshop upgrade' => 'autoWorkshopTitle',
        'modernizing the automobile technology workshop so level 3-5 students can practise engine diagnostics, servicing, and safety on current vehicles' => 'autoWorkshopBody',
        'building construction practice site' => 'buildSiteTitle',
        'a hands-on construction yard where students learn masonry, measurement, and site safety while contributing to campus buildings' => 'buildSiteBody',
        'carpentry and tailoring workshops' => 'tradesWorkshopTitle',
        'equipping level 1 short-course workshops for carpentry and tailoring so learners can produce real items and prepare for local work' => 'tradesWorkshopBody',
        'academic requirements' => 'academicRequirements',
        'school requirements' => 'academicRequirements',
        'first term - download your level requirements' => 'firstTermRequirements',
        'download your level requirements' => 'downloadLevelRequirements',
        'documents and materials needed for each level' => 'requirementsMaterials',
    ];

    /**
     * @return array{key: string, args: array<string, string>}|null
     */
    public static function resolve(?string $text): ?array
    {
        $normalized = self::normalize((string) $text);
        if ($normalized === '') {
            return null;
        }

        if (isset(self::MAP[$normalized])) {
            return ['key' => self::MAP[$normalized], 'args' => []];
        }

        if (preg_match('/^academic requirements (\d{4}-\d{4})$/', $normalized, $matches)) {
            return [
                'key'  => 'academicRequirementsYear',
                'args' => ['year' => $matches[1]],
            ];
        }

        return null;
    }

    public static function key(?string $text): ?string
    {
        return self::resolve($text)['key'] ?? null;
    }

    public static function normalize(string $text): string
    {
        $text = strtolower(trim(preg_replace('/\s+/u', ' ', $text) ?? $text));
        $text = str_replace(['–', '—'], '-', $text);

        return rtrim($text, " \t\n\r\0\x0B.!?:;");
    }
}
