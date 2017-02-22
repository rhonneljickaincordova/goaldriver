$(document).ready(function(){
	
	
	
	var DT_options = {
		'language':{ 
			"processing"	: '<div><i class="fa fa-spinner fa-pulse" style="margin-right:10px;"></i><label>Loading...</label></div>',
			"emptyTable"	: "No KPIs have been setup yet"
		},
		"bProcessing" : true,
		"bSort" : false,	
		"aoColumnDefs":[
			{	
				"aTargets": [ 0 ],
				className: "kpi_id dt-body-center hide"
			},
			{	"aTargets": [ 1 ], className:"ed_kpi_name", 'width':'275px'	},
			{ 	"aTargets": [ 2 ], className: 'dt-body-center prev_column', 
				"mRender": function(data, type, row){
					var kpi_id = row[0];
					
					var tooltip_id = "tooltip_"+kpi_id;
					var icons = '<div class="">';
					
					icons += '<span data-toggle="tooltip" title="Actual">';
					icons += '<i class="fa fa-square-o"></i>';
					icons += '</span>';
					var data_entry = row[3];
					if(data_entry.show_target === true){
						icons += '<span data-toggle="tooltip" title="Target">';
						icons += '<i class="fa fa-dot-circle-o"></i>';
						icons += '</span>'
					}
					icons += '</div>';
					
					return icons;
				}
			},
			{ 	
				"aTargets": [ 3, 4, 5, 6, 7 ],	
				"mRender": function(data, type, row){
					var kpi_id = row[0];
					angular.element(this).parents('td').addClass(kpi_id+"_"+data.date);
					
					var actual_val = (data.actual != null ? data.actual : '');
					var target_val = (data.target != null ? data.target : '');
					var show_target = (data.show_target === true ? '' : 'hidden');
					
					if(data.frequency == "daily" && data.display != undefined && (data.display == 0 || data.display == "0"))
					{
						return "";
					}
					
					var actual_text = "<input type='text' class='form-control actual' value='"+actual_val+"' data-val='"+actual_val+"' autocomplete='off'>";
					var target_text = "<input type='text' style='margin-top:3px;' class='form-control target "+show_target+"' value='"+target_val+"' data-val='"+target_val+"' autocomplete='off'>";	
						
					return actual_text + target_text;
					
					
				},
				"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
					var kpi_id = oData[0];
					$(nTd).attr('id',kpi_id+"_"+sData.date);
				}
			},
			{ 	"aTargets": [ 8 ], 'width':'20px', className: 'dt-body-center next_column',
				"mRender": function(data, type, row){
					var kpi_id = row[0];
					
					var tooltip_id = "tooltip_"+kpi_id;
					var icons = '<div class="">';
					
					icons += '<span data-toggle="tooltip" title="Actual">';
					icons += '<i class="fa fa-square-o"></i>';
					icons += '</span>';
					var data_entry = row[3];
					if(data_entry.show_target === true){
						icons += '<span data-toggle="tooltip" title="Target">';
						icons += '<i class="fa fa-dot-circle-o"></i>';
						icons += '</span>'
					}
					icons += '</div>';
					
					return icons;
				}
			}
		],
		"paginate" :false,
		"filter" :false,
		"dom" : "tr"
	};
		
	
	window.Kpi_data_days_list = $("#kpi_calendar-daily").DataTable(DT_options);
	window.Kpi_data_weeks_list = $("#kpi_calendar-weekly").DataTable(DT_options);
	window.Kpi_data_months_list = $("#kpi_calendar-monthly").DataTable(DT_options);
	window.Kpi_data_quarters_list = $("#kpi_calendar-quarterly").DataTable(DT_options);
	window.Kpi_data_years_list = $("#kpi_calendar-yearly").DataTable(DT_options);
	$("#mydatepicker-days").datetimepicker({	format :  "YYYY-MM-DD", useCurrent: false	});
	$("#mydatepicker-weeks").datetimepicker({	format :  "YYYY-MM-DD", useCurrent: false	});
	$("#mydatepicker-months").datetimepicker({	format :  "YYYY-MM", viewMode: "months", useCurrent: false	});
	$("#mydatepicker-quarters").datetimepicker({	format :  "YYYY-MM", viewMode: "months", useCurrent: false	});
	$("#mydatepicker-years").datetimepicker({	format :  "YYYY", viewMode: "years", useCurrent: false	});
	
	$('#kpi_calendar-daily').on('draw.dt', function () {
		$('[data-toggle="tooltip"]').tooltip({placement: "left"});
	});
	$('#kpi_calendar-weekly').on('draw.dt', function () {
		$('[data-toggle="tooltip"]').tooltip({placement: "left"});
	});
	$('#kpi_calendar-monthly').on('draw.dt', function () {
		$('[data-toggle="tooltip"]').tooltip({placement: "left"});
	});
	$('#kpi_calendar-quarterly').on('draw.dt', function () {
		$('[data-toggle="tooltip"]').tooltip({placement: "left"});
	});
	$('#kpi_calendar-yearly').on('draw.dt', function () {
		$('[data-toggle="tooltip"]').tooltip({placement: "left"});
	});
});

