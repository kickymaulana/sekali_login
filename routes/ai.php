<?php

use App\Mcp\Servers\SsoServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::oauthRoutes();

// 🟢 Hilangkan sementara middleware auth:api untuk testing di Inspector
Mcp::web('/mcp/sso', SsoServer::class);
