import("./util/date.js");
import("./util/misc.js");

import {search} from "./fn/search.js";
//Navbar
const navbar = document.querySelector('nav');
const collapsableMenu = navbar.querySelector(".collapsable");
const burgerCollapser = collapsableMenu.querySelector(".burger");

const isCollapsible = () => {
    return window.innerWidth < 1000 || navbar.offsetWidth > window.innerWidth;
}
function cookBurger() {
    const collapsible = isCollapsible();
    collapsableMenu.setAttribute("data-collapsable", `${collapsible}`);
    const items = collapsableMenu.querySelector('section.items')
    if (collapsible && collapsableMenu.getAttribute("data-collapsed") === null) {
        collapsableMenu.setAttribute("data-collapsed", "true");
        items.style.height = items.children.length * 1.45 + "rem";
    } else
        items.style.height = "unset";
}
window.addEventListener('resize', cookBurger);
burgerCollapser.addEventListener('click', async (_) => {
    collapsableMenu.setAttribute('data-collapsed', (!(collapsableMenu.getAttribute('data-collapsed') === "true")).toString())
});
cookBurger()
const searchForm = document.querySelector("form.search");
const searchResults = searchForm.parentElement.querySelector('#searchResult');
searchForm.addEventListener("submit", search);
document.addEventListener('click', (e) => {
    if (e.target === document.body || e.target.classList.contains("index-box")) {
        searchResults.classList.add("d-none");
    } else if (e.target.name === "q" && searchResults.innerHTML !== "") {
        searchResults.classList.remove("d-none");
    }
})
searchForm.style.display = "flex";
searchForm.target = "none";

const url = window.location.href;
const urlObject = new URL(url);
const params = new URLSearchParams(urlObject.search);

const expValue = (parseInt(params.get('exp')) || 10080)*60;
const expTime = Date.now() + expValue;

window.addEventListener('DOMContentLoaded', async (e) => {
    const dateInput = document.querySelector('input[type=datetime-local]')
    updateInputElementDate(dateInput, expTime*1000);
    for (const el of document.querySelectorAll('[unix]')) {
        const unix = el.getAttribute('unix')*1000;
        updateElementTextDate(el, unix);
    }
});