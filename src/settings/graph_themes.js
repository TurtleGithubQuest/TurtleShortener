export const themes = {
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
        series: [{
            label: {
                color: 'rgba(255, 255, 255, 0.75)',
            },
            itemStyle: {
                color: '#10918d',
                shadowBlur: 6,
                shadowColor: 'rgba(12, 230, 237, 0.33)',
                borderColor: 'rgba(0, 0, 0, 0.3)',
                borderWidth: 2
            },
        }]
    },
    "bar": {
        xAxis: {
            type: 'category'
        },
        yAxis: {
            type: 'value',
            minInterval: 1,
        },
        series: [{
            type: 'bar',
            showBackground: true,
            selectedMode: "single",
            barWidth: 20,
            backgroundStyle: {
                color: 'rgba(180, 180, 180, 0.2)'
            },
        }]
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
            radius: '40%',
            selectedMode: 'multiple',
            startAngle: 65,
            label: {
                formatter: '{b} {d}%',
                minPadding: 40,
                overflow: 'break'
            },
            emphasis: {
                itemStyle: {
                    borderColor: 'rgba(0, 0, 0, 0.3)',
                    borderWidth: 1.5
                }
            }
        }]
    }
};