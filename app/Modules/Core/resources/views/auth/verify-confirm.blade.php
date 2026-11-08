@extends('core::layouts.auth')

@section('title', 'Activa tu cuenta')
@section('heading', 'Crea tu contraseña')
@section('intro', 'Al guardarla, tu correo quedará confirmado y tu cuenta activa.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('verification.confirm', ['token' => $token]) }}" data-password-form novalidate>
        @csrf

        <x-core::password-fields />

        <button class="auth-button" type="submit">Activar mi cuenta <x-dashboard.icon name="arrow-right" /></button>
    </form>
@endsection
