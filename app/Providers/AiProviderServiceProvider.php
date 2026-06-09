<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AiProviderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->injectStreamFalse(config('ai.providers.omniroute.url', ''));
        $this->injectStreamFalse(config('ai.providers.omniroute2.url', ''));
    }

    private function injectStreamFalse(string $url): void
    {
        if (empty($url)) {
            return;
        }

        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $port = parse_url($url, PHP_URL_PORT) ?: '';

        if (empty($host)) {
            return;
        }

        Http::globalRequestMiddleware(function ($request) use ($host, $port) {
            $requestUri = (string) $request->getUri();
            $parsed = parse_url($requestUri);

            if (($parsed['host'] ?? '') !== $host) {
                return $request;
            }

            if ($port && ($parsed['port'] ?? '') !== $port) {
                return $request;
            }

            $body = (string) $request->getBody();

            if (empty($body)) {
                return $request;
            }

            $data = json_decode($body, true);

            if ($data === null || ! isset($data['model'])) {
                return $request;
            }

            if (! isset($data['stream'])) {
                $data['stream'] = false;
                $request->getBody()->rewind();
                $request->getBody()->write(json_encode($data));
            }

            return $request;
        });
    }
}
