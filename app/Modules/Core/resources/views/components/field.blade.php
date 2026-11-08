@props(['name', 'label', 'type' => 'text', 'value' => null, 'action' => null, 'inputId' => $name])

@php($isPassword = $type === 'password')

<div class="auth-field">
    <div class="auth-label-row">
        <label class="auth-label" for="{{ $inputId }}">{{ $label }}</label>
        {{ $action }}
    </div>
    <div class="auth-input-wrap">
        <input
            {{ $attributes->class(['auth-input', 'auth-input-password' => $isPassword]) }}
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $isPassword ? '' : old($name, $value) }}"
            required
            @error($name) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @enderror
        >
        @if ($isPassword)
            <button class="auth-visibility" type="button" data-password-visibility aria-controls="{{ $inputId }}" aria-pressed="false">Mostrar</button>
        @endif
    </div>
    @error($name)
        <p class="auth-error" id="{{ $inputId }}-error">{{ $message }}</p>
    @enderror
</div>
