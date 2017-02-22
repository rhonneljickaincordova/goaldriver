$(document).ready(function(){
	window.Kpi_list = $("#kpi_table").DataTable({
	"fnInitComplete": function(oSettings, json) {
		if($("#kpi_permission").val() != "readwrite"){
			$("#kpi_table").find(".edit_delete").addClass("hidden");				
		}
    },	
	"columnDefs": [
		{ 	"targets": [0], 
			className: "kpi_name", "searchable": false, "sortable": false, 
			"mRender": function(data, type, row){
				if($("#kpi_permission").val() == "readwrite"){
					
					var text_re = '<p>';
					text_re += "<a href='#' data-toggle='modal' data-target='#edit_kpiModal'  class='edit_kpi' style='margin-right:10px;text-decoration: none;'>";
					text_re += data;
					text_re += "</a>";
					text_re +='</p>';
					
					return text_re;
				}else{
					return '<p>'+data+'</p>' ;
				}
				
				
			}  
		},
		{ "targets": [1], className: "kpi_id hidden" , "searchable": false, "sortable": false },
		{ "targets": [2], className: "kpi_icon hidden", "searchable": false, "sortable": false  },
		{ "targets": [3], className: "kpi_description", "searchable": false, "sortable": false  },
		{ 	"targets": [4], "searchable": false, "sortable": false ,
			className: "kpi_frequency",
			"mRender": function(data, type, row){
							text_re = '<p>'+data+'</p>' ;
							return text_re;
						}
		},
		{ "targets": [5], className: "kpi_format", "searchable": false, "sortable": false  },
		{ "targets": [ 6 ], className: "kpi_best_direction", "searchable": false, "sortable": false  },
		{ "targets": [ 7 ], className: "kpi_target hidden", "searchable": false, "sortable": false  },
		{ "targets": [ 8 ], className: "kpi_rag_1 hidden", "searchable": false, "sortable": false  },
		{ "targets": [ 9 ], className: "kpi_rag_2 hidden", "searchable": false, "sortable": false  },
		{ "targets": [ 10 ], className: "kpi_rag_3 hidden", "searchable": false, "sortable": false  },
		{ "targets": [ 11 ], className: "kpi_rag_4 hidden", "searchable": false, "sortable": false  },
		{ "targets": [ 12 ], className: "kpi_agg_type hidden"  },
		{ "targets": [ 13 ], className: "kpi_kpi_format_id hidden"  },
		{ "targets": [ 14 ], className: "assigned_to hidden",  "mRender": function(data, type, row){
						var user_text = '';
						if(data > 1) { user_text = 'Users'; }else{ user_text = "User"; }
						text_re = '<p style="color:#3E981A;">'+data+ ' '+user_text+'</p>' ;
						return text_re;
					}
		},	
		{ "targets": [ 15 ], className: "islocked hidden"  },
		/* { "targets": [ 15 ], className: "kpi_current_trend"  },
		{ "targets": [ 16 ], className: "kpi_rollup_to_parent"  },
		{ "targets": [ 17 ], className: "kpi_parent_kpi_id"  } */
		{ "targets": [ 16 ], className: "edit_delete dt-body-center", "searchable": false, "sortable": false,
			"mRender": function(data, type, row){
						text_re = "<a href='#'  title='Edit' data-toggle='modal' data-target='#edit_kpiModal'  class='edit_kpi' style='margin-right:10px;text-decoration: none;'>";
						text_re += '<i class="fa fa-pencil" style="font-size:15px;"></i>';
						text_re += "</a>";
						text_re += "<a href='#'  title='Delete' data-toggle='modal' data-target='#delete_kpiModal'  class='delete_kpi' style='text-decoration: none;'>";
						text_re += '<i class="fa fa-trash-o" style="font-size:15px;"></i>';
						text_re += "</a>";			
						
						if($("#kpi_permission").val() == "readwrite"){
							return text_re;
						}else{
							return "";
						}
						
					}	
		},
		{ "targets": [ 17 ], className: "kpi_name_sort hidden"  },
		{ "targets": [ 18 ], className: "kpi_kpi_days hidden"  }
	],
	"paginate" :false,
	"dom" : "t",
	"order": [[ 17, "asc" ]]
	/*,
	  
	   "bLengthChange" :false,
	  "processing" :false,
	  "filter" :false, */
  
		
	});
	$('.edit_kpi').tooltip({placement: "left"});
	$('.delete_kpi').tooltip({placement: "right"});
});

