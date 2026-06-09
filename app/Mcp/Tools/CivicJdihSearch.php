<?php

namespace App\Mcp\Tools;

use App\Services\CivicStackService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Search JDIH (Jaringan Dokumentasi dan Informasi Hukum) for Indonesian legal regulations. Returns relevant laws (UU), government regulations (PP), and ministerial regulations (Permen).')]
class CivicJdihSearch extends Tool
{
    public function handle(Request $request): Response
    {
        $keyword = $request->input('keyword', '');
        $type = $request->input('type', 'pp');
        $limit = min((int) $request->input('limit', 5), 20);

        if (empty($keyword)) {
            return Response::error('Search keyword is required');
        }

        try {
            $civic = app(CivicStackService::class);
            $result = $civic->jdihSearch($keyword, $type, $limit);

            if ($result === null) {
                return Response::error('JDIH search unavailable');
            }

            return Response::json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return Response::error('JDIH search failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'keyword' => $schema->string()->description('Search keyword (KBLI description or regulation topic)')->required(),
            'type' => $schema->string()->description('Regulation type: uu, pp, perpres, permen, perda (default: pp)')->optional(),
            'limit' => $schema->integer()->description('Max results (default 5, max 20)')->optional(),
        ];
    }
}
