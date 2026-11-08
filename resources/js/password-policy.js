/**
 * Mirrors the backend policy (RN-CORE-003). The server remains the authority.
 */
export const passwordRules = {
	length: (value) => [...value].length >= 8,
	uppercase: (value) => /\p{Lu}/u.test(value),
	lowercase: (value) => /\p{Ll}/u.test(value),
	number: (value) => /\p{N}/u.test(value),
	symbol: (value) => /[\p{Z}\p{S}\p{P}]/u.test(value),
};

export function unmetPasswordRules(value) {
	return Object.keys(passwordRules).filter((rule) => !passwordRules[rule](value));
}
