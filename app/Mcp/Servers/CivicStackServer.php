<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CivicBpjphSearch;
use App\Mcp\Tools\CivicJdihSearch;
use App\Mcp\Tools\CivicNibLookup;
use App\Mcp\Tools\CivicSimbgSearch;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Indonesia Civic Stack')]
#[Version('1.0.0')]
#[Instructions('Indonesian government data services for business licensing: NIB/OSS lookup, SIMBG building permits, BPJPH halal certification, and JDIH legal regulations search.')]
class CivicStackServer extends Server
{
    protected array $tools = [
        CivicNibLookup::class,
        CivicSimbgSearch::class,
        CivicJdihSearch::class,
        CivicBpjphSearch::class,
    ];
}
