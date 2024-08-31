import {createEl} from "../util/misc.js";
export async function search(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const response = await fetch("php/fn/Search.php", {
        method: "POST",
        body: formData
    });
    function addResult(href, text, hrefDisplay) {
        const result = createEl("div", "result");
        const urlA  = createEl("a");
        result.innerHTML = text;
        if (hrefDisplay) {
            urlA.href = href;
            urlA.innerText = hrefDisplay;
            result.append(urlA);
        }
        results.append(result)
    }
    const items = e.target.parentElement;
    const results = items.querySelector("#searchResult");
    results.innerHTML = "";
    results.classList.add("d-none");
    if (response.ok) {
        const json = await response.json();
        for (const key of Object.keys(json)) {
            const url = json[key]["url"];
            let userFriendlyURL = url.replace(/(http:\/\/|https:\/\/|www\.)/g, "");
            const shortcode = json[key]["shortcode"];
            const host = window.location.protocol + "//" + window.location.host;
            addResult(url, `
                <a class="shortcode" href="${host}/${shortcode}+">[?]</a>
            `, userFriendlyURL);
        }
    } else {
        addResult(null, response.statusText)
    }
    items.style.overflow = "visible";
    items.style.height = "1.45rem"
    results.classList.remove("d-none");
}
