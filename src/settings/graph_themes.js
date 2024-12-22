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
	},
	"line": {
		grid: {
			left: '5%',
			right: '5%',
			bottom: '8%',
			top: '15%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			axisLine: {
				show: false
			},
			axisTick: {
				show: false
			},
			axisLabel: {
				color: '#dfe8ed',
				margin: 15,
				fontSize: 11
			}
		},
		yAxis: {
			type: 'value',
			splitLine: {
				lineStyle: {
					color: 'rgba(255, 255, 255, 0.08)',
					width: 1,
					type: 'dashed'
				}
			},
			axisLine: {
				show: false
			},
			axisTick: {
				show: false
			},
			axisLabel: {
				color: '#dfe8ed',
				margin: 15,
				fontSize: 11
			}
		},
		legend: {
			data: [],
			textStyle: {
				color: '#dfe8ed',
				fontSize: 12
			},
			padding: [15, 0],
			icon: 'roundRect'
		},
		tooltip: {
			trigger: 'axis',
			show: true,
			backgroundColor: 'rgba(16, 22, 26, 0.8)',
			borderRadius: 4,
			padding: [8, 12],
			textStyle: {
				fontSize: 11,
				fontWeight: 500
			},
			axisPointer: {
				type: 'line',
				lineStyle: {
					color: 'rgba(255, 255, 255, 0.3)',
					width: 1,
					type: 'solid'
				}
			}
		},
		series: [{
			type: 'line',
			smooth: true,
			symbolSize: 8,
			symbol: 'circle',
			lineStyle: {
				width: 3,
				cap: 'round'
			},
			itemStyle: {
				borderWidth: 2
			},
			emphasis: {
				focus: 'series',
				scale: true,
				lineStyle: {
					width: 4
				},
				itemStyle: {
					borderWidth: 3
				}
			},
			showSymbol: false,
			areaStyle: {
				opacity: 0.1
			}
		}]
	}
};