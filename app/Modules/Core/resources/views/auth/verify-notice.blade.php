@extends('core::layouts.auth')

@section('title', 'Verifica tu correo')
@section('heading', 'Verifica tu correo')
@section('intro', 'Abre el enlace que enviamos a tu correo para activar tu cuenta. Vence en 24 horas.')

@section('content')
    <hr class="auth-divider">

    <form class="auth-form" method="POST" action="{{ route('verification.send') }}">
        @csrf

        <x-core::field name="email" label="¿No lo recibiste? Solicita otro enlace" type="email" autocomplete="email" inputmode="email" />

        <button class="auth-button" type="submit">Enviar un nuevo enlace</button>
    </form>

    <p class="auth-footnote"><a class="auth-link" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
