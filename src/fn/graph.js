import {createEl, deepMerge, deepClone} from "../util/misc.js";
import {themes} from "../settings/graph_themes.js";

let chartContainer;
export function loadCharts() {
    chartContainer = document.getElementById("stats_container");
    if (chartContainer && geoDataSummary) {
        const { total_clicks, avg_click_time, countries, cities, operating_systems, clicks_by_day } = geoDataSummary;

        const echarts = [
            //createChart(chartContainer, "Countries", countries),
            //createChart(chartContainer, "Cities", cities, "pie"),
            createNestedChart("Countries", "pie", cities,countries),
            createChart("Operating Systems", "pie", operating_systems),
            createChart("Daily visits", "bar", clicks_by_day)
        ];
        window.addEventListener("resize", () => {
            echarts.forEach(([el, echart]) => {
                echart.resize();
            });
        })
    }
}
function initChart(title, theme) {
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
function createNestedChart(title, theme, data1, data2) {
    let [chartDiv, echart, option] = initChart(title, theme);
    option["series"][1] = option["series"][0];
    option["series"][0] = deepMerge(option["series"][0], {
        radius: [0, 35],
        selectedMode: 'single',
        label: {
            position: 'inner',
            fontSize: "0.83rem"
        },
        labelLine: {
            show: false
        },
        data: data1.map(item => ({
            name: item.name,
            value: item.percentage
        })),
    });
    option["series"][1] = deepMerge(option["series"][1], {
        type: 'pie',
        radius: [45, 65],
        label: {
            fontSize: "1rem"
        },
        data: data2.map(item => ({
            name: item.name,
            value: item.percentage
        }))
    });
    echart.setOption(option);
    return [chartDiv, echart];
}
function createChart(title, theme, data) {
    let [chartDiv, echart, option] = initChart(title, theme);
    if (theme === "pie") {
        option["series"][0]["data"] = data.map(item => ({
            name: item.name,
            value: item.percentage
        }));
    } else if (theme === "radar") {
        let radarIndicators = data.map(item => ({
            icon: `{${item.name}|}`,
            name: item.name,
            max: 100,
        }));

        let seriesDataList = data.map(item => item.percentage);

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
                const date = new Date(item.unix * 1000);
                return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
            }
        );
    }
    echart.setOption(option);
    return [chartDiv, echart];
}

function langIcons(lang) {
    return `img/svg/flag/${lang}.svg`;
}

function createRichIconEntry(lang) {
    return {
        height: 20,
        backgroundColor: {
            image: langIcons(lang),
        },
    };
}

const fields = ["total_clicks", "avg_click_time", "countries", "cities", "operating_systems"];

export function registerThemes() {
    //noinspection JSUnresolvedVariable
    if (typeof echarts === "undefined") {
        return;
    }
    let registeredThemes = [];
    for (let [name, option] of Object.entries(themes)) {
        if (name !== "default") {
            option = deepMerge(themes["default"], option, false, true);
            /*if (option.xAxis && option.xAxis.isArray) {
                option.xAxis = [Object.assign({}, ...option.xAxis)];
            }*/
        } else {
            if (option.radar && option.radar.name) {
                option.radar.name.rich = fields.reduce((obj, lang) => {
                    obj[lang] = createRichIconEntry(lang);
                    return obj;
                }, {});
            }
        }
        //noinspection JSUnresolvedVariable
        echarts.registerTheme(name, option);
        themes[name] = option;
        registeredThemes.push(name);
    }
    console.debug(`Registered chart themes: ${registeredThemes.join(", ")}.`);
}