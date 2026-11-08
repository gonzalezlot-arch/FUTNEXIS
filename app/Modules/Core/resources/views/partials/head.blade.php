<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#101d32">
<meta name="application-name" content="FUTNEXIS">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="FUTNEXIS">
<meta name="referrer" content="no-referrer">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<title>@yield('title') · FUTNEXIS</title>
<script>
    try {
        const savedTheme = localStorage.getItem('futnexis-theme');
        const prefersDark = matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.dataset.theme = savedTheme ?? (prefersDark ? 'dark' : 'light');
    } catch {
        document.documentElement.dataset.theme = 'light';
    }
</script>
@fonts
@vite(['resources/css/app.css', 'resources/js/app.js'])
