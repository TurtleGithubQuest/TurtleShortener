import("./util/date.ts");
import("./util/misc.js");
import("./util/form.js");
import { bubbleCursor } from "cursor-effects";
import {loadCharts, registerThemes} from "./fn/graph.ts";
import {initializeDateTime} from "./modules/datetime/datetime.js";
import {initializeNavbar} from "./modules/navbar/navbar.js";
import {initializeCopyButtons} from "./modules/copy/copy.js";

const url = window.location.href;
const urlObject = new URL(url);
const params = new URLSearchParams(urlObject.search);

const expValue = (parseInt(params.get('exp')) || 10080)*60;
const expTime = Date.now() + (expValue*1000);

window.addEventListener('DOMContentLoaded', async () => {
	initializeNavbar();
	initializeDateTime();
	initializeCopyButtons();

	const _ = new bubbleCursor();
	registerThemes();
	loadCharts();
});