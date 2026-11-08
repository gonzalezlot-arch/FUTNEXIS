@extends('core::layouts.auth')

@section('title', 'Confirma tu correo')
@section('heading', 'Confirma tu correo')
@section('intro', 'Un último paso para activar tu cuenta de FUTNEXIS.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('verification.confirm', ['token' => $token]) }}">
        @csrf

        <button class="auth-button" type="submit">Confirmar mi correo <x-dashboard.icon name="arrow-right" /></button>
    </form>
@endsection
