@extends('core::layouts.auth')

@section('title', 'Crear cuenta')
@section('heading', 'Crea tu cuenta')
@section('intro', 'Te enviaremos un enlace para confirmar que el correo es tuyo. Después crearás tu contraseña.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('register') }}">
        @csrf

        <x-core::field name="email" label="Correo electrónico" type="email" autocomplete="email" inputmode="email" autofocus />

        <button class="auth-button" type="submit">Continuar <x-dashboard.icon name="arrow-right" /></button>
    </form>

    <p class="auth-footnote">¿Ya tienes una cuenta? <a class="auth-link" href="{{ route('login') }}">Inicia sesión</a></p>
@endsection
