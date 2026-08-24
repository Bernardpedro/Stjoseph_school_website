<?php

namespace App\Filters;

use App\Services\LocaleContext;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LocaleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ciLocale = LocaleContext::normalize((string) config('App')->defaultLocale);
        $nuxtLocale = $this->detectNuxtLocale($request, $ciLocale);

        LocaleContext::set($ciLocale, $nuxtLocale);

        if ($request instanceof IncomingRequest) {
            $request->setLocale($ciLocale);
        }

        service('language')->setLocale($ciLocale);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    protected function detectNuxtLocale(RequestInterface $request, string $fallback): string
    {
        $header = trim($request->getHeaderLine('X-Nuxt-Locale'));
        if ($header === '') {
            $header = trim($request->getHeaderLine('X-Locale'));
        }
        if ($header !== '') {
            return LocaleContext::normalize($header);
        }

        if ($request instanceof IncomingRequest) {
            $query = (string) ($request->getGet('nuxt_lang') ?? $request->getGet('lang') ?? '');
            if ($query !== '') {
                return LocaleContext::normalize($query);
            }
        }

        return $fallback;
    }
}
