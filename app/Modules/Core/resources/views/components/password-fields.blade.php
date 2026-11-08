@props(['label' => 'Contraseña'])

<x-core::field name="password" :label="$label" type="password" autocomplete="new-password" data-password-input />

<ul class="auth-rules" aria-label="Requisitos de la contraseña">
    <li data-password-rule="length">Mínimo 8 caracteres</li>
    <li data-password-rule="uppercase">Una mayúscula</li>
    <li data-password-rule="lowercase">Una minúscula</li>
    <li data-password-rule="number">Un número</li>
    <li data-password-rule="symbol">Un carácter especial</li>
</ul>

<x-core::field name="password_confirmation" label="Confirmar contraseña" type="password" autocomplete="new-password" data-password-confirmation />

<p class="auth-error" data-password-client-error role="alert" hidden></p>
