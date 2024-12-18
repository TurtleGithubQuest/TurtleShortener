import { search } from "../../fn/search.js";

export function initializeNavbar(): void {
	const navbar = document.querySelector('nav');
	if (!navbar) {
		return;
	}

	const collapsableMenu = navbar.querySelector<HTMLElement>(".collapsable");
	const searchForm = navbar.querySelector<HTMLFormElement>("form.search");
	const searchResults = searchForm?.parentElement?.querySelector<HTMLElement>('#searchResult');

	if (!collapsableMenu || !searchForm || !searchResults) {
		return;
	}

	const isCollapsible = (): boolean => {
		return window.innerWidth < 1000 || navbar.offsetWidth > window.innerWidth;
	};

	function cookBurger(): void {
		if (!collapsableMenu) {
			console.error("Collapsable menu not found");
			return;
		}
		const collapsible = isCollapsible();
		collapsableMenu.setAttribute("data-collapsable", `${collapsible}`);
		const items = collapsableMenu.querySelector<HTMLElement>('section.items');
		if (!items) {
			return;
		}

		if (collapsible && collapsableMenu.getAttribute("data-collapsed") === null) {
			collapsableMenu.setAttribute("data-collapsed", "true");
			items.style.height = `${items.children.length * 1.45}rem`;
		} else {
			items.style.height = "unset";
		}
	}

	searchForm.addEventListener("submit", search);
	document.addEventListener('click', (e: MouseEvent) => {
		const target = e.target as HTMLElement;
		if (target === document.body || target.classList.contains("index-box")) {
			searchResults.classList.add("d-none");
		} else if ((target as HTMLInputElement).name === "q" && searchResults.innerHTML !== "") {
			searchResults.classList.remove("d-none");
		}
	});

	searchForm.style.display = "flex";
	searchForm.target = "none";

	if (collapsableMenu) {
		const burgerCollapser = collapsableMenu.querySelector<HTMLElement>(".burger");
		if (!burgerCollapser) {
			return;
		}
		window.addEventListener('resize', cookBurger);
		burgerCollapser.addEventListener('click', async (_: MouseEvent) => {
			collapsableMenu.setAttribute('data-collapsed',
				(!(collapsableMenu.getAttribute('data-collapsed') === "true")).toString());
		});
		cookBurger();
	}
}