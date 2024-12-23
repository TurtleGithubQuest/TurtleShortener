import {createEl, deepMerge, deepClone, getShade} from "../util/misc";
import {themes} from "../settings/graph_themes.js";

let chartContainer: HTMLElement | null;
let fullWidth = false;

export function loadCharts(): void {
	chartContainer = document.getElementById("stats_container");

	if (!chartContainer) {
		return;
	}

	if (typeof geoDataSummary !== 'undefined' && geoDataSummary) {
		loadUrlSummary(geoDataSummary);
	} else if (typeof geoDataRangeSummary !== 'undefined' && geoDataRangeSummary) {
		fullWidth = true;
		loadDateRangeSummary(geoDataRangeSummary);
	}
}

function loadUrlSummary(summary: any): void {
	const {total_clicks, avg_clickTime, countries, cities, operating_systems, clicks_by_day} = summary;
	const echarts = [
		createNestedChart(translations['countries'], "pie", countries, cities),
		createChart(translations['os'], "pie", operating_systems),
		createChart(translations['daily_visits'], "bar", clicks_by_day)
	];
	registerResizeHandler(echarts);
}

function loadDateRangeSummary(summary: any): void {
	const {country_stats, os_stats, city_stats, source_stats} = summary;

	const countryData = country_stats.map((stat: any) => ({
		...stat,
		name: stat.country
	}));
	const osData = os_stats.map((stat: any) => ({
		...stat,
		name: stat.operating_system
	}));
	const cityData = city_stats.map((stat: any) => ({
		...stat,
		name: stat.city
	}));
	const sourceData = source_stats.map((stat: any) => ({
		...stat,
		name: stat.click_source
	}));

	const [chartDiv, echart, option] = initChart(translations['statistics'], 'line');
	const dates = new Set();
	const dataGroups = {
		'Sources': { data: sourceData, categories: [], color: "#A81B8F" },
		'Cities': { data: cityData, categories: [], color: "#FF4B4B" },
		'Countries': { data: countryData, categories: [], color: "#00BA6C" },
		'Operating Systems': { data: osData, categories: [], color: "#4B89FF" },
	};

	Object.values(dataGroups).forEach((group: any) => {
		group.data.forEach((item: any, index: number) => {
			const date = new Date(item.unix * 1000).toLocaleDateString();
			dates.add(date);
			group.categories.push({
				name: item.name,
				color: getShade(group.color, index)
			});
		});
	});

	const dateArray: any = Array.from(dates);
	const series: any = [];

	Object.entries(dataGroups).forEach(([groupName, group]: [string, any], groupIndex: number) => {
		const isFirst = groupIndex === 0;
		Array.from(group.categories).forEach((category: any, categoryIndex: number) => {
			const previousIndex: number = Math.max(Math.min(group.categories.length, categoryIndex) - 1, 0);
			series.push({
				name:  `${translations[groupName.toLowerCase()] ?? groupName} - ${category.name}`,
				type: isFirst ? 'line' : 'bar',
				stack: isFirst ? undefined : groupName,
				emphasis: {
					focus: 'series',
					scale: true
				},
				barGap: '30%',
				barCategoryGap: '40%',
				barWidth: '12',
				itemStyle: {
					opacity: isFirst ? 1 :  0.8,
					// eslint-disable-next-line @typescript-eslint/ban-ts-comment
					// @ts-ignore
					color: isFirst ? category.color : new window.echarts.graphic.LinearGradient(0, 0, 0, 1, [
						{ offset: 0, color: category.color },
						{ offset: 1, color: group.categories[previousIndex].color }
					])
				},
				smooth: isFirst,
				symbolSize: isFirst ? 8 : undefined,
				symbol: isFirst ? 'circle' : undefined,
				lineStyle: isFirst ? {
					color: '#7E296E',
					style: 'dashed',
					width: 1.75,
					cap: 'square',
					join: 'bevel',
					shadowColor: 'rgba(168, 27, 142, 0.5)',
					shadowBlur: 6,
					opacity: 0.4
				} : undefined,
				data: dateArray.map((date: any) => {
					const item = group.data.find((d: any) =>
						new Date(d.unix * 1000).toLocaleDateString() === date &&
						d.name === category.name
					);
					return item ? item.visit_count : 0;
				})
			});
		});
	});

	option.xAxis.data = dateArray;
	option.legend.data = series.map((s: any) => s.name);
	option.series = series;

	echart.setOption(option);
	const echarts: any = [[chartDiv, echart]];
	registerResizeHandler(echarts);
}

function registerResizeHandler(echarts: ChartReturn[]): void {
	window.addEventListener("resize", () => {
		echarts.forEach(([, echart]) => {
			echart.resize();
		});
	});
}

function initChart(title: string, theme: ChartTheme): [HTMLElement, any, ChartOption] {
	const chartDiv = createEl('div', 'stats_chart');

	if (fullWidth) {
		chartDiv.style.width = "100%";
	}

	chartContainer!.appendChild(chartDiv);

	const echart = echarts.init(chartDiv, theme);

	let option = echart.getOption();
	if (!option) {
		option = themes![theme] ?? themes!["default"];
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
	dataInner: any[],
	dataOuter: any[]
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
	data: any[]
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
			data: data.map((item: any) => ({
				name: item.name,
				value: item.percentage
			})),
		});
	} else if (theme === "radar") {
		const radarIndicators = data.map((item: any) => ({
			icon: `{${item.name}|}`,
			name: item.name,
			max: 100,
		}));

		const seriesDataList = data.map((item: any) => item.percentage);

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
	if (typeof echarts === 'undefined' || typeof themes == 'undefined') {
		return;
	}
	const registeredThemes = [];
	for (const [name, option] of Object.entries(themes)) {
		if (name === "default") {
			if (option.radar && option.radar.name) {
				option.radar.name.rich = fields.reduce((obj: any, lang: string) => {
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