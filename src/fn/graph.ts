import {createEl, deepMerge, deepClone} from "../util/misc.js";
import {themes} from "../settings/graph_themes.js";

let chartContainer: HTMLElement | null;

export function loadCharts(): void {
	chartContainer = document.getElementById("stats_container");
	if (chartContainer && geoDataSummary) {
		const {total_clicks, avg_clickTime, countries, cities, operating_systems, clicks_by_day} = geoDataSummary;

		const echarts = [
			//createChart(chartContainer, "Countries", countries),
			//createChart(chartContainer, "Cities", cities, "pie"),
			createNestedChart(translations['countries'], "pie", countries, cities),
			createChart(translations['os'], "pie", operating_systems),
			createChart(translations['daily_visits'], "bar", clicks_by_day)
		];
			//noinspection JSUnresolvedVariable
		window.addEventListener("resize", () => {
			echarts.forEach(([el, echart]) => {
				echart.resize();
			});
		});

	}
}

function initChart(title: string, theme: ChartTheme): [HTMLDivElement, any, ChartOption] {
	const chartDiv = createEl('div', 'stats_chart');
	chartContainer.appendChild(chartDiv);

	const echart = echarts.init(chartDiv, theme);

	let option = echart.getOption();
	if (!option) {
		option = themes[theme] ?? themes["default"];
	}
	option = deepClone(option);
	option["title"] = {
		text: title,
		left: 'center'
	};
	return [chartDiv, echart, option];
}

function createNestedChart(
	title: string,
	theme: ChartTheme,
	dataInner: GeoItem[],
	dataOuter: GeoItem[]
): ChartReturn {
	const [chartDiv, echart, option] = initChart(title, theme);
	option["series"][1] = option["series"][0];
	option["series"][0] = deepMerge(option["series"][0], {
		data: dataInner.map(item => ({
			name: item.name,
			value: item.percentage
		})),
		label: {
			position: 'inner',
			fontSize: "0.83rem"
		},
		labelLine: {
			show: false
		},
		radius: [0, 40],
		selectedMode: 'single',
	});
	option["series"][1] = deepMerge(option["series"][1], {
		type: 'pie',
		radius: [45, 65],
		label: {
			fontSize: "1rem"
		},
		data: dataOuter.map(item => ({
			name: item.name,
			value: item.percentage
		}))
	});
	echart.setOption(option);
	return [chartDiv, echart];
}

function createChart(
	title: string,
	theme: ChartTheme,
	data: GeoItem[] | SystemItem[] | DayClickItem[]
): ChartReturn {
	let [chartDiv, echart, option] = initChart(title, theme);
	if (theme === "pie") {
		option["series"][0] = deepMerge(option["series"][0], {
			label: {
				formatter: function (params: { name: any; value: number; }) {
					const name = params.name;
					const value = params.value.toFixed(2);
					const availableIcons = ['windows', 'linux'];
					if (availableIcons.includes(name)) {
						return `{${name}| } {br| } {percentage|${value}%}`;
					} else {
						return `${name} ${value}%`;
					}
				},
				rich: {
					hr: {
						borderColor: '#777',
						width: '100%',
						borderWidth: 0.5,
						height: 0
					},
					windows: {
						height: 24,
						align: 'center',
						backgroundColor: {
							image: svgIcon('windows')
						},
					},
					linux: {
						height: 24,
						align: 'center',
						backgroundColor: {
							image: svgIcon('linux')
						},
					},
					percentage: {
						height: 8,
						align: 'center',
					},
				}
			},
			data: data.map(item => ({
				name: item.name,
				value: item.percentage
			})),
		});
	} else if (theme === "radar") {
		const radarIndicators = data.map(item => ({
			icon: `{${item.name}|}`,
			name: item.name,
			max: 100,
		}));

		const seriesDataList = data.map(item => item.percentage);

		option = {
			...option,
			title: {
				text: title,
				left: 'center'
			},
			radar: {
				...themes["radar"].radar,
				indicator: radarIndicators
			},
			series: [{
				...themes["radar"].series[0],
				data: [{
					value: seriesDataList,
					name: title
				}]
			}]
		};
	} else if (theme === "bar") {
		option["series"][0]["data"] = data.map(item => item.count);
		option["xAxis"]["data"] = data.map(item => {
			const date = new Date((item.unix??0) * 1000);
			return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
		}
		);
	}
	echart.setOption(option);
	return [chartDiv, echart];
}

function langIcons(lang: string): string {
	return svgIcon(`flag/${lang}`);
}
function svgIcon(name: string): string {
	return `img/svg/${name}.svg`;
}

function createRichIconEntry(lang: string): {
    height: number;
    backgroundColor: {
        image: string;
    };
} {
	return {
		height: 20,
		backgroundColor: {
			image: langIcons(lang),
		},
	};
}

const fields: string[] = ["total_clicks", "avg_click_time", "countries", "cities", "operating_systems"];

export function registerThemes(): void {
	if (typeof echarts === "undefined") {
		return;
	}
	const registeredThemes = [];
	for (const [name, option] of Object.entries(themes)) {
		if (name === "default") {
			if (option.radar && option.radar.name) {
				option.radar.name.rich = fields.reduce((obj, lang) => {
					obj[lang] = createRichIconEntry(lang);
					return obj;
				}, {});
			}
		} else {
			const mergedOption = deepMerge(themes["default"], option, false, true);
			echarts.registerTheme(name, mergedOption);
			themes[name] = mergedOption;
		}
		registeredThemes.push(name);
	}
	console.debug(`Registered chart themes: ${registeredThemes.join(", ")}.`);
}