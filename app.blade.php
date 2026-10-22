<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Banking System')</title>
    <style>
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --brand: #2563eb;
            --green: #059669;
            --red: #dc2626;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
        }
        header {
            background: #0f172a;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header .brand { font-size: 18px; font-weight: 700; letter-spacing: .3px; }
        header nav a { color: #cbd5e1; text-decoration: none; margin-left: 18px; font-size: 14px; }
        header nav a:hover { color: #fff; }
        .wrap { max-width: 1040px; margin: 24px auto; padding: 0 20px; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 17px; margin: 0 0 14px; }
        p.sub { color: var(--muted); margin: 0 0 18px; font-size: 14px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .stat { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 18px; }
        .stat .label { font-size: 12px; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); }
        .stat .value { font-size: 26px; font-weight: 700; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 10px 8px; text-align: left; border-bottom: 1px solid var(--line); }
        th { color: var(--muted); font-weight: 600; font-size: 12px; text-transform: uppercase; }
        td.num, th.num { text-align: right; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-deposit { background: #d1fae5; color: var(--green); }
        .badge-withdrawal { background: #fee2e2; color: var(--red); }
        .badge-low { background: #fef3c7; color: #b45309; }
        .btn {
            display: inline-block; background: var(--brand); color: #fff; border: 0;
            padding: 10px 18px; border-radius: 8px; font-size: 14px; cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background: #1d4ed8; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; padding: 10px 12px; border: 1px solid var(--line);
            border-radius: 8px; font-size: 14px; font-family: inherit; background: #fff;
        }
        .field { margin-bottom: 16px; }
        .row { display: flex; gap: 16px; flex-wrap: wrap; }
        .row > .field { flex: 1 1 220px; }
        .alert { padding: 12px 14px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert ul { margin: 4px 0 0; padding-left: 18px; }
        .muted { color: var(--muted); font-size: 13px; }
        .right { text-align: right; }
        .list-none { list-style: none; margin: 0; padding: 0; }
        .list-none li { padding: 8px 0; border-bottom: 1px solid var(--line); font-size: 14px; }
        .list-none li:last-child { border-bottom: 0; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>
<header>
    <div class="brand">Simple Banking System</div>
    @auth
        <nav>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('transactions.index') }}">Transactions</a>
            <a href="{{ route('transactions.create') }}">New Transaction</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn" style="background:#334155;padding:6px 12px">Logout ({{ auth()->user()->name }})</button>
            </form>
        </nav>
    @endauth
</header>

<div class="wrap">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
