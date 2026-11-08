<!DOCTYPE html>
<html lang="es" data-theme="light">
    <head>
        @include('core::partials.head')
    </head>
    <body>
        <div class="auth-shell">
            <aside class="auth-brand">
                <svg class="auth-pitch" viewBox="0 0 640 420" aria-hidden="true" focusable="false">
                    <rect x="10" y="10" width="620" height="400" rx="6" />
                    <path d="M320 10v400" />
                    <circle cx="320" cy="210" r="62" />
                    <circle cx="320" cy="210" r="3" />
                    <path d="M10 92h112v236H10M630 92H518v236h112M10 150h44v120H10M630 150h-44v120h44" />
                    <path d="M122 166a62 62 0 0 1 0 88M518 166a62 62 0 0 0 0 88" />
                </svg>

                <a class="brand-lockup" href="{{ route('login') }}" aria-label="FUTNEXIS, inicio">
                    <span class="brand-mark" aria-hidden="true">FN</span>
                    <span class="brand-copy">
                        <strong>FUTNEXIS</strong>
                        <small>SPORTS TECHNOLOGY</small>
                    </span>
                </a>

                <div class="auth-brand-message">
                    <p class="auth-eyebrow">Plataforma de gestión deportiva</p>
                    <p class="auth-headline">Cada torneo, <span>bajo control.</span></p>
                    <p class="auth-lead">Organizaciones, equipos, jugadores y competiciones en un mismo lugar.</p>
                </div>

                <ul class="auth-formats" aria-label="Modalidades previstas">
                    <li>Fútbol</li>
                    <li>5</li>
                    <li>6</li>
                    <li>7</li>
                    <li>9</li>
                    <li>11</li>
                </ul>
            </aside>

            <main class="auth-main">
                <x-core::theme-toggle class="auth-theme" />

                <div class="auth-card">
                    <h1 class="auth-card-title">@yield('heading')</h1>
                    @hasSection('intro')
                        <p class="auth-card-copy">@yield('intro')</p>
                    @endif

                    @if (session('status'))
                        <div class="auth-alert" role="status">{{ session('status') }}</div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
