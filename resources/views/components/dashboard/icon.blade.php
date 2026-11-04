@props(['name'])

<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
    @switch($name)
        @case('grid')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" />
            <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
            <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" />
            <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
            @break
        @case('building')
            <path d="M4 20.5h16M6.5 20V4.5h7V20M13.5 9h4v11M9 8h1M9 12h1M9 16h1M16 12h1M16 16h1" />
            @break
        @case('trophy')
            <path d="M8 4h8v4a4 4 0 0 1-8 0V4ZM8 6H4v2a4 4 0 0 0 4 4M16 6h4v2a4 4 0 0 1-4 4M12 12v5M8 20h8M9 17h6" />
            @break
        @case('shield')
            <path d="M12 3.5 19 6v5.5c0 4.2-2.8 7.4-7 9-4.2-1.6-7-4.8-7-9V6l7-2.5Z" />
            <path d="m9 12 2 2 4-4" />
            @break
        @case('users')
            <circle cx="9" cy="8" r="3" />
            <path d="M3.5 20v-1.5a5.5 5.5 0 0 1 11 0V20M16 5.5a3 3 0 0 1 0 5.8M18 14a4.5 4.5 0 0 1 2.5 4v2" />
            @break
        @case('whistle')
            <circle cx="8" cy="14" r="5" />
            <path d="m12.5 11 7-4 2 4-7 4M4.5 9.5l-1-2M8 6V3.5M11.5 7l1.5-2" />
            @break
        @case('chart')
            <path d="M4 19.5h16M6.5 16v-4M12 16V6M17.5 16V9" />
            <path d="m5 9 5-4 5 2 4-3" />
            @break
        @case('user-check')
            <circle cx="9" cy="8" r="3" />
            <path d="M3.5 20v-1.5a5.5 5.5 0 0 1 11 0V20M16 11l2 2 3.5-4" />
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path d="m19.4 15 .1.1 1.1.9-1 1.8-1.4-.5a7.8 7.8 0 0 1-1.6.9l-.2 1.5h-2.1l-.2-1.5a7.8 7.8 0 0 1-1.6-.9l-1.4.5-1-1.8 1.1-.9a6.7 6.7 0 0 1 0-1.8l-1.1-.9 1-1.8 1.4.5a7.8 7.8 0 0 1 1.6-.9l.2-1.5h2.1l.2 1.5a7.8 7.8 0 0 1 1.6.9l1.4-.5 1 1.8-1.1.9a6.7 6.7 0 0 1-.1 1.7Z" transform="translate(-1.5 -1.5)" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('check')
            <path d="m5 12 4 4L19 6" />
            @break
        @case('close')
            <path d="m6 6 12 12M18 6 6 18" />
            @break
        @case('panel-left')
            <rect x="3.5" y="4.5" width="17" height="15" rx="2" />
            <path d="M9 5v14" />
            @break
        @case('menu')
            <path d="M4 6.5h16M4 12h16M4 17.5h16" />
            @break
        @case('bell')
            <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
            @break
        @case('moon')
            <path d="M20 15.2A8.3 8.3 0 0 1 8.8 4 8.4 8.4 0 1 0 20 15.2Z" />
            @break
        @case('sun')
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2.5v2M12 19.5v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2.5 12h2m15 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
            @break
        @case('arrow-right')
            <path d="M4 12h16m-6-6 6 6-6 6" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7v5l3 2" />
            @break
        @default
            <circle cx="12" cy="12" r="8" />
    @endswitch
</svg>