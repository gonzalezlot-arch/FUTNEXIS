import { unmetPasswordRules } from './password-policy';

function renderPasswordRules(form, password) {
	const unmetRules = unmetPasswordRules(password);

	form.querySelectorAll('[data-password-rule]').forEach((item) => {
		item.classList.toggle('is-met', !unmetRules.includes(item.dataset.passwordRule));
	});

	return unmetRules;
}

function showPasswordError(form, message) {
	const error = form.querySelector('[data-password-client-error]');

	if (error) {
		error.textContent = message;
		error.hidden = message === '';
	}
}

document.querySelectorAll('[data-password-form]').forEach((form) => {
	const passwordInput = form.querySelector('[data-password-input]');
	const confirmationInput = form.querySelector('[data-password-confirmation]');

	renderPasswordRules(form, passwordInput.value);

	passwordInput.addEventListener('input', () => {
		renderPasswordRules(form, passwordInput.value);
		showPasswordError(form, '');
	});

	confirmationInput.addEventListener('input', () => showPasswordError(form, ''));

	form.addEventListener('submit', (event) => {
		if (renderPasswordRules(form, passwordInput.value).length > 0) {
			event.preventDefault();
			showPasswordError(form, 'La contraseña todavía no cumple todos los requisitos.');
			passwordInput.focus();

			return;
		}

		if (passwordInput.value !== confirmationInput.value) {
			event.preventDefault();
			showPasswordError(form, 'Las contraseñas no coinciden.');
			confirmationInput.focus();
		}
	});
});

document.querySelectorAll('[data-password-visibility]').forEach((button) => {
	button.addEventListener('click', () => {
		const input = document.getElementById(button.getAttribute('aria-controls'));
		const isHidden = input.type === 'password';

		input.type = isHidden ? 'text' : 'password';
		button.textContent = isHidden ? 'Ocultar' : 'Mostrar';
		button.setAttribute('aria-pressed', String(isHidden));
	});
});
