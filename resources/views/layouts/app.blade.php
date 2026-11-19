<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'URL Shortener' }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; background: #f4f1ea; color: #1c1915; }
        a { color: #0f4c3a; }
        header, main { width: min(960px, calc(100% - 32px)); margin: 0 auto; }
        header { display: flex; justify-content: space-between; gap: 16px; align-items: center; padding: 24px 0 8px; }
        header p { margin: 0; }
        nav { display: flex; gap: 12px; align-items: center; }
        main { padding-bottom: 48px; }
        h1 { font-size: 1.8rem; margin: 12px 0; }
        form.stack { display: grid; gap: 12px; max-width: 460px; }
        label { display: grid; gap: 4px; font-size: 0.95rem; }
        input, select, button { font: inherit; padding: 8px 10px; }
        button, .button { background: #0f4c3a; color: #fff; border: 0; text-decoration: none; display: inline-block; padding: 8px 12px; cursor: pointer; }
        .button.secondary, button.secondary { background: transparent; color: #1c1915; border: 1px solid #1c1915; }
        .error { color: #8d1d1d; margin: 0; }
        .status { background: #e5f2eb; padding: 10px 12px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e4ddd0; vertical-align: top; word-break: break-word; }
        .muted { color: #5c564c; }
        .card { background: #fff; padding: 16px; border: 1px solid #e4ddd0; }
    </style>
</head>
<body>
    <header>
        <p><a href="{{ route('dashboard') }}"><strong>URL Shortener</strong></a></p>
        @auth
            <nav>
                <span class="muted">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="secondary" type="submit">Log out</button>
                </form>
            </nav>
        @endauth
    </header>
    <main>
        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif
        @yield('content')
    </main>
</body>
</html>
