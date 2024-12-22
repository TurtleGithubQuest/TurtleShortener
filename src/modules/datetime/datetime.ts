import { updateInputElementDate, updateElementTextDate } from "../../util/date";

export function initializeDateTime(defaultExpValue = 10080) {
	const url = window.location.href;
	const urlObject = new URL(url);
	const params = new URLSearchParams(urlObject.search);

	const exp = params.get('exp');
	const expTimeInSeconds = (exp ? parseInt(exp) : defaultExpValue) * 60;
	const expTime = Date.now() + (expTimeInSeconds * 1000);

	const dateInput: HTMLInputElement|null = document.querySelector('input[type=datetime-local]');
	if (dateInput) {
		updateInputElementDate(dateInput, expTime.toString());
	}

	document.querySelectorAll('[unix]').forEach((el) => {
		const unixAttribute = el.getAttribute('unix') || null;

		if (unixAttribute === null) {
			return;
		}

		const unix = Number(unixAttribute) * 1000;
		updateElementTextDate(el as HTMLElement, unix.toString());
	});
}