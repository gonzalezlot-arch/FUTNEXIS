@extends('layouts.dashboard')

@section('title', 'Panel principal')

@section('content')
    <main class="dashboard-content">
        <div class="demo-banner" role="note">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.5 21 20H3l9-16.5ZM12 9v4.5m0 3v.1" /></svg>
            <span>{{ $dashboard['demo_label'] }}</span>
        </div>

        <section class="hero-panel" aria-labelledby="dashboard-title">
            <div class="hero-copy">
                <span class="hero-kicker">{{ $dashboard['season'] }} · PANEL DE CONTROL</span>
                <h1 id="dashboard-title">Cada torneo,<br> bajo control.</h1>
                <p>Buenos días, Lucía. Este es el pulso de <strong data-selected-organization>{{ $dashboard['organization'] }}</strong>.</p>
            </div>
            <div class="hero-context">
                <div class="hero-context-top">
                    <div class="hero-context-label">
                        <small>ORGANIZACIÓN SELECCIONADA</small>
                        <strong data-selected-organization>{{ $dashboard['organization'] }}</strong>
                    </div>
                    <span class="season-chip">DEMO</span>
                </div>
                <div class="hero-rule"><span aria-hidden="true"></span><span>Operación de temporada activa</span></div>
            </div>
            <div class="hero-field" aria-hidden="true"><span class="field-halfway"></span><span class="field-dot"></span></div>
        </section>

        <div class="section-spacer"></div>

        <section class="stats-grid" aria-label="Indicadores de demostración">
            @foreach ($dashboard['stats'] as $stat)
                <x-dashboard.stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :delta="$stat['delta']"
                    :detail="$stat['detail']"
                    :icon="$stat['icon']"
                    :tone="$stat['tone']"
                />
            @endforeach
        </section>

        <div class="dashboard-columns">
            <section class="panel" aria-labelledby="activity-title">
                <div class="section-heading">
                    <div>
                        <h2 id="activity-title">Actividad reciente</h2>
                        <p>Últimos movimientos del entorno demo</p>
                    </div>
                    <button class="section-action" type="button" disabled aria-disabled="true">Ver actividad <x-dashboard.icon name="arrow-right" /></button>
                </div>
                <div class="activity-list">
                    @foreach ($dashboard['activity'] as $entry)
                        <article class="activity-entry">
                            <span class="activity-icon activity-icon--{{ $entry['tone'] }}"><x-dashboard.icon :name="$entry['icon']" /></span>
                            <div class="activity-main">
                                <div class="activity-copy">
                                    <strong>{{ $entry['title'] }}</strong>
                                    <span>{{ $entry['detail'] }}</span>
                                </div>
                                <time class="activity-time">{{ $entry['time'] }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="panel matches-panel" aria-labelledby="matches-title">
                <div class="section-heading">
                    <div>
                        <h2 id="matches-title">Próximos partidos</h2>
                        <p>Agenda de encuentros · datos ficticios</p>
                    </div>
                    <button class="section-action" type="button" disabled aria-disabled="true">Calendario <x-dashboard.icon name="arrow-right" /></button>
                </div>
                <div class="match-list">
                    @foreach ($dashboard['matches'] as $match)
                        <article class="match-card">
                            <div class="match-teams">
                                <span class="match-team"><span class="team-mark team-mark--blue">{{ $match['home_mark'] }}</span>{{ $match['home'] }}</span>
                                <span class="match-team"><span class="team-mark">{{ $match['away_mark'] }}</span>{{ $match['away'] }}</span>
                            </div>
                            <div class="match-time">
                                <strong>{{ $match['date'] }}</strong>
                                <span>{{ $match['competition'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="fixture-overview">
                    <span class="live-indicator"><span aria-hidden="true"></span>24 partidos programados</span>
                    <span>Próximas 72 h</span>
                </div>
            </section>
        </div>

        <section class="panel tournaments-panel" aria-labelledby="tournaments-title">
            <div class="section-heading">
                <div>
                    <h2 id="tournaments-title">Torneos activos</h2>
                    <p>Avance de competición en {{ $dashboard['season'] }}</p>
                </div>
                <button class="section-action" type="button" disabled aria-disabled="true">Todos los torneos <x-dashboard.icon name="arrow-right" /></button>
            </div>
            <div class="tournament-list">
                @foreach ($dashboard['tournaments'] as $tournament)
                    <article class="tournament-row">
                        <div class="tournament-name">
                            <span class="tournament-mark"><x-dashboard.icon name="trophy" /></span>
                            <span class="tournament-name-copy">
                                <strong>{{ $tournament['name'] }}</strong>
                                <small>{{ $tournament['category'] }}</small>
                            </span>
                        </div>
                        <span class="tournament-teams">{{ $tournament['teams'] }}</span>
                        <div class="progress-wrap">
                            <div class="progress-caption"><span class="tournament-progress-label">Avance</span><strong>{{ $tournament['progress'] }}%</strong></div>
                            <div class="progress-track" role="progressbar" aria-label="Avance de {{ $tournament['name'] }}" aria-valuenow="{{ $tournament['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-fill {{ $tournament['tone'] === 'green' ? 'progress-fill--green' : ($tournament['tone'] === 'amber' ? 'progress-fill--amber' : '') }}" style="width: {{ $tournament['progress'] }}%"></div>
                            </div>
                        </div>
                        <span class="status-pill {{ $tournament['tone'] === 'amber' ? 'status-pill--amber' : '' }}">{{ $tournament['status'] }}</span>
                    </article>
                @endforeach
            </div>
        </section>

        <footer class="footer-note">
            <span><strong>FUTNEXIS</strong> · SPORTS TECHNOLOGY</span>
            <span>Panel de demostración · Información no conectada a base de datos</span>
        </footer>
    </main>
@endsection