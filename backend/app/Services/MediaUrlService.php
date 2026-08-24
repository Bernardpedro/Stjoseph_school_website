<?php

namespace App\Services;

class MediaUrlService
{
    /** @var list<string> */
    protected array $photos = [
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080145/DSC_0121_rsjx6k.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080177/DSC_0122_revr2l.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080182/DSC_0123_yqqtu9.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1753956667/DSC_0613.JPGResized1_zqz5go.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1753956666/DSC_0611.JPGResized2_f0xrdd.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140933/5._efpl5u.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752079026/8._odsquj.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771935/SchoolCompound006_wrzj5r.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771937/SchoolCompound002_f7geff.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771577/ClassRoom001_ambcrn.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771577/ClassRoom002_yrl0hs.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1766038842/PortailsReal2_mgw5vf.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752745560/moteur2Resized_pt3a83.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752774851/constr3_j4zg1g.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078179/constr1_nfdcod.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741665/carpentryWorkShop005_r3gocb.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078673/Carpentry1_zn8ktl.jpg',
        'https://res.cloudinary.com/dck2vzccq/image/upload/v1752775223/tailoringResized2_f1gkyl.jpg',
    ];

    public function resolve(?string $path, string $hint = ''): string
    {
        $path = str_replace('\\', '/', trim((string) $path));
        if ($path === '') {
            return $this->fallback($hint, 0);
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $relative = preg_match('#uploads/.+$#', $path, $m) ? $m[0] : ltrim($path, '/');
        $absolute = FCPATH . $relative;
        if (is_file($absolute)) {
            return rtrim((string) config('App')->baseURL, '/') . '/' . $relative;
        }

        return $this->fallback($hint . '|' . $relative, crc32($relative));
    }

    public function resolveMany(array $paths, string $hint = ''): array
    {
        $out = [];
        foreach (array_values($paths) as $i => $path) {
            if (is_array($path)) {
                $url = $path['url'] ?? '';
                $path['url'] = $this->resolve((string) $url, $hint);
                $out[] = $path;
                continue;
            }
            $out[] = $this->resolve((string) $path, $hint . '|' . $i);
        }
        return $out;
    }

    protected function fallback(string $hint, int $seed): string
    {
        $pool = $this->poolFor($hint);
        return $pool[abs($seed) % count($pool)];
    }

    /**
     * @return list<string>
     */
    protected function poolFor(string $hint): array
    {
        $h = strtolower($hint);

        if (preg_match('/exam|rank|legacy|top 10|nesa|result/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1756885559/result2025_d6d9ey.png',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140933/5._efpl5u.jpg',
            ];
        }
        if (preg_match('/mass|holy|bishop|religious|parish|jesus|chorale/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1766038842/PortailsReal2_mgw5vf.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771935/SchoolCompound006_wrzj5r.jpg',
            ];
        }
        if (preg_match('/german|mayor|visit|wibabara|family|delegation/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080145/DSC_0121_rsjx6k.jpg',
            ];
        }
        if (preg_match('/dining|rhein|partner|lahnstein/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771937/SchoolCompound002_f7geff.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078179/constr1_nfdcod.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752774851/constr3_j4zg1g.jpg',
            ];
        }
        if (preg_match('/basket|sport|coupe|football|match/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1753956667/DSC_0613.JPGResized1_zqz5go.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1753956666/DSC_0611.JPGResized2_f0xrdd.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080182/DSC_0123_yqqtu9.jpg',
            ];
        }
        if (preg_match('/fashion|tailor|up stairs/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752775223/tailoringResized2_f1gkyl.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078742/tailoring5_elkcvz.jpg',
            ];
        }
        if (preg_match('/itorero|cultural|igisoro|dancer|feast|exhibition|technical project/', $h)) {
            return [
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080145/DSC_0121_rsjx6k.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080177/DSC_0122_revr2l.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080182/DSC_0123_yqqtu9.jpg',
                'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741665/carpentryWorkShop005_r3gocb.jpg',
            ];
        }

        return $this->photos;
    }
}
