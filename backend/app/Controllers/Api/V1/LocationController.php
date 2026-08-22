<?php

namespace App\Controllers\Api\V1;

use App\Services\LocationService;

class LocationController extends BaseApiController
{
    public function index()
    {
        $service = new LocationService();
        $data = $service->all();
        $districtCount = array_sum(array_map(
            static fn (array $p) => count($p['districts'] ?? []),
            $data
        ));

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'message' => 'Rwanda provinces and districts',
            'data' => $data,
            'total_provinces' => count($data),
            'total_districts' => $districtCount,
        ]);
    }

    public function districts()
    {
        $province = (string) ($this->request->getGet('province') ?? $this->request->getGet('code') ?? '');
        $districts = (new LocationService())->districts($province);

        if ($province !== '' && $districts === []) {
            return $this->fail('Province not found.', 404);
        }

        return $this->ok($districts);
    }
}
