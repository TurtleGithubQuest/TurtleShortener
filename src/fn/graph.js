import {createEl, deepMerge, deepClone} from "../util/misc.js";
import {themes} from "../settings/graph_themes.js";

let chartContainer;
export function loadCharts() {
    //noinspection JSUnresolvedVariable
    chartContainer = document.getElementById("stats_container");
    if (chartContainer && geoDataSummary) {
        const { total_clicks, avg_clickTime, countries, cities, operating_systems, clicks_by_day } = geoDataSummary;

        const echarts = [
            //createChart(chartContainer, "Countries", countries),
            //createChart(chartContainer, "Cities", cities, "pie"),
            createNestedChart("Countries", "pie", countries, cities),
            createChart("Operating Systems", "pie", operating_systems),
            createChart("Daily visits", "bar", clicks_by_day)
        ];
        //noinspection JSUnresolvedVariable
        window.addEventListener("resize", () => {
            echarts.forEach(([el, echart]) => {
                echart.resize();
            });
        });
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
function createNestedChart(title, theme, dataInner, dataOuter) {
    let [chartDiv, echart, option] = initChart(title, theme);
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
function createChart(title, theme, data) {
    let [chartDiv, echart, option] = initChart(title, theme);
    if (theme === "pie") {
        option["series"][0] = deepMerge(option["series"][0], {
            label: {
                formatter: function (params) {
                        const name = params.name;
                        const value = params.value.toFixed(2);
                        const availableIcons = ['windows', 'linux'];
                        if (availableIcons.includes(name)) {
                            return `{${name}| } {hr| } {percentage|${value}%}`;
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
                const date = new Date((item.unix??0) * 1000);
                return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
            }
        );
    }
    echart.setOption(option);
    return [chartDiv, echart];
}

function langIcons(lang) {
    return svgIcon(`flag/${lang}`);
}
function svgIcon(name) {
    return `img/svg/${name}.svg`;
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