@props(['label', 'value', 'delta', 'detail', 'icon', 'tone' => 'blue'])

<article class="stat-card">
    <div class="stat-top">
        <span class="stat-label">{{ $label }}</span>
        <span class="stat-icon stat-icon--{{ $tone }}"><x-dashboard.icon :name="$icon" /></span>
    </div>
    <div class="stat-value-row">
        <strong class="stat-value">{{ $value }}</strong>
        <span class="stat-delta">{{ $delta }}</span>
    </div>
    <span class="stat-detail">{{ $detail }}</span>
</article>