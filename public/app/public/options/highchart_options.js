$(document).ready(function(){
	window.new_gauge_default_options = {
			title : {
				text : ""
			},
			subtitle :{
				text : ""
			},
			tooltip: {
				style: {
					fontSize: '13px',
					fontFamily: '"Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif'
				},
				borderWidth: 1,
				backgroundColor: 'white',
				enabled : true,
				useHTML: true,
				pointFormat: '{point.formula_string}'
			},
			pane: {
				startAngle: 0,
				endAngle: 360,
				background: [
				{ 
					outerRadius: '112%',
					innerRadius: '88%',
					backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2]).setOpacity(0.3).get(),
					borderWidth: 0
				}
				]
			},
			exporting: {
				enabled: false
			},
			yAxis: {
				min: 0,
				max: 100,
				lineWidth: 0,
				tickPositions: []
			},

			plotOptions: {
				solidgauge: {
					dataLabels: {
						format: '{series.name}<br><span style="font-size:2em; color: {point.color}; font-weight: bold">{point.y}%</span>',
						y: -40,
						borderWidth: 0
					},
					linecap: 'round',
					stickyTracking: false,
					borderWidth: '26px'
				}
			},
			credits: {
				enabled: false
			},
			series: [
			{
				name: " ",
				borderColor: Highcharts.getOptions().colors[2],
			   data: [{
					color: Highcharts.getOptions().colors[1],
					radius: '100%',
					innerRadius: '100%',
					y: 0
					
				}]
			}
			]
		};
	
window.gauge_default_options = {
			title : {
				text : ""
			},
			subtitle :{
				text : ""
			},
			tooltip: {
				borderWidth: 0,
				backgroundColor: 'none',
				shadow: false,
				style: {
					fontSize: '12px'
				},
				pointFormat: '{series.name}<br><span style="font-size:2em; color: {point.color}; font-weight: bold">{point.y}%</span>',
				positioner: function (labelWidth, labelHeight) {
					return {
						x: 150 - labelWidth / 2,
						y: 125
					};
				}
			},
			pane: {
				startAngle: 0,
				endAngle: 360,
				background: [
				{ 
					outerRadius: '112%',
					innerRadius: '88%',
					backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2]).setOpacity(0.3).get(),
					borderWidth: 0
				}
				]
			},
			exporting: {
				enabled: false
			},
			yAxis: {
				min: 0,
				max: 100,
				lineWidth: 0,
				tickPositions: []
			},

			plotOptions: {
				solidgauge: {
					borderWidth: '26px',
					dataLabels: {
						enabled: false
					},
					linecap: 'round',
					stickyTracking: false
				}
			},
			credits: {
				enabled: false
			},
			series: [
			{
				name: " ",
				borderColor: Highcharts.getOptions().colors[2],
			   data: [{
					color: Highcharts.getOptions().colors[1],
					radius: '100%',
					innerRadius: '100%',
					y: 0
					
				}]
			}
			]
		};
			
	
	window.gauge_default_colors = {
		green :{
			borderColor : Highcharts.getOptions().colors[2],
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[2]).setOpacity(0.3).get(),			
		},
		amber : {
			borderColor : Highcharts.getOptions().colors[6],
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[6]).setOpacity(0.3).get(),
		},
		red : {
			borderColor : Highcharts.getOptions().colors[8],
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[8]).setOpacity(0.3).get(),
		},
		grey : {
			borderColor : Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0.5).get(),
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0.3).get(),
		}
	};
	
	window.line_default_options = {
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        tooltip: {
            crosshairs : true,
			shared : true,
			valueSuffix: '$'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        yAxis: {
            title: {
                text: 'Data'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
		series: []
    };
	
	window.bar_default_options = {
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        tooltip: {
            crosshairs : true,
			shared : true,
			valueSuffix: '$'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        yAxis: {
            title: {
                text: 'Data'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
		series: []
    };
	
	window.pie_default_options = {
		chart : {
			plotBackgroundColor : null,
			plotBorderWidth : null,
			plotShadow : false
		},
		title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b><br/>{point.percentage:.1f}%'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true
                },
				showInLegend : true
            }
        },
		legend :{
			layout : 'vertical',
			align : 'right',
			verticalAlign : 'middle',
			borderWidth : 0
		}
    };
});