<?php

namespace App\Mcp\Tools;

use App\Services\CivicStackService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Search SIMBG (Sistem Informasi Manajemen Bangunan Gedung) for building permit hints and requirements in a specific city.')]
class CivicSimbgSearch extends Tool
{
    public function handle(Request $request): Response
    {
        $city = $request->input('city', '');

        if (empty($city)) {
            return Response::error('City name is required');
        }

        try {
            $civic = app(CivicStackService::class);
            $result = $civic->simbgSearch($city);

            if ($result === null) {
                return Response::error('SIMBG search unavailable or no results');
            }

            return Response::json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return Response::error('SIMBG search failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'city' => $schema->string()->description('City or kabupaten name (e.g., "Jakarta Selatan")')->required(),
        ];
    }
}
