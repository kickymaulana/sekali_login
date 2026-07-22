<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('get-user-info')]
#[Description('Mengambil data detail pengguna SSO berdasarkan email atau ID')]
class GetUserInfoTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $search = $request->get('search');

        $user = User::where('email', $search)
            ->orWhere('id', $search)
            ->with('roles')
            ->first();

        if (! $user) {
            return Response::error("Pengguna dengan ID/Email '{$search}' tidak ditemukan.");
        }

        return Response::structured([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'created_at' => $user->created_at->toIso8601String(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'search' => $schema->string()
                ->description('ID atau Alamat Email pengguna yang ingin dicari')
                ->required(),
        ];
    }
}
