<!DOCTYPE html>
<html lang="es" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#101d32">
        <title>@yield('title', 'Panel principal') · FUTNEXIS</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell" data-app-shell>
            <aside class="sidebar" id="primary-navigation" aria-label="Navegación principal">
                <div class="brand-row">
                    <a class="brand-lockup" href="{{ route('internal.demo') }}" aria-label="FUTNEXIS, panel principal">
                        <span class="brand-mark" aria-hidden="true">FN</span>
                        <span class="brand-copy">
                            <strong>FUTNEXIS</strong>
                            <small>SPORTS TECHNOLOGY</small>
                        </span>
                    </a>
                    <button class="sidebar-close" type="button" data-sidebar-close aria-label="Cerrar menú">
                        <x-dashboard.icon name="close" />
                    </button>
                </div>

                <span class="nav-caption">Gestión</span>
                <nav class="sidebar-nav">
                    <a class="nav-item" href="{{ route('internal.demo') }}" aria-current="page" aria-label="Panel principal">
                        <x-dashboard.icon name="grid" />
                        <span class="nav-item-label">Panel principal</span>
                    </a>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Organizaciones, disponible próximamente">
                        <x-dashboard.icon name="building" />
                        <span class="nav-item-label">Organizaciones</span>
                    </button>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Torneos, disponible próximamente">
                        <x-dashboard.icon name="trophy" />
                        <span class="nav-item-label">Torneos</span>
                    </button>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Equipos, disponible próximamente">
                        <x-dashboard.icon name="shield" />
                        <span class="nav-item-label">Equipos</span>
                    </button>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Jugadores, disponible próximamente">
                        <x-dashboard.icon name="users" />
                        <span class="nav-item-label">Jugadores</span>
                    </button>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Partidos, disponible próximamente">
                        <x-dashboard.icon name="whistle" />
                        <span class="nav-item-label">Partidos</span>
                    </button>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Estadísticas, disponible próximamente">
                        <x-dashboard.icon name="chart" />
                        <span class="nav-item-label">Estadísticas</span>
                    </button>
                </nav>

                <span class="nav-caption" style="margin-top: 25px">Sistema</span>
                <nav class="sidebar-nav" aria-label="Configuración del sistema">
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Usuarios y permisos, disponible próximamente">
                        <x-dashboard.icon name="user-check" />
                        <span class="nav-item-label">Usuarios y permisos</span>
                    </button>
                    <button class="nav-item" type="button" disabled aria-disabled="true" title="Disponible próximamente" aria-label="Configuración, disponible próximamente">
                        <x-dashboard.icon name="settings" />
                        <span class="nav-item-label">Configuración</span>
                    </button>
                </nav>

                <div class="sidebar-footer">
                    <div class="demo-sidebar-note"><span aria-hidden="true"></span><span>Entorno de demostración</span></div>
                    <button class="sidebar-collapse" type="button" data-sidebar-collapse aria-expanded="true" aria-label="Contraer menú lateral" title="Contraer menú lateral">
                        <x-dashboard.icon name="panel-left" />
                    </button>
                </div>
            </aside>

            <button class="sidebar-backdrop" type="button" data-sidebar-backdrop aria-label="Cerrar menú" hidden></button>

            <div class="main-area">
                <header class="topbar">
                    <div class="topbar-start">
                        <button class="mobile-menu-button" type="button" data-sidebar-toggle aria-expanded="false" aria-controls="primary-navigation" aria-label="Abrir menú">
                            <x-dashboard.icon name="menu" />
                        </button>
                        <button class="icon-button sidebar-collapse" type="button" data-sidebar-collapse aria-expanded="true" aria-label="Contraer menú lateral" title="Contraer menú lateral">
                            <x-dashboard.icon name="panel-left" />
                        </button>
                        <div class="context-crumb"><span>FUTNEXIS&nbsp; /</span><strong>Panel principal</strong></div>
                    </div>
                    <div class="topbar-actions">
                        <div class="org-picker topbar-org-picker" data-org-picker>
                            <button class="org-trigger" type="button" data-org-toggle aria-expanded="false" aria-controls="organization-menu" aria-label="Seleccionar organización">
                                <span class="org-emblem" data-selected-organization-mark aria-hidden="true">{{ $dashboard['organization_mark'] }}</span>
                                <span class="org-trigger-copy">
                                    <small>ORGANIZACIÓN ACTIVA</small>
                                    <strong data-selected-organization>{{ $dashboard['organization'] }}</strong>
                                </span>
                                <span class="select-arrow" aria-hidden="true"><x-dashboard.icon name="chevron-down" /></span>
                            </button>
                            <div class="org-menu" id="organization-menu" data-org-menu hidden>
                                @foreach ($dashboard['organizations'] as $organization)
                                    <button
                                        class="org-option"
                                        type="button"
                                        data-organization="{{ $organization['name'] }}"
                                        data-organization-mark="{{ $organization['mark'] }}"
                                        aria-pressed="{{ $organization['name'] === $dashboard['organization'] ? 'true' : 'false' }}"
                                    >
                                        <span class="org-emblem" aria-hidden="true">{{ $organization['mark'] }}</span>
                                        <span>{{ $organization['name'] }}</span>
                                        <span data-organization-check {{ $organization['name'] === $dashboard['organization'] ? '' : 'hidden' }}><x-dashboard.icon name="check" /></span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <button class="icon-button notification-button" type="button" aria-label="Notificaciones de demostración">
                            <x-dashboard.icon name="bell" />
                            <span class="notification-count" aria-hidden="true"></span>
                        </button>
                        <span class="topbar-divider" aria-hidden="true"></span>
                        <button class="icon-button theme-button" type="button" data-theme-toggle aria-pressed="false" aria-label="Cambiar a tema oscuro" title="Cambiar tema">
                            <x-dashboard.icon name="moon" />
                            <span data-theme-icon="sun"><x-dashboard.icon name="sun" /></span>
                        </button>
                        <span class="topbar-divider" aria-hidden="true"></span>
                        <div class="profile" aria-label="Usuario de demostración: Lucía Demo, administradora">
                            <span class="profile-avatar" aria-hidden="true">LD</span>
                            <span class="profile-copy">
                                <strong>Lucía Demo</strong>
                                <small>Administradora</small>
                            </span>
                        </div>
                    </div>
                </header>

                @yield('content')
            </div>
        </div>
    </body>
</html>