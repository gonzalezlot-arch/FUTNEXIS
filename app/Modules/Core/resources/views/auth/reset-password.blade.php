@extends('core::layouts.auth')

@section('title', 'Nueva contraseña')
@section('heading', 'Crea una nueva contraseña')
@section('intro', 'Al guardarla se cerrarán todas tus sesiones abiertas.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('password.update') }}" data-password-form novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-core::field name="email" label="Correo electrónico" type="email" autocomplete="username" inputmode="email" :value="$email" />
        <x-core::password-fields label="Nueva contraseña" />

        <button class="auth-button" type="submit">Guardar contraseña</button>
    </form>

    <p class="auth-footnote"><a class="auth-link" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
