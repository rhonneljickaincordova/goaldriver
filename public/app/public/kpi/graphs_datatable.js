$(document).ready(function(){
	window.Graph_list = $("#graph_table").DataTable({
		"aoColumnDefs":[
			{	
				"targets": [ 0 ], className: "graph_id hidden", "searchable": false, "sortable": false
			},
			{
				"aTargets": [ 1 ], className: "graph_name", "searchable": false, "sortable": false,
				"mRender": function(data, type, row){
					if($("#kpi_permission").val() == "readwrite"){
					
						var text_re = '<p>';
						text_re = "<a href='#' data-toggle='modal' data-target='#edit_graphModal' class='edit_org' style='margin-right:10px;text-decoration: none;'>";
						text_re += data;
						text_re += "</a>";
						text_re +='</p>';
						
						return text_re;
					}else{
						return '<p>'+data+'</p>' ;
					}
					
					/* text_re = "<p>" +data+"</p>";
					return text_re; */
				}
			},
			{ 
				"targets": [ 2 ], className: "graph_description" , "searchable": false, "sortable": false
			},
			{ 
				"targets": [ 3 ], className: "graph_type_id hidden" , "searchable": false, "sortable": false
			},
			{ 
				"targets": [ 4 ], className: "graph_type", "searchable": false, "sortable": false
			},
			{ 
				"targets": [ 5 ], className: "graph_kpi_id hidden", "searchable": false, "sortable": false 
			},
			{ 
				"targets": [ 6 ], className: "graph_kpi_name", "searchable": false, "sortable": false,
				"mRender": function(data, type, row){
					text_re = "<p>" +data+"</p>";
					return text_re;
				}
			},
			{ 
				"targets": [ 7 ], className: "graph_entered_by", "searchable": false, "sortable": false 
			},
			{ 
				"targets": [ 8 ], className: "graph_entered", "searchable": false, "sortable": false
			},
			{ 
				"targets": [ 9 ], 
				className: "edit_delete", 
				"width" : "30px",
				"sortable":false ,
				"mRender": function(data, type, row){
					text_re = "<a href='#' title='Edit' data-toggle='modal' data-target='#edit_graphModal' class='edit_graph' style='margin-right:10px;text-decoration: none;'>";
						text_re += '<i class="fa fa-pencil" style="font-size:15px;"></i>';
					text_re += "</a>";
					text_re += "<a href='#' title='Delete' data-toggle='modal' data-target='#delete_graphModal' class='delete_graph' style='text-decoration: none;'>";
					text_re += '<i class="fa fa-trash-o" style="font-size:15px;"></i>';
					text_re += "</a>";			
					if($("#kpi_permission").val() == "readwrite"){
						return text_re;
					}else{
						return "";
					}
				}
			},
			{	
				"targets": [ 10 ], className: "graph_name_sort hidden"
			},
			{	
				"targets": [ 11 ], className: "graph_show_on_dashboard hidden"
			},
			{	
				"targets": [ 12 ], className: "graph_show_average hidden"
			},
			{	
				"targets": [ 13 ], className: "graph_show_breakdown hidden"
			},
			{	
				"targets": [ 14 ], className: "graph_show_gauge_on_dash hidden"
			},
			{	
				"targets": [ 15 ], className: "graph_reset_frequency_type hidden"
			},
			{	
				"targets": [ 16 ], className: "graph_kpi_frequency hidden"
			}
			
	  ],
	  "paginate" :false,
	  "dom" : "t",
	   "order": [[ 10, "asc" ]]
	  /* 
	  "bLengthChange" :false,
	  "processing" :false,
	  "filter" :false, */
	});
	
	$('.edit_bShowOnDash_tooltip').tooltip({placement: "right"});
	$('.edit_bShowAverage_tooltip').tooltip({placement: "right"});
	$('.edit_bShowBreakdown_tooltip').tooltip({placement: "right"});
	$('.edit_edit_bShowGaugeOnDash_tooltip').tooltip({placement: "right"});
	
	
	$('#graph_table').on('draw.dt', function () {
		if($("#kpi_permission").val() != "readwrite"){
			$("#graph_table").find(".edit_delete").addClass("hidden");				
		}else{
			$('.edit_graph').tooltip({placement: "left"});
			$('.delete_graph').tooltip({placement: "right"});	
		}
	});	
});








