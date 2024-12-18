import { copyValue } from "../../util/misc.js";

export function initializeCopyButtons() {
	for (const el of document.querySelectorAll('[copyValue]')) {
		el.addEventListener('click', async (e) => {
			const el = e.target;
			copyValue(el);
		});
	}
}