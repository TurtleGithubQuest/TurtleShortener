import {createEl} from "../util/misc";

export async function search(e: SubmitEvent) {
	e.preventDefault();
	const target: HTMLFormElement = e.target;
	const formData: FormData = new FormData(target);
	const response = await fetch("/api/v1/search", {
		method: "POST",
		body: formData
	});

	const items = target?.parentElement;
	const results = items?.querySelector("#searchResult") || null;

	if (results === null || items === null) {
		return;
	}

	results.innerHTML = "";

	const addResult = (function(results) {
		return function(url: string, shortcode: string) {
			const result = createEl('div', 'result');
			const urlA  = createEl('a', '');
			const userFriendlyURL = url.replace(/(http:\/\/|https:\/\/|www\.)/g, "");
			if (userFriendlyURL) {
				if (shortcode !== undefined) {
					const host = window.location.protocol + "//" + window.location.host;
					urlA.href = host+"/"+shortcode+"+";
					result.innerHTML = `<a class="shortcode" href="${url}">[•]</a>`;
				}
				urlA.innerText = userFriendlyURL;
				result.append(urlA);
			}
			results.append(result);
		};
	})(results);

	if (response.ok) {
		const contentType = response.headers.get("content-type");
		if (contentType && contentType.includes("application/json")) {
			const json = await response.json();
			for (const key of Object.keys(json)) {
				const url = json[key]["url"];
				const shortcode = json[key]["shortcode"];
				addResult(
					url,
					shortcode
				);
			}
		} else {
			console.error("Search response is not json",response);
		}
	} else {
		if (response.status !== 404) {
			console.error("Search error", response);
		}
	}
	items.style.overflow = "visible";
	items.style.height = "1.45rem";
}