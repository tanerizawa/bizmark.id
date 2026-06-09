<?php

namespace App\Mcp\Tools;

use App\Services\CivicStackService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Look up a business by NIB (Nomor Induk Berusaha) number via the OSS RBA portal. Returns company details, risk classification, business fields, and addresses.')]
class CivicNibLookup extends Tool
{
    public function handle(Request $request): Response
    {
        $query = $request->input('query', '');

        if (empty($query)) {
            return Response::error('NIB query is required');
        }

        try {
            $civic = app(CivicStackService::class);
            $result = $civic->nibLookup($query);

            if ($result === null) {
                return Response::error('NIB lookup unavailable or not found');
            }

            return Response::json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return Response::error('NIB lookup failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('NIB number (13-digit) or company name')->required(),
        ];
    }
}
