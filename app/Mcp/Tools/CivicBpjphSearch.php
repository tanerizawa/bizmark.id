<?php

namespace App\Mcp\Tools;

use App\Services\CivicStackService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Search BPJPH (Badan Penyelenggara Jaminan Produk Halal) for halal certification status of a company or product.')]
class CivicBpjphSearch extends Tool
{
    public function handle(Request $request): Response
    {
        $company = $request->input('company', '');

        if (empty($company)) {
            return Response::error('Company name is required');
        }

        try {
            $civic = app(CivicStackService::class);
            $result = $civic->bpjphSearch($company);

            if ($result === null) {
                return Response::error('BPJPH search unavailable or no results');
            }

            return Response::json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return Response::error('BPJPH search failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'company' => $schema->string()->description('Company or product name to check halal certification status')->required(),
        ];
    }
}
