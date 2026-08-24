<?php

namespace App\Controllers\Api\V1;

use App\Services\LocaleContext;

class LocaleController extends BaseApiController
{
    public function index()
    {
        return $this->ok([
            'codeigniter' => LocaleContext::codeigniter(),
            'nuxt'        => LocaleContext::nuxt(),
            'supported'   => config('App')->supportedLocales,
        ]);
    }
}
