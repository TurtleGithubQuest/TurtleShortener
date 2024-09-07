import {createEl} from "../util/misc.js";
export async function search(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const response = await fetch("/TurtleShortener/Misc/Search.php", {
        method: "POST",
        body: formData
    });
    function addResult(url, shortcode) {
        const result = createEl("div", "result");
        const urlA  = createEl("a");
        const isUndefined = shortcode===undefined;
        let userFriendlyURL = url.replace(/(http:\/\/|https:\/\/|www\.)/g, "");
        if (userFriendlyURL) {
            if (!isUndefined) {
                const host = window.location.protocol + "//" + window.location.host;
                urlA.href = url;
                result.innerHTML = `<a class="shortcode" href="${host}/${shortcode}+">[?]</a>`;
            }
            urlA.innerText = userFriendlyURL;
            result.append(urlA);
        }
        results.append(result);
    }
    const items = e.target.parentElement;
    const results = items.querySelector("#searchResult");
    results.innerHTML = "";
    results.classList.add("d-none");
    if (response.ok) {
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
        console.error(response);
        //addResult(null, response.statusText)
    }
    items.style.overflow = "visible";
    items.style.height = "1.45rem"
    results.classList.remove("d-none");
}