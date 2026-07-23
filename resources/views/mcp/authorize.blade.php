<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authorize Application - {{ config('app.name', 'SSO Server') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .icon-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .icon-box svg {
            width: 48px;
            height: 48px;
            color: #4f46e5;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 14px;
            text-align: center;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 24px;
        }
        .info-box .label {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .info-box .value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .btn-group {
            display: flex;
            gap: 12px;
        }
        .btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-cancel:hover { background: #e2e8f0; }
        .btn-approve {
            background: #4f46e5;
            color: #ffffff;
        }
        .btn-approve:hover { background: #4338ca; }
        .btn-approve:disabled { opacity: 0.7; cursor: not-allowed; }
        .btn svg { width: 16px; height: 16px; }
        .spinner { display: none; animation: spin 1s linear infinite; }
        .spinner.show { display: inline-block; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="card">
    <div class="icon-box">
        <svg stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
    </div>

    <h1>Authorize {{ $client->name }}</h1>
    <p class="subtitle">Aplikasi ini akan menerima akses ke akun SSO Anda.</p>

    <div class="info-box">
        <div class="label">Logged in as</div>
        <div class="value">{{ $user->email }}</div>
    </div>

    <div class="btn-group">
        <form method="POST" action="{{ route('passport.authorizations.deny') }}" style="flex:1">
            @csrf
            @method('DELETE')
            <input type="hidden" name="state" value="">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <button type="submit" class="btn btn-cancel">
                <svg stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tolak
            </button>
        </form>

        <form method="POST" action="{{ route('passport.authorizations.approve') }}" style="flex:1" id="authorizeForm">
            @csrf
            <input type="hidden" name="state" value="">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <button type="submit" class="btn btn-approve" id="authorizeButton">
                <span id="authorizeText">Setujui</span>
                <svg id="loadingSpinner" class="spinner" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('authorizeForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('authorizeButton');
        const txt = document.getElementById('authorizeText');
        const spin = document.getElementById('loadingSpinner');
        btn.disabled = true;
        txt.textContent = 'Memproses...';
        spin.classList.add('show');
    });
</script>
</body>
</html>
