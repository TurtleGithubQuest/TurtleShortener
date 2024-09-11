export function loadChart() {
    const chart = document.getElementById("stats_chart");
    if (chart && geoDataSummary) {
        const echart = echarts.init(chart, "default");
        let option = echart.getOption();
        if (!option) option = themes["default"];

        const { total_clicks, avg_click_time, countries, cities, operating_systems, user_agents } = geoDataSummary;

        // Map the data to radar indicators and series data
        const radarIndicators = [
            { name: 'Total Clicks', max: total_clicks },
            { name: 'Average Click Time', max: avg_click_time },
            { name: 'Countries', max: 100 },
            { name: 'Cities', max: 100 },
            { name: 'Operating Systems', max: 100 },
            { name: 'User Agents', max: 100 },
        ];

        const seriesDataList = [
            total_clicks,
            avg_click_time,
            countries.reduce((acc, item) => acc + item.percentage, 0),
            cities.reduce((acc, item) => acc + item.percentage, 0),
            operating_systems.reduce((acc, item) => acc + item.percentage, 0),
            user_agents.reduce((acc, item) => acc + item.percentage, 0),
        ];

        option.series[0].data = [
            {
                value: seriesDataList,
                name: "SeriesName" // set the name of this data series
            },
        ];

        option.radar.indicator = radarIndicators;
        echart.setOption(option);
    }
}

function langIcons(lang) {
    return `img/svg/lang/${lang}.svg`;
}

function createRichIconEntry(lang) {
    return {
        height: 20,
        backgroundColor: {
            image: langIcons(lang),
        },
    };
}

const fields = ["total_clicks", "avg_click_time", "countries", "cities", "operating_systems", "user_agents"];

const themes = {
    "default": {
        textStyle: {
            fontFamily: "JetBrainsMono",
            color: "#dfe8ed"
        },
        title: {
            itemGap: 0,
            textStyle: {
                color: "#f0faff"
            }
        },
        animation: true,
        backgroundColor: null,
        radar: {
            symbol: "roundRect",
            symbolSize: 5,
            radius: 100,
            startAngle: 65,
            splitNumber: 5,
            name: {
                textStyle: {
                    color: '#dfe8ed'
                },
                renderMode: 'richText',
                max: 10,
                formatter: function(name, obj) {
                    return `${obj.icon} ${obj.name}`;
                },
                rich: {}
            },
            splitArea: {
                areaStyle: {
                    color: ['#37344C', '#403E57', '#494863', '#52526E'],
                    shadowColor: 'rgba(0, 0, 0, 0.2)',
                    shadowBlur: 10
                }
            },
            axisLine: {
                lineStyle: {
                    color: "#2f2d3b"
                },
            },
            splitLine: {
                lineStyle: {
                    color: "#2D2B39"
                }
            },
            emphasis: {
                name: {
                    color: "#000"
                }
            },
            indicator: [],
        },
        series: [{
            type: "radar",
            lineStyle: {
                color: '#DAA520',
                width: 1
            },
            itemStyle: {
                color: "#D4AF37"
            },
            areaStyle: {
                color: "rgba(212, 175, 55, 0.1)"
            },
            emphasis: {
                itemStyle: {
                    borderWidth: 3,
                },
                lineStyle: {
                    width: 2
                },
                areaStyle: {
                    color: 'rgba(212, 175, 55, 0.4)'
                }
            }
        }]
    }
};

export function registerThemes() {
    if (typeof echarts !== "undefined")
        for (let [name, option] of Object.entries(themes)) {
            if (name !== "default") {
                option = deepDictMerge(option, themes["default"], false);
                option.xAxis = [Object.assign({}, ...option.xAxis)];
            } else {
                option.radar.name.rich = fields.reduce((obj, lang) => {
                    obj[lang] = createRichIconEntry(lang);
                    return obj;
                }, {});
            }
            echarts.registerTheme(name, option);
            console.debug(`Registered chart theme '${name}'.`);
        }
}