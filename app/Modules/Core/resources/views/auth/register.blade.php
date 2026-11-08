@extends('core::layouts.auth')

@section('title', 'Crear cuenta')
@section('heading', 'Crea tu cuenta')
@section('intro', 'Empieza con tu correo. Podrás crear o unirte a una organización después.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('register') }}" data-password-form novalidate>
        @csrf

        <x-core::field name="email" label="Correo electrónico" type="email" autocomplete="email" inputmode="email" autofocus />
        <x-core::password-fields />

        <button class="auth-button" type="submit">Crear cuenta <x-dashboard.icon name="arrow-right" /></button>
    </form>

    <p class="auth-footnote">¿Ya tienes una cuenta? <a class="auth-link" href="{{ route('login') }}">Inicia sesión</a></p>
@endsection
