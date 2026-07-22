<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetUserInfoTool;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server;

#[Name('SSO Management Server')]
#[Version('1.0.0')]
#[Instructions('Server ini menyediakan kapabilitas manajemen user dan autentikasi SSO.')]
class SsoServer extends Server
{
    protected array $tools = [
        GetUserInfoTool::class, // 🟢 Daftarkan Tool di sini
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
