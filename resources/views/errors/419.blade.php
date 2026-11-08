@extends('core::layouts.auth')

@section('title', 'La página expiró')
@section('heading', 'La página expiró')
@section('intro', 'Por seguridad, los formularios abiertos durante mucho tiempo dejan de ser válidos. Vuelve a la página e inténtalo de nuevo.')

@section('content')
    <p class="auth-footnote"><a class="auth-link" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
