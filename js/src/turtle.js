import("./util/misc.js")
import("./util/date.js")

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