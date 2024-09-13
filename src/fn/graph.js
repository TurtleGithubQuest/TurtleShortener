import { createEl } from "../util/misc.js";

export function loadCharts() {
    const chartContainer = document.getElementById("stats_container");
    if (chartContainer && geoDataSummary) {
        const { total_clicks, avg_click_time, countries, cities, operating_systems } = geoDataSummary;

        const echarts = [
            createChart(chartContainer, "Countries", countries, "pie"),
            createChart(chartContainer, "Cities", cities, "pie"),
            createChart(chartContainer, "Operating Systems", operating_systems, "pie")
        ];
        window.addEventListener("resize", () => {
            echarts.forEach(([echart, el]) => {
                echart.resize();
            });
        })
    }
}

function createChart(container, title, data, theme) {
    const chartDiv = createEl('div', 'stats_chart');
    container.appendChild(chartDiv);

    const echart = echarts.init(chartDiv, theme);
    let option = echart.getOption();
    if (!option) {
        option = themes[theme] ?? themes["default"];
    }

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
    }

    echart.setOption(option);
    return [echart, chartDiv];
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

const themes = {
    "default": {
        textStyle: {
            fontFamily: "JetBrainsMono",
            color: "#dfe8ed"
        },
        tooltip: {
            show: false
        },
        title: {
            itemGap: 0,
            textStyle: {
                color: "#f0faff"
            }
        },
        animation: true,
        backgroundColor: null,
    },
    "radar": {
        textStyle: {
            fontFamily: "JetBrainsMono",
            color: "#FFFFFF"
        },
        title: {
            itemGap: 0,
            textStyle: {
                color: "#FFFFFF"
            }
        },
        radar: {
            symbol: "roundRect",
            symbolSize: 5,
            radius: 100,
            startAngle: 65,
            splitNumber: 5,
            name: {
                textStyle: {
                    color: '#FFFFFF'
                },
                renderMode: 'richText',
                formatter: function (name, obj) {
                    const icon = obj.icon ? obj.icon + " " : "";
                    return `${icon} ${obj.name}`;
                },
                rich: {}
            },
            splitArea: {
                areaStyle: {
                    color: ['#00796B', '#20B2AA', '#00796B', '#20B2AA'],
                    shadowColor: 'rgba(0, 0, 0, 0.2)',
                    shadowBlur: 10
                }
            },
            axisLine: {
                lineStyle: {
                    color: "#FFFFFF"
                },
            },
            splitLine: {
                lineStyle: {
                    color: "#FFFFFF"
                }
            },
            emphasis: {
                name: {
                    color: "#FFFFFF"
                }
            },
            indicator: [],
        },
        series: [{
            type: "radar",
            lineStyle: {
                color: '#FFFFFF',
                width: 1
            },
            itemStyle: {
                color: "#FFFFFF"
            },
            areaStyle: {
                color: "rgba(255, 255, 255, 0.1)"
            },
            emphasis: {
                itemStyle: {
                    borderWidth: 3,
                },
                lineStyle: {
                    width: 2
                },
                areaStyle: {
                    color: 'rgba(255, 255, 255, 0.4)'
                }
            }
        }]
    },
    "pie": {
        series: [{
            type: 'pie',
            radius: '50%',
            label: {
                color: 'rgba(255, 255, 255, 0.3)'
            },
            itemStyle: {
                color: '#00796B',
                shadowBlur: 100,
                shadowColor: 'rgba(32, 178, 170, 0.5)'
            },
            emphasis: {
                itemStyle: {
                    borderColor: 'rgba(0, 0, 0, 0.3)',
                    borderWidth: 1
                }
            }
        }]
    }
};
function deepMerge(target, source) {
    for (const key of Object.keys(source)) {
        if (source[key] instanceof Object && key in target) {
            Object.assign(source[key], deepMerge(target[key], source[key]));
        }
    }
    Object.assign(target || {}, source);
    return target;
}
export function registerThemes() {
    if (typeof echarts === "undefined") {
        return;
    }
    let registeredThemes = [];
    for (let [name, option] of Object.entries(themes)) {
        if (name !== "default") {
            option = deepMerge(JSON.parse(JSON.stringify(themes["default"])), option);
            if (option.xAxis && option.xAxis.isArray) {
                option.xAxis = [Object.assign({}, ...option.xAxis)];
            }
        } else {
            if (option.radar && option.radar.name) {
                option.radar.name.rich = fields.reduce((obj, lang) => {
                    obj[lang] = createRichIconEntry(lang);
                    return obj;
                }, {});
            }
        }
        echarts.registerTheme(name, option);
        themes[name] = option;
        registeredThemes.push(name);
    }
    console.debug(`Registered chart themes: ${registeredThemes.join(", ")}.`);
}