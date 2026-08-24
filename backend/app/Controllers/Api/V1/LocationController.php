<?php

namespace App\Controllers\Api\V1;

use App\Services\ContentCache;
use App\Services\I18n;
use App\Services\LocationService;

class LocationController extends BaseApiController
{
    public function index()
    {
        $cached = ContentCache::remember('locations', 'all', function () {
            $service = new LocationService();
            $data = $service->all();
            $districtCount = array_sum(array_map(
                static fn (array $p) => count($p['districts'] ?? []),
                $data
            ));

            return [
                'data'            => $data,
                'total_provinces' => count($data),
                'total_districts' => $districtCount,
            ];
        }, ContentCache::TTL_LOCATIONS);

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'message' => I18n::line('Api.locations'),
            'data' => $cached['data'],
            'total_provinces' => $cached['total_provinces'],
            'total_districts' => $cached['total_districts'],
        ]);
    }

    public function districts()
    {
        $province = (string) ($this->request->getGet('province') ?? $this->request->getGet('code') ?? '');
        $districts = ContentCache::remember(
            'locations',
            'districts_' . ($province === '' ? 'none' : $province),
            fn () => (new LocationService())->districts($province),
            ContentCache::TTL_LOCATIONS
        );

        if ($province !== '' && $districts === []) {
            return $this->fail('Api.provinceNotFound', 404);
        }

        return $this->ok($districts);
    }
}
