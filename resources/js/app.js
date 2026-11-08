import './auth-forms';

const appShell = document.querySelector('[data-app-shell]');
const themeToggle = document.querySelector('[data-theme-toggle]');
const themeStorageKey = 'futnexis-theme';

function setTheme(theme) {
	document.documentElement.dataset.theme = theme;
	themeToggle?.setAttribute('aria-pressed', String(theme === 'dark'));
	themeToggle?.setAttribute(
		'aria-label',
		theme === 'dark' ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro',
	);

	try {
		window.localStorage.setItem(themeStorageKey, theme);
	} catch {
		// The dashboard remains usable when browser storage is unavailable.
	}
}

try {
	const savedTheme = window.localStorage.getItem(themeStorageKey);

	if (savedTheme === 'light' || savedTheme === 'dark') {
		document.documentElement.dataset.theme = savedTheme;
		themeToggle?.setAttribute('aria-pressed', String(savedTheme === 'dark'));
	}
} catch {
	document.documentElement.dataset.theme = 'light';
}

themeToggle?.setAttribute('aria-pressed', String(document.documentElement.dataset.theme === 'dark'));

themeToggle?.addEventListener('click', () => {
	const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
	setTheme(nextTheme);
});

const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarClose = document.querySelector('[data-sidebar-close]');
const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');
const sidebarCollapseButtons = document.querySelectorAll('[data-sidebar-collapse]');

function setSidebarOpen(isOpen) {
	appShell?.classList.toggle('is-sidebar-open', isOpen);
	sidebarBackdrop?.toggleAttribute('hidden', !isOpen);
	sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
}

sidebarToggle?.addEventListener('click', () => {
	setSidebarOpen(!appShell?.classList.contains('is-sidebar-open'));
});

sidebarClose?.addEventListener('click', () => setSidebarOpen(false));
sidebarBackdrop?.addEventListener('click', () => setSidebarOpen(false));

sidebarCollapseButtons.forEach((sidebarCollapse) => {
	sidebarCollapse.addEventListener('click', () => {
	const isCollapsed = appShell?.classList.toggle('is-collapsed') ?? false;
		sidebarCollapseButtons.forEach((button) => {
			button.setAttribute('aria-expanded', String(!isCollapsed));
			button.setAttribute(
				'aria-label',
				isCollapsed ? 'Expandir menú lateral' : 'Contraer menú lateral',
			);
		});
	});
});

const organizationToggle = document.querySelector('[data-org-toggle]');
const organizationMenu = document.querySelector('[data-org-menu]');

function setOrganizationMenuOpen(isOpen) {
	organizationMenu?.toggleAttribute('hidden', !isOpen);
	organizationToggle?.setAttribute('aria-expanded', String(isOpen));
}

organizationToggle?.addEventListener('click', () => {
	setOrganizationMenuOpen(organizationMenu?.hasAttribute('hidden') ?? true);
});

document.querySelectorAll('[data-organization]').forEach((option) => {
	option.addEventListener('click', () => {
		const organization = option.dataset.organization;
		const initials = option.dataset.organizationMark;

		document.querySelectorAll('[data-selected-organization]').forEach((element) => {
			element.textContent = organization;
		});

		document.querySelectorAll('[data-selected-organization-mark]').forEach((element) => {
			element.textContent = initials;
		});

		document.querySelectorAll('[data-organization]').forEach((organizationOption) => {
			const isSelected = organizationOption === option;

			organizationOption.setAttribute('aria-pressed', String(isSelected));
			organizationOption.querySelector('[data-organization-check]')?.toggleAttribute('hidden', !isSelected);
		});

		setOrganizationMenuOpen(false);
	});
});

document.addEventListener('click', (event) => {
	if (!event.target.closest('[data-org-picker]')) {
		setOrganizationMenuOpen(false);
	}
});

document.addEventListener('keydown', (event) => {
	if (event.key === 'Escape') {
		setOrganizationMenuOpen(false);
		setSidebarOpen(false);
	}
});
