<button {{ $attributes->merge(['class' => 'icon-button theme-button']) }} type="button" data-theme-toggle aria-pressed="false" aria-label="Cambiar a tema oscuro" title="Cambiar tema">
    <span data-theme-icon="moon"><x-dashboard.icon name="moon" /></span>
    <span data-theme-icon="sun"><x-dashboard.icon name="sun" /></span>
</button>
