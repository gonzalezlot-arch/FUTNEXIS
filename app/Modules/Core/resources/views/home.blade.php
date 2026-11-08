@section('title', 'Inicio')
<!DOCTYPE html>
<html lang="es" data-theme="light">
    <head>
        @include('core::partials.head')
    </head>
    <body>
        <div class="home-shell">
            <header class="home-topbar">
                <a class="brand-lockup" href="{{ route('home') }}" aria-label="FUTNEXIS, inicio">
                    <span class="brand-mark" aria-hidden="true">FN</span>
                    <span class="brand-copy">
                        <strong>FUTNEXIS</strong>
                        <small>SPORTS TECHNOLOGY</small>
                    </span>
                </a>

                <div class="home-topbar-actions">
                    <span class="home-user">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                    <x-core::theme-toggle />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="home-logout" type="submit">Cerrar sesión</button>
                    </form>
                </div>
            </header>

            <main class="home-main">
                @error('form')
                    <div class="auth-alert auth-alert-error" role="alert">{{ $message }}</div>
                @enderror

                <h1 class="home-title">Te damos la bienvenida a FUTNEXIS</h1>
                <p class="home-copy">Tu cuenta está lista. Para empezar a gestionar torneos, necesitarás formar parte de una organización.</p>

                <div class="home-options">
                    <section class="home-option" aria-labelledby="create-organization-title">
                        <span class="home-option-icon"><x-dashboard.icon name="building" /></span>
                        <h2 id="create-organization-title">Crear una organización</h2>
                        <p>Registra tu club, liga o academia y administra sus competiciones.</p>
                        <span class="home-soon">Disponible próximamente</span>
                    </section>

                    <section class="home-option" aria-labelledby="accept-invitation-title">
                        <span class="home-option-icon"><x-dashboard.icon name="user-check" /></span>
                        <h2 id="accept-invitation-title">Aceptar una invitación</h2>
                        <p>Únete a una organización que ya te haya invitado.</p>
                        <span class="home-soon">Disponible próximamente</span>
                    </section>
                </div>

                <form class="home-security" method="POST" action="{{ route('logout.all') }}">
                    @csrf
                    <p>¿Dejaste una sesión abierta en otro dispositivo?</p>
                    <button class="auth-link home-security-button" type="submit">Cerrar todas mis sesiones</button>
                </form>
            </main>
        </div>
    </body>
</html>
