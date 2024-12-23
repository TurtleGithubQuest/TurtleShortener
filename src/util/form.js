import { createAlertAndAssign } from "./misc.ts";

function handleResponse(e, callback) {
	e.preventDefault();

	const form = e.target;
	const formData = new FormData(form);
	const action = e.submitter.getAttribute('formaction');

	fetch(action, {
		method: form.method,
		body: formData,
	})
		.then(response => response.text())
		.then(callback)
		.catch(error => {
			console.error('Form error:', error);
		});
}

function responseConsole(e) {
	handleResponse(e, data => {
		console.info(data.replace(/<br>/g, '\n'));
	});
}

function responseAlert(e) {
	handleResponse(e, data => {
		createAlertAndAssign('info', data);
	});
}

document.querySelectorAll("form[response-type]").forEach((form) => {
	const responseType = form.getAttribute('response-type');
	if (!responseType) return;

	form.target = "none";

	const responseHandlers = {
		console: responseConsole,
		alert: responseAlert,
	};

	const handler = responseHandlers[responseType];
	if (handler) {
		form.addEventListener('submit', handler);
	}
});