@extends('core::layouts.auth')

@section('title', 'Iniciar sesión')
@section('heading', 'Inicia sesión')
@section('intro', 'Ingresa con tu correo electrónico y tu contraseña.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf

        <x-core::field name="email" label="Correo electrónico" type="email" autocomplete="username" inputmode="email" autofocus />

        <x-core::field name="password" label="Contraseña" type="password" autocomplete="current-password">
            <x-slot:action>
                <a class="auth-link" href="{{ route('password.request') }}">¿La olvidaste?</a>
            </x-slot:action>
        </x-core::field>

        <button class="auth-button" type="submit">Entrar <x-dashboard.icon name="arrow-right" /></button>
    </form>

    <p class="auth-footnote">¿Eres nuevo en FUTNEXIS? <a class="auth-link" href="{{ route('register') }}">Crea tu cuenta</a></p>
    <p class="auth-footnote"><a class="auth-link" href="{{ route('verification.notice') }}">Reenviar correo de verificación</a></p>
@endsection
