$(document).ready(function()
{
	/***** 
	Initialize Milestone List DataTable  
	*****/
	
	window.Milestone_list = $("#datatable_milestones").DataTable({
		'language':{ 
			"processing"	: '<div><i class="fa fa-spinner fa-pulse" style="margin-right:10px;"></i><label>Loading...</label></div>',
			"emptyTable"	: "No Milestones have been setup yet"
		},
		"createdRow": function( row, data, index ) {
			var m_id = data[1];
			$(row).attr('id',"milestone_row-"+m_id);
			$(row).addClass("milestone_rows");
		},
		"aoColumnDefs":[
			{	
				"targets": [ 0 ], className: "milestone_table_title m_name dt-head-center dt-body-center dt-body-padding_none", "searchable": false, "sortable": false,
				"mRender" : function(data, type, row){
					if(type=="display"){
						var m_id = row[1];
						var m_dueDate = row[2];
						var m_dueDate_format_string = row[4];
						var m_status = row[9];
						var m_bShowOnDash = row[14];
						var m_name = data;
						var milestone_checkbox_class = "";
						if(m_bShowOnDash == 1 || m_bShowOnDash == "1"){
							milestone_checkbox_class = "milestone_checkbox_active";
						}else{
							milestone_checkbox_class = "milestone_checkbox";
						}
						
						/******
							HEAD 
						******/
						var nav_header = "<div class='navbar-header'>";
								nav_header += "<button class='navbar-toggle collapsed' data-toggle='collapse' data-target='#bs-example-navbar-collapse-"+m_id+"' aria-expanded='false' style='padding:0px 0px;margin-top:0px;'>";	
									
									nav_header += "<span class='sr-only'>";
										nav_header += "Toggle navigation";
									nav_header += "</span>";
									nav_header += "<span class='fa fa-cogs'>";
									nav_header += "</span>";
								nav_header += "</button>";
								
								nav_header += "<span class='panel-title'>";
									nav_header += "<a class='accordion-toggle' id='accordion-toggle-"+m_id+"' data-toggle='collapse' data-parent='#m_accordion-"+m_id+"' href='#accordion-"+m_id+"' aria-expanded='false'>";
										nav_header += "<i class='fa fa-plus'></i>";
										nav_header += "<span>"+m_name+"</span>";
									nav_header += "</a>";
								nav_header += "</span>";
							nav_header += "</div>"
						
						var nav_con = "<div class='collapse navbar-collapse pull-rigth' id='bs-example-navbar-collapse-"+m_id+"'>";
								nav_con += "<ul  class='nav navbar-nav navbar-right'>";
									 nav_con += "<li>";
										nav_con += "<span class='panel-options'>";
											if($("#milestone_permission").val() == "readwrite"){
												nav_con += "<a href='#' class='edit edit_milestone_btn' data-toggle='modal' data-target='#edit_milestone_modal' title='Edit'>";
													nav_con += "<span></span>Edit";
												nav_con += "</a>";
												
												nav_con += "<a href='#' class='delete delete_milestone_btn' data-toggle='modal' data-target='#delete_milestone_modal' title='Delete'>";
													nav_con += "<span></span>Delete";
												nav_con += "</a>";
												
												nav_con += "<input type='checkbox' class='milestone_checkbox_input'> ";
													nav_con += "<label style='cursor:pointer;' for='rating-input-1-5' class='update_bShowOnDash_btn "+milestone_checkbox_class+"' ></label>";
												nav_con += "</input>";
											}
											
										nav_con += "</span>";
									nav_con += "</li>";
								nav_con += "</ul>";
								nav_con += "<ul class='nav navbar-nav navbar-right'>";
									nav_con += "<li>";
										nav_con += "<span>Due Date : ";
											if(m_dueDate != null && m_dueDate != '0000-00-00'){
												  nav_con += m_dueDate_format_string;
											}else{
												nav_con  +=  'N/A';
											}
										nav_con += " </span>";
									nav_con += "</li>";
								nav_con += "</ul>";
							nav_con += "</div>";
							
						var panel_head = "<div class='panel panel-default'>";
								panel_head += "<div class='panel-heading clearfix'>";
									panel_head += "<nav class='navbar' style='margin-bottom:0px;min-height:0px;'>";
										panel_head += "<div class='container-fluid'>";
											
											panel_head += nav_header;	
											panel_head += nav_con;	
										
										panel_head += "</div>"
									panel_head += "</nav>"
								panel_head += "</div>"
							panel_head += "</div>"
						
						/******
						BODY 
						******/
						var in_progress_string = "<div id='inprogress-"+m_id+"' class='inprogress_mTasks tab-pane fade in active'>";
								
									in_progress_string += "<table id='db_milestoneTask_inprogress-"+m_id+"' class='milestoneTask_dt table table-responsive'>";
										in_progress_string += "<thead>";
											in_progress_string += "<tr>"
												in_progress_string += "<th>Task ID</th>";
												in_progress_string += "<th></th>";
												in_progress_string += "<th>Task</th>";
												in_progress_string += "<th>Description</th>";
												in_progress_string += "<th>Task startDate</th>";
												in_progress_string += "<th>Task startDate Format</th>";
												in_progress_string += "<th>Start Date</th>";
												in_progress_string += "<th>Task duDate</th>";
												in_progress_string += "<th>Task dueDate Format</th>";
												in_progress_string += "<th>Due Date</th>";
												in_progress_string += "<th>By Who</th>";
												in_progress_string += "<th>Status</th>";
												in_progress_string += "<th>Priority</th>";
												in_progress_string += "<th>Participants</th>";
												in_progress_string += "<th>Milestone ID</th>";
												in_progress_string += "<th></th>";
											in_progress_string += "</tr>"
										in_progress_string += "</thead>";
										in_progress_string += "<tbody>";
										in_progress_string += "</tbody>";
									in_progress_string += "</table>";
								
							in_progress_string += "</div>";		
							
						var complete_string = "<div id='complete-"+m_id+"' class='complete_mTasks tab-pane fade'>";
								
									complete_string += "<table id='db_milestoneTask_complete-"+m_id+"' class='milestoneTask_dt table table-responsive'>";
										complete_string += "<thead>";
											complete_string += "<tr>"
												complete_string += "<th>Task ID</th>";
												complete_string += "<th></th>";
												complete_string += "<th>Task</th>";
												complete_string += "<th>Description</th>";
												complete_string += "<th>Task startDate</th>";
												complete_string += "<th>Task startDate Format</th>";
												complete_string += "<th>Start Date</th>";
												complete_string += "<th>Task duDate</th>";
												complete_string += "<th>Task dueDate Format</th>";
												complete_string += "<th>Due Date</th>";
												complete_string += "<th>By Who</th>";
												complete_string += "<th>Status</th>";
												complete_string += "<th>Priority</th>";
												complete_string += "<th>Participants</th>";
												complete_string += "<th>Milestone ID</th>";
												complete_string += "<th></th>";
											complete_string += "</tr>"
										complete_string += "</thead>";
										complete_string += "<tbody>";
										complete_string += "</tbody>";
									complete_string += "</table>";	
								
							complete_string += "</div>";	
						
						var tab_con = "<div class='tab-content' style='border-left:none;border-right:none;border-bottom:none;'>";
								tab_con += in_progress_string;
								tab_con += complete_string;
							tab_con += "</div>";
							
						var	panel_body = "<div id='accordion-"+m_id+"' class='panel-collapse collapse'>";
								panel_body += "<div class='panel-body'>";
									/* 
									NEW TASK BUTTON 
									*/
									if($("#milestone_permission").val() == "readwrite"){
										panel_body += "<a href='#' title='New Task' data-toggle='modal' data-target='#add_task_modal' data-table_name='milestone_task' data-section_target='milestone_task'  class='btn btn-primary btn-sm pull-right'><i class='fa fa-plus'></i> New Task</a>";	
										panel_body += "<br/><br/>";	
									}
									panel_body += "<ul class='nav nav-tabs  nav-justified'>";
										panel_body += "<li class='active'><a data-toggle='tab' href='#inprogress-"+m_id+"'>In progress</a></li>";
										panel_body += "<li><a data-toggle='tab' href='#complete-"+m_id+"'>Completed</a></li>";
									panel_body += "</ul>";
									panel_body += tab_con;
								panel_body += "</div>";
							panel_body += "</div>";
							
						/*****
						MAIN 
						*****/
						var panel_con = "<div class='panel-group' id='m_accordion-"+m_id+"'>";
								panel_con += panel_head;
								panel_con += panel_body;
							panel_con += "</div>";
							
						
						return panel_con;
					}
					return data;
				}
			},
			{	"aTargets": [ 1 ], className: "m_id hidden", "searchable": false, "sortable": false		},
			{	"aTargets": [ 2 ], className: "m_dueDate hidden", "searchable": false, "sortable": true, "sType": "date"	},
			{	"aTargets": [ 3 ], className: "m_dueDate_format hidden", "searchable": false, "sortable": false	},
			{	"aTargets": [ 4 ], className: "m_dueDate_format_string hidden", "searchable": false, "sortable": false	},
			{	"aTargets": [ 5 ], className: "m_startDate hidden", "searchable": false, "sortable": false	},
			{	"aTargets": [ 6 ], className: "m_startDate_format hidden", "searchable": false, "sortable": false	},
			{ 	"aTargets": [ 7 ], className: "m_description hidden" , "searchable": false, "sortable": false	},
			{ 	"aTargets": [ 8 ], className: "m_owner_id hidden" , "searchable": false, "sortable": false	},
			{ 	"aTargets": [ 9 ], className: "m_status hidden", "searchable": false, "sortable": false	},
			{ 	
				"targets": [ 10 ], className: "m_editable hidden", "searchable": false, "sortable": false,	
				"mRender": function(data, type, row){
					if($("#milestone_permission").val() == "readwrite"){
						return "yes";
					}else{
						return "no";
					}
				}
			},
			{ "aTargets": [ 11 ], className: "m_loaded_tasks hidden", "searchable": false, "sortable": false	},
			{ "aTargets": [ 12 ], className: "m_sorter hidden", "searchable": false, "sortable": false	},
			{ "aTargets": [ 13 ], className: "m_taskcount hidden", "searchable": false, "sortable": false	},
			{ "aTargets": [ 14 ], className: "m_bShowOnDash hidden", "searchable": false, "sortable": false	}
	   ],
	  "paginate" :false,
	  "dom" : "tr",
	   "order": [[ 12, "asc" ]]
	});
	
	$('#datatable_milestones').on('draw.dt', function () {
		if($("#milestone_permission").val() == "readwrite"){
			$('.edit_milestone_btn').tooltip({placement: "left"});
			$('.delete_milestone_btn').tooltip({placement: "right"});	
		}
	});	
	
	$('#milestone_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
	$('#milestone_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
	$('#edit_milestone_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
	$('#edit_milestone_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
	
	$('#task_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
	$('#task_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
	$('#edit_task_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
	$('#edit_task_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });

});



