@extends('core::layouts.auth')

@section('title', 'Recuperar contraseña')
@section('heading', 'Recupera tu contraseña')
@section('intro', 'Te enviaremos un enlace seguro para crear una nueva. Vence en 60 minutos.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-core::field name="email" label="Correo electrónico" type="email" autocomplete="email" inputmode="email" autofocus />

        <button class="auth-button" type="submit">Enviar enlace</button>
    </form>

    <p class="auth-footnote"><a class="auth-link" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
