angular.module('moreApps')
.filter('propsFilter', function() {
  return function(items, props) {
    var out = [];
	if (angular.isArray(items)) {
      items.forEach(function(item) {
        var itemMatches = false;
		var keys = Object.keys(props);
        for (var i = 0; i < keys.length; i++) {
          var prop = keys[i];
          var text = props[prop].toLowerCase();
          if(item[prop] != undefined){
	          if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
	            itemMatches = true;
	            break;
	          }
	      }
        }
		if (itemMatches) {
          out.push(item);
        }
      });
    } else {
      /* Let the output be the input untouched */
      out = items;
    }

    return out;
  };
})
.filter('GetIndexNumeric', GetIndexNumeric)
.directive('hideCommentEditTextarea', function($parse, $document) {
	
    var dir = {
        compile: function($element, attr) {
          var fn = $parse(attr["hideCommentEditTextarea"]);
          return function(scope, element, attr) {
            element.bind("click", function(event) {
				event.stopPropagation();
			});
            angular.element($document[0].body).bind("click",function(event) {
				if(scope.show_task_comment_container == true){
					scope.$apply(function() {
						fn(scope, {$event:event});
					});	
				}
				
            });
          };
        }
      };
    return dir;
})
.controller('milestoneClassic', function ($scope,$http,$location,$rootScope,$filter,$httpParamSerializerJQLike){
	
	/*************************************************************************************************
	Declare Variables
	*************************************************************************************************/
		$scope.Milestone_tasks_list = {}
		
		$scope.show_task_comment_container = false;
		$scope.load_all_tasks = 0;
		$scope.load_plain_tab = 0;
		$scope.load_kanban_tab = 0;
		
		$scope._multipleUser = {};
		$scope._edit_multipleUser = {};
		$scope.participants = {};
		$scope.edit_participants = {};
		$scope.temp_delete_m_id = 0;
		
		$scope.id_add_task_modal = "#add_task_modal";
		$scope.id_edit_task_modal = "#edit_task_modal";
		$scope.id_delete_task_modal = "#delete_task_modal";
		$scope.id_add_milestone_modal = "#add_milestone_modal";
		$scope.id_edit_milestone_modal = "#edit_milestone_modal";
		$scope.id_delete_milestone_modal = "#delete_milestone_modal";
		
		$scope.url_add_task = 'milestone/ajax_add_task';
		$scope.url_edit_task = 'milestone/ajax_edit_task';
		$scope.url_delete_task = 'milestone/ajax_delete_task';
		$scope.url_add_milestone = "milestone/ajax_add_milestone";
		$scope.url_edit_milestone = "milestone/ajax_edit_milestone";
		$scope.url_delete_milestone = "milestone/ajax_delete_milestone";
		$scope.url_edit_bShowOnDash_milestone = "milestone/ajax_edit_bShowOnDash";
		
		
		$scope.url_add_task_comment = 'milestone/ajax_add_task_comment';
		$scope.url_edit_task_comment = 'milestone/ajax_edit_task_comment';
		$scope.url_delete_task_comment = 'milestone/ajax_delete_task_comment';
		
		$scope.url_get_tasks = 'milestone/ajax_get_all_task';
		$scope.url_get_get_all_milestone = "milestone/ajax_get_all_milestone";
		$scope.url_get_milestone_tasks = "milestone/ajax_get_milestone_tasks";
		$scope.url_get_all_task_comment = "milestone/ajax_get_all_task_comment";
		$scope.url_inline_edit_startDate = 'milestone/ajax_inline_edit_startDate';
		$scope.url_inline_edit_dueDate = 'milestone/ajax_inline_edit_dueDate';
		$scope.url_inline_edit_owner = 'milestone/ajax_inline_edit_owner';
		$scope.url_inline_edit_status = 'milestone/ajax_inline_edit_status';
		$scope.url_inline_edit_priority = 'milestone/ajax_inline_edit_priority';
	
		$scope.organ_usersList = [];
		$scope.milestonesDropdownList = [];
		$scope.notif_task_id = $("#notif_task_id").val();
		
		$scope.filter_0 = [];
		$scope.filter_1 = [];
		$scope.filter_2 = [];
		$scope.filter_3 = [];
		$scope.filter_4 = [];
		$scope.filter_5 = [];
		$scope.filter_6 = [];
		$scope.filter_7 = [];
		$scope.filter_8 = [];
		$scope.filter_9 = [];
		$scope.filter_10 = [];
		
		$scope.statusList = [
			{	value: '0', label: '0'	}, 	
			{	value: '1', label: '10%'	}, 	
			{	value: '2', label: '20%'	}, 	
			{	value: '3', label: '30%'	}, 	
			{	value: '4', label: '40%'	}, 	
			{	value: '5', label: '50%'	}, 	
			{	value: '6', label: '60%'	}, 	
			{	value: '7', label: '70%'	}, 	
			{	value: '8', label: '80%'	}, 	
			{	value: '9', label: '90%'	}, 	
			{	value: '10', label: '100%'	}
		];
		$scope.priorityList = [
		    {value: '1', label: 'None'},
		    {value: '2', label: 'Low'},
		    {value: '3', label: 'Medium'},
		    {value: '4', label: 'High'}
		];
		$scope.kanban_filterList = [
	 		{value: 0, label: 'By Milestone'},
		    {value: 1, label: 'By Priority'},
		    {value: 2, label: 'By Percentage'},
	 	];
		
		$scope.processing_text = '<div><i class="fa fa-spinner fa-pulse" style="margin-right:10px;"></i><label>Loading...</label></div>';
		$scope.empty_table_text = "No Tasks have been setup yet";
		$scope.kanban_filter_id = $scope.kanban_filterList[2];
		
		$scope.dt_options = {
			'language':{ "processing"	: $scope.processing_text, "emptyTable"	: $scope.empty_table_text 	},
			"createdRow": function( row, data, index ) {
				var status = data[11];
				var status_type = "inprogress";
				
				if(status == 10){
					status_type = "complete";	
				}
				
				$(row).attr('id',"milestone_task_row-"+data[0]);
				$(row).attr('data_task_id',data[0]);
				$(row).attr('data_m_id',data[14]);
				$(row).attr('data_status_type',status_type);
				$(row).attr('data_table_name',"milestone_task");
			},
			"bProcessing" : true,
			"aoColumnDefs":[
				{	"aTargets": [ 0 ], className: "m_task_task_id hidden", "searchable": false, "sortable": false 	},
				{	"aTargets": [ 1 ], className: "m_task_comment_count hidden", 'width':'275px', "searchable": false, "sortable": false 	},
				{	"aTargets": [ 2 ], className: "m_task_task_name dt-body-left dt-padding_left ", 'width':'275px', "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var text_re = "<a href='#' data-toggle='modal' data-target='#edit_task_modal' data-section_target='milestone_task'>";
									text_re += data;
								text_re += "</a>";
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return data;
							}
						}
						return data;
					}
				},
				{	"aTargets": [ 3 ], className: "m_task_task_description hidden", 'width':'275px', "searchable": false, "sortable": false 	},
				{ 	"aTargets": [ 4 ], className: 'm_task_task_startDate hidden', "searchable": false, "sortable": false  },
				{ 	"aTargets": [ 5 ], className: 'm_task_task_startDate_format hidden', "searchable": false, "sortable": false  },
				{ 	"aTargets": [ 6 ], className: 'm_task_task_startDate_format_final', "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var defaultdate = "-";
							var static_date = "-";
							if(data != "9999-99-99"){
								var defaultdate = data;
								var static_date = row[5];
							}
							
							var text_re = "<input type='text' class='form-control taskdatatable_start_date' data-initialize='0' id='milestone_taskdatatable_start_date-"+row[0]+"' data-task_id='"+row[0]+"'data-defaultdate='"+defaultdate+"'  data-isclicked='0' />"
							
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return static_date;
							}
						}
						
						return data;
					}
				},
				{ 	"aTargets": [ 7 ], className: 'm_task_task_dueDate hidden', "searchable": false, "sortable": false  },
				{ 	"aTargets": [ 8 ], className: 'm_task_task_dueDate_format hidden', "searchable": false, "sortable": false  },
				{ 	"aTargets": [ 9 ], className: 'm_task_task_dueDate_format_final', "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var defaultdate = "-";
							var static_date = "-";
							if(data != "9999-99-99"){
								var defaultdate = data;
								var static_date = row[8];
							}
							
							var text_re = "<input type='text' class='form-control taskdatatable_due_date' data-initialize='0' id='milestone_taskdatatable_due_date-"+row[0]+"' data-task_id='"+row[0]+"' data-defaultdate='"+defaultdate+"' data-isclicked='0'/>"
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return static_date;
							}
						}
						return data;
					}
				},
				{ 	"aTargets": [ 10 ], className: 'm_task_owner_id', "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_owner = "-";
							var dropdown_text = "<select class='task_dropdown_owner form-control' id='t_dp_owner_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.organ_usersList,function(user){
								var selected = "";
								if(user.value == data){
									selected = "selected";
									static_owner = user.label;
								}
								dropdown_text += "<option value='"+user.value+"' "+selected+">"+user.label+"</option>"; 
							});
							dropdown_text += "</select>";
							if($("#milestone_permission").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_owner;
							}
						}
						return data;
					}
				},
				{ 	"aTargets": [ 11 ], className: 'm_task_status', "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_status = "-";
							var dropdown_text = "<select class='task_dropdown_status form-control' id='t_dp_status_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.statusList,function(status){
								var selected = "";
								if(status.value == data){
									selected = "selected";
									static_status = status.label;
								}
								dropdown_text += "<option value='"+status.value+"' "+selected+">"+status.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_status;
							}
						}
						return data;
					}
				},
				{ 	"aTargets": [ 12 ], className: 'm_task_priority', "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_priority = "-";
							var dropdown_text = "<select class='task_dropdown_priority form-control' id='t_dp_priority_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.priorityList,function(priority){
								var selected = "";
								if(priority.value == data){
									selected = "selected";
									static_priority = priority.label;
								}
								dropdown_text += "<option value='"+priority.value+"' "+selected+">"+priority.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_priority;
							}
						}
						return data;
					}
				},
				{ 	"aTargets": [ 13 ], className: 'm_task_participant_id hidden', "searchable": false, "sortable": false  },
				{ 	"aTargets": [ 14 ], className: 'm_task_milestone_id hidden', "searchable": false, "sortable": false  },
				{ 	"aTargets": [ 15 ], className: 'm_task_edit_delete', "searchable": false, "sortable": false,
					"mRender": function(data, type, row){
						text_re = "<a href='#' title='Edit' data-toggle='modal' data-target='#edit_task_modal' data-section_target='milestone_task' style='margin-right:10px;text-decoration: none;'>";
							text_re += '<i class="fa fa-pencil" style="font-size:15px;"></i>';
						text_re += "</a>";
						text_re += "<a href='#' title='Delete' data-toggle='modal' data-target='#delete_task_modal' style='text-decoration: none;'>";
							text_re += '<i class="fa fa-trash-o" style="font-size:15px;"></i>';
						text_re += "</a>";			
						if($("#milestone_permission").val() == "readwrite"){
							return text_re;
						}else{
							return "";
						}
					}
				}
			],
			"paginate" :false,
			"filter" :false,
			"dom" : "t"
		};
		
		$scope.Tasks_list = $("#datatable_tasks").DataTable({
			'language':{ "processing"	: $scope.processing_text, "emptyTable"	: $scope.empty_table_text	},
			"createdRow": function( row, data, index ){ 
				$(row).attr('id',"task_row-"+data[0]);
				$(row).attr('data_table_name',"task");
			},
			"aoColumnDefs":[
				{	"targets": [ 0 ], className: "t_task_id hidden", "searchable": false, "sortable": false					 	},
				{	"targets": [ 1 ], className: "t_comment_count", "searchable": false, "sortable": false,
					"mRender": function(data, type, row){
						if(type == "display"){
							if(data > 0){
								var text_re = "<a href='#' data-toggle='modal' data-target='#edit_task_modal' data-section_target='task' >";
									text_re += "<i data-toggle='tooltip' data-placement='right' data-original-title='Comments' title='Comments' class='fa fa-comments' title='comments'></i>";;
								text_re += "</a>";
							
								if($("#milestone_permission").val() == "readwrite"){
									return text_re;
								}else{
									return "";
								}
							}else{
								return "";
							}
						}
						return data;
					}
				},
				{ 	"targets": [ 2 ], className: "t_task_name" , "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var text_re = "<a href='#' data-toggle='modal' data-target='#edit_task_modal' data-section_target='milestone_task'>";
									text_re += data;
								text_re += "</a>";
							
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return data;
							}
						}
						return data;
					}
				},
				{ 	"targets": [ 3 ], className: "t_task_description hidden" , "searchable": false, "sortable": true			},
				{ 	"targets": [ 4 ], className: "t_task_startDate hidden", "searchable": false, "sortable": true				},
				{ 	"targets": [ 5 ], className: "t_task_startDate_format hidden", "searchable": false, "sortable": true 	},
				{ 	"targets": [ 6 ], className: "t_task_startDate_format_final", "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var defaultdate = "-";
							var static_date = "-";
							if(data != "9999-99-99"){
								var defaultdate = data;
								var static_date = row[5];
							}
							var text_re = "<input type='text' class='form-control taskdatatable_start_date' data-initialize='0'  id='taskdatatable_start_date-"+row[0]+"' data-task_id='"+row[0]+"' data-defaultdate='"+defaultdate+"' data-isclicked='0' />"
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return static_date;
							}
								
						}
						
						return data;
					}
				},
				{ 	"targets": [ 7 ], className: "t_task_dueDate hidden", "searchable": false, "sortable": false						},
				{ 	"targets": [ 8 ], className: "t_task_dueDate_format hidden", "searchable": false, "sortable": true 				},
				{ 	"targets": [ 9 ], className: "t_task_dueDate_format_final", "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var defaultdate = "-";
							var static_date = "-";
							if(data != "9999-99-99"){
								var defaultdate = row[0];
								var static_date = row[8];
							}
							var text_re = "<input type='text' class='form-control taskdatatable_due_date' data-initialize='0'  id='taskdatatable_due_date-"+row[0]+"' data-task_id='"+row[0]+"' data-defaultdate='"+defaultdate+"' data-isclicked='0' />"
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return static_date;
							}
						}
						return data;
					}
				},
				{ 	"targets": [ 10 ], className: "t_owner_id", "width" : "30px", "searchable":false, "sortable" : true, "sType": "num",
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_owner = "-";
							var dropdown_text = "<select class='task_dropdown_owner form-control' id='t_dp_owner_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.organ_usersList,function(user){
								var selected = "";
								if(user.value == data){
									selected = "selected";
									static_owner = user.label;
								}
								dropdown_text += "<option value='"+user.value+"' "+selected+">"+user.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_owner;
							}
						}
						return data;
					}
				},
				{	"targets": [ 11 ], className: "t_status", "searchable": false,	"sortable" : true, "sType": "num",
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_status = "-";
							var dropdown_text = "<select class='task_dropdown_status form-control' id='t_dp_status_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.statusList,function(status){
								var selected = "";
								if(status.value == data){
									selected = "selected";
									static_status = status.label;
								}
								dropdown_text += "<option value='"+status.value+"' "+selected+">"+status.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_status;
							}
						}
						return data;
					}
				},
				{	"targets": [ 12 ], className: "t_priority", "searchable": false, "sortable" : true, "sType": "num",
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_priority = "-";
							var dropdown_text = "<select class='task_dropdown_priority form-control' id='t_dp_priority_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.priorityList,function(priority){
								var selected = "";
								if(priority.value == data){
									selected = "selected";
									static_priority = priority.label;
								}
								dropdown_text += "<option value='"+priority.value+"' "+selected+">"+priority.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_priority;
							}
						}
						return data;
					}
				},
				{	"targets": [ 13 ], className: "t_participant_id hidden", "searchable": false, "sortable" : false 			},
				{	"targets": [ 14 ], className: "t_milestone_id hidden", "searchable": false, "sortable" : false			 	},
				{	"targets": [ 15 ], className: "t_edit_delete", "searchable": false, "sortable" : false,
					"mRender": function(data, type, row){
						if(type == "display"){
							text_re = "<a href='#' title='Edit' data-toggle='modal' data-target='#edit_task_modal' class='edit_task_icon' data-section_target='task' style='margin-right:10px;text-decoration: none;'>";
								text_re += '<i class="fa fa-pencil" style="font-size:15px;"></i>';
							text_re += "</a>";
							text_re += "<a href='#' title='Delete' data-toggle='modal' data-target='#delete_task_modal' class='delete_task_icon' style='text-decoration: none;'>";
								text_re += '<i class="fa fa-trash-o" style="font-size:15px;"></i>';
							text_re += "</a>";			
							if($("#milestone_permission").val() == "readwrite"){
								return text_re;
							}else{
								return "";
							}
							
						}
						 return data;
					}
				}
			],
		  "paginate" :false,
		  "dom" : "t",
		   "order": [[ 9, "asc" ]]
		});
		
	$('#datatable_tasks').on('draw.dt', function () {
		if($("#milestone_permission").val() != "readwrite"){
			$("#datatable_tasks").find(".t_edit_delete").addClass("hidden");				
		}else{
			$('.edit_task_icon').tooltip({placement: "left"});
			$('.delete_task_icon').tooltip({placement: "right"});	
		}
	});		
	/*************************************************************************************************
	GetIndex of dropdown using value 
	*************************************************************************************************/
	function getSelectedItem(array, object){
		return array[$filter('GetIndexNumeric')(array,object )]; 
	}
/******************************************************************************************************************************************
***
*** MILESTONES	
***
******************************************************************************************************************************************/
	/*************************************************************************************************
	On Click accordion-toggle of Milestone Row
	*************************************************************************************************/
		angular.element("#datatable_milestones").on('click',".accordion-toggle", function(){
			$(this).find('i').toggleClass('fa-minus').toggleClass('fa-plus');
			var id = $(this).attr('id');
			var current_row = $(this).parents('tr');	
			var row_data = Milestone_list.row( current_row ).data();
			var m_id = row_data[1];
			var loaded_tasks = row_data[11];
			
			if(loaded_tasks == 0){
				$scope.get_milestone_tasks(m_id);
			}
		});	
	/*************************************************************************************************
	On Click open Add Milestone modal
	*************************************************************************************************/
		$scope.open_add_milestone = function(){
			$scope.isopen_name = false;
			$scope.isopen_milestone_date = false;
			
			$scope.milestone_name = "";
			/* $scope.milestone_owner = $scope.organ_usersList[0]; */
			$scope.milestone_owner = $scope.milestone_owner = getSelectedItem($scope.organ_usersList, {value:$scope.user_id}); $scope.organ_usersList[0];
			$scope.milestone_description = "";
			$scope.milestone_status = $scope.statusList[0];
			angular.element("#milestone_start_date").val("");
			angular.element("#milestone_due_date").val("");
			
			$("#add_milestone_modal").modal('show');
		}
	/*************************************************************************************************
	On Click Edit Milestone show Edit Milestone Modal
	*************************************************************************************************/
		angular.element('#edit_milestone_modal').on('show.bs.modal', function(e) {
			$scope.isopen_edit_name = false;
			$scope.isopen_edit_milestone_date = false;
			
			var current_row = angular.element(e.relatedTarget).parents('tr');
			var row_data = Milestone_list.row( current_row ).data();
			var name = row_data[0];
			var m_id = row_data[1];
			var dueDate = row_data[2];
			var dueDate_format = row_data[3];
			var startDate = row_data[5];
			var startDate_format = row_data[6];
			var description = row_data[7];
			var owner_id = row_data[8];
			var status_id = row_data[9];
			
			$scope.$apply(function(){
				$scope.edit_milestone_id = m_id;
				$scope.edit_milestone_name = name;
				$scope.edit_milestone_owner = getSelectedItem($scope.organ_usersList, {value:owner_id}); 
				$scope.edit_milestone_description = description;
				$scope.edit_milestone_status = getSelectedItem($scope.statusList, {value:status_id});  
			});
			
			if(dueDate == null || dueDate == '0000-00-00'){
				angular.element('#edit_milestone_due_date').val('');
			}else{
				angular.element('#edit_milestone_due_date').val(dueDate_format);
			}

			if(startDate == null || startDate == '0000-00-00'){
					angular.element('#edit_milestone_start_date').val('');
			}else{
				angular.element('#edit_milestone_start_date').val(startDate_format);
			}
		});
	/*************************************************************************************************
	On Click Delete Milestone show Delete Milestone Modal
	*************************************************************************************************/
		angular.element('#delete_milestone_modal').on('show.bs.modal', function(e) {
			var current_row = angular.element(e.relatedTarget).parents('tr');
			var row_data = Milestone_list.row( current_row ).data();
			var m_id = row_data[1];
			var taskcount = row_data[13];
			$scope.temp_delete_m_id = m_id;
			$scope.temp_delete_m_taskcount = taskcount;
		});
	/*************************************************************************************************
	On Click Save Milestone button 
	*************************************************************************************************/
		$scope.save_milestone = function(){
			/* start date */
			if(angular.element('#milestone_start_date').val() != ''){
				var temp_start_date  =  angular.element('#milestone_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#milestone_due_date').val() != ''){
				var temp_due_date = angular.element('#milestone_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			var data = {
				action : "add_milestone",
				name: $scope.milestone_name,
				owner_id: $scope.milestone_owner.value,
				description: $scope.milestone_description,
				status: $scope.milestone_status.value,
				start_date: start_date,
				due_date : due_date
			};
			
			var has_error = $scope.validate_Milestone("add", $scope.milestone_name, $scope.milestone_status.value, start_date, due_date, start_date_comparison, due_date_comparison);
			if(has_error == false){
				$scope.process_Milestone(data, "add", $scope.id_add_milestone_modal, $scope.url_add_milestone);
			}
		}
	/************************************************************************************************* 
	On Click Update Milestone button 
	*************************************************************************************************/
		$scope.update_milestone = function()
		{
			/* start date */
			if(angular.element('#edit_milestone_start_date').val() != ''){
				var temp_start_date  =  angular.element('#edit_milestone_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#edit_milestone_due_date').val() != ''){
				var temp_due_date = angular.element('#edit_milestone_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			var data = {
				action : "edit_milestone",
				m_id: $scope.edit_milestone_id,
				name: $scope.edit_milestone_name,
				owner_id: $scope.edit_milestone_owner.value,
				description: $scope.edit_milestone_description,
				status: $scope.edit_milestone_status.value,
				start_date: start_date,
				due_date : due_date
			};
			var has_error = $scope.validate_Milestone("edit", $scope.edit_milestone_name, $scope.edit_milestone_status.value, start_date, due_date, start_date_comparison, due_date_comparison);
			if(has_error == false){
				$scope.process_Milestone(data, "edit", $scope.id_edit_milestone_modal, $scope.url_edit_milestone);
			}
		}
	/*************************************************************************************************
	On Click Delete Milestone button 
	*************************************************************************************************/
		$scope.delete_milestone = function()
		{
			if($scope.load_all_tasks == 1){
				var taskcount = $scope.Tasks_list.column(14).data().filter(
				 function ( value, index ) {
					 return value == $scope.temp_delete_m_id ? true : false;
				}).length;
			}else{
				var taskcount = $scope.temp_delete_m_taskcount;
			}
			if(taskcount > 0){
				$.confirm({
					title: 'Milestone has Tasks!',
					content: 'Do you want to delete the tasks?',
					confirmButton: 'Delete',
					cancelButton: "Don't delete",
					 confirmButtonClass: 'btn-danger',
					cancelButtonClass: 'btn-info',
					confirm: function(){
						var data = { action : "delete_milestone", m_id: $scope.temp_delete_m_id, delete_tasks : 1 };
						$scope.process_Milestone(data, "delete", $scope.id_delete_milestone_modal, $scope.url_delete_milestone);
					},
					cancel: function(){
						var data = { action : "delete_milestone", m_id: $scope.temp_delete_m_id, delete_tasks : 0  };
						$scope.process_Milestone(data, "delete", $scope.id_delete_milestone_modal, $scope.url_delete_milestone);
					}
				});
			}else{
				var data = { action : "delete_milestone", m_id: $scope.temp_delete_m_id, delete_tasks : 0  };
				$scope.process_Milestone(data, "delete", $scope.id_delete_milestone_modal, $scope.url_delete_milestone);
			}
			
		};
	/*************************************************************************************************
	Show On Dash Milestone
	*************************************************************************************************/	
		$("#datatable_milestones").on('click', ".update_bShowOnDash_btn", function()
		{
			var current_row = angular.element(this).parents('tr');
			var row_data = Milestone_list.row( current_row ).data();
			var m_id = row_data[1];
			var bShowOnDash = row_data[14];
			
			if(bShowOnDash == 1){
				var temp_bShowOnDash = 0;
			}else{
				var temp_bShowOnDash = 1;
			}
			
			var data = { action : "edit_bShowOnDash", m_id: m_id, bShowOnDash : temp_bShowOnDash  };
			$scope.process_Milestone(data, "bShowOnDash", "", $scope.url_edit_bShowOnDash_milestone);
		});	
	/*************************************************************************************************
	VALIDATE Milestone : Add, Edit
	*************************************************************************************************/	
		$scope.validate_Milestone = function(process_type, m_name, m_status, m_start_date, m_due_date, start_date_comparison, due_date_comparison)
		{
			$scope.isopen_name = false;
			$scope.isopen_edit_name = false;
			$scope.isopen_milestone_date = false;
			$scope.isopen_edit_milestone_date = false;
			
			var error = false;
			if(parseInt(m_status) < 0 || parseInt(m_status) > 10){
				error = true;
			}
			
			if(m_name == ""){
				error = true;
				if(process_type == "add"){
					$scope.isopen_name = true;
					$scope._error_m_name = "Milstone Name is required";
				}else{
					$scope.isopen_edit_name = true;
					$scope._error_edit_name = "Milstone Name is required";
				}
			}
			
			if(m_start_date != null && m_due_date != null && m_start_date != m_due_date && start_date_comparison > due_date_comparison){
				error = true;
				if(process_type == "add"){
					$scope.isopen_milestone_date = true;
					$scope._error_m_date = "Start date is Greater than Due date.";
				}else{
					$scope.isopen_edit_milestone_date = true;
					$scope._error_edit_milestone_date = "Start date is Greater than Due date.";
				}
			}
			
			return error;	
		}
	/*************************************************************************************************
	Process POST of Milestone : Add, Edit, Delete
	*************************************************************************************************/
		$scope.process_Milestone =  function(data, process_type, modal_id, url)
		{
			var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
			var final_data = angular.extend(data, csrf_object);
			
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : url,
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.error == 0){
					var data = response.milestone;
					
					switch(process_type ){
						case "add" : 	$scope.add_milestone_toMilestoneList(data);
						break;
						case "edit": 	$scope.update_milestone_toMilestoneList(data);
						break;
						case "delete":	
							$scope.delete_milestone_toMilestoneList(data);
							if($scope.load_all_tasks == 1)
							{
								var task_rows = $scope.Tasks_list.rows().data();
								angular.forEach( task_rows ,function(task_row){
									if(data != 0 && data == task_row[14]){
										var task_row_id = angular.element("#task_row-"+task_row[0]);
										if(task_row_id.length > 0){
											if(final_data.delete_tasks == 1){
												$scope.Tasks_list.row(task_row_id).remove().draw();	
											}else{
												var task_column = angular.element("#task_row-"+task_row[0]).find(".t_milestone_id");
					
												if(task_column.length > 0){
													update_column = $scope.Tasks_list.cell(task_column).data(0); 
												}
											}
											
										}
									}
									
								});	
							}
						break;
						case "bShowOnDash":	$scope.update_milestone_bShowOnDash_toMilestoneList(data);
						break;
					}
						$.alert({
							title: 'Success',
							content: response.message,
							confirmButtonClass: 'btn-success',
							confirm: function(){
								if(modal_id != ""){
									$(modal_id).modal('hide');
								}
							}
						});
				}else{
					$.alert({
						title: 'Error',
						content: response.message,
						confirmButtonClass: 'btn-danger',
						confirm: function(){
							if(modal_id != ""){
								$(modal_id).modal('hide');
							}
						}
					});
				}
			}).error(function(){
				$.alert({
					title: 'Error',
					content: "Failed to process operation.",
					confirmButtonClass: 'btn-danger',
					confirm: function(){
						if(modal_id != ""){
							$(modal_id).modal('hide');
						}
					}
				});
			});
		}
	/*************************************************************************************************
	OnPage load get all milestones 
	called by ng-init="get_milestones()"
	*************************************************************************************************/
		$scope.get_milestones = function()
		{
			var final_data = {action: "get_milestones", csrf_gd : Cookies.get('csrf_gd')};
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : $scope.url_get_get_all_milestone,
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
					var milestones_data = response.milestones;
					var count = response.count;	
					var users_count = response.users_count;	
					var organ_users = response.organ_users;
					var user_id = response.user_id;
					
					$scope.user_id = user_id;
					
					Milestone_list.clear().draw();
					
					if(count > 0){
						var x = 0;
						while(x < count){
							$scope.add_milestone_toMilestoneList(milestones_data[x]);
							x++;
						}
					}
					
					/***** 
					Users dropdown default 
					*****/
					var tmp_users = [];
					if(users_count > 0){
						var z = 0;
						while(z < users_count){
							var user_name = organ_users[z]['first_name'] + " " + organ_users[z]['last_name'];
							tmp_users.push({value: organ_users[z]['user_id'], label : user_name});
							z++;
						}
						
						$scope.organ_usersList = tmp_users;	
					}else{
						tmp_users.push({value: 0, label : 'No users' });
						$scope.organ_usersList = tmp_users;
					}
					
					$scope.milestone_owner = getSelectedItem($scope.organ_usersList, {value:user_id});
					$scope.edit_milestone_owner = $scope.organ_usersList[0];	
					if($scope.load_all_tasks == 1){
						$scope.Tasks_list.draw();
					}
					
					if($scope.notif_task_id != 0)
					{
						$("#milestones_tablist a[href='#plain_tab']").tab('show');
					}
			});
		};
	/*************************************************************************************************
	ADD Milestone to Milestone List Datatable
	*************************************************************************************************/
		$scope.add_milestone_toMilestoneList = function(data)
		{
			Milestone_list.row.add([ 	
					data.name, 
					data.id, 
					data.dueDate, 
					data.dueDate_format, 
					data.dueDate_format_string, 
					data.startDate, 
					data.startDate_format,
					data.description, 
					data.owner_id, 
					data.status, 
					"", 
					0,
					data.date_sorter,
					data.taskcount,
					data.bShowOnDash
			]).draw().node();
			
			$scope.initialize_tasksDatatable(data.id);
		};
	/*************************************************************************************************
	UPDATE Milestone to Milestone List Datatable
	*************************************************************************************************/
		$scope.update_milestone_toMilestoneList = function(data)
		{
			var data_count = Object.keys(data).length;
			var data_keys = Object.keys(data);
			var m_id = data.id
			var current_row = angular.element("#milestone_row-"+m_id).parents('tr');
			var row_milestone_id = "#milestone_row-"+m_id;
			
			if( data_count > 0){
				var x = 0;
				while(x < data_count){
					var className = ".m_"+data_keys[x];
					var new_column_data = data[data_keys[x]];
					var milestone_column = angular.element(row_milestone_id).find(className);
					
					if(milestone_column.length > 0){
						update_column = Milestone_list.cell(milestone_column).data(new_column_data); 
					}
					x++
				}
				
				if(data['name'] != undefined){
					var milestone_column = angular.element(row_milestone_id).find(".m_name");
					
					if(milestone_column.length > 0){
						update_column = Milestone_list.cell(milestone_column).data(data['name']); 
					}
				}
				
				/* one time set load_tasks to 0 */
				var milestone_column = angular.element(row_milestone_id).find(".m_loaded_tasks");
					
				if(milestone_column.length > 0){
					update_column = Milestone_list.cell(milestone_column).data(0); 
				}
				
				Milestone_list.draw();
				/* re-initialize tasksDatatable */
				$scope.initialize_tasksDatatable(data.id);	
			}
		};
	/*************************************************************************************************
	Delete Milestone to Milestone List Datatable
	*************************************************************************************************/
		$scope.delete_milestone_toMilestoneList = function(m_id)
		{
			var current_row = angular.element("#milestone_row-"+m_id);
			var rowNode = Milestone_list.rows(current_row).remove().draw();
			
		};
	/*************************************************************************************************
	Update Milestone bShowOnDash in Milestone List Datatable
	*************************************************************************************************/	
	$scope.update_milestone_bShowOnDash_toMilestoneList = function(data)
		{
			var data_count = Object.keys(data).length;
			var data_keys = Object.keys(data);
			var m_id = data.id
			var current_row = angular.element("#milestone_row-"+m_id).parents('tr');
			var row_milestone_id = "#milestone_row-"+m_id;
			
			var className = ".m_bShowOnDash"; 
			var new_column_data = data.bShowOnDash; 
			var milestone_column = angular.element(row_milestone_id).find(className);
			Milestone_list.cell(milestone_column).data(new_column_data); 
			
			if(new_column_data == 1 || new_column_data == "1"){
				var remove_class = "milestone_checkbox";	
				var add_class = "milestone_checkbox_active";	
			}else{
				var remove_class = "milestone_checkbox_active";
				var add_class = "milestone_checkbox";	
			}
			
			angular.element(row_milestone_id).find(".m_name").find(".update_bShowOnDash_btn").removeClass(remove_class);
			angular.element(row_milestone_id).find(".m_name").find(".update_bShowOnDash_btn").addClass(add_class);
		};
/******************************************************************************************************************************************
***
*** MILESTONES TASKS	
***
******************************************************************************************************************************************/	
	/*************************************************************************************************
	Initialize Tasks List datatable per milestone : Inprogress Tasks
	*************************************************************************************************/
		$scope.initialize_tasksDatatable = function(m_id)
		{
			/* Initialize Complete Tasks */
			if($("#db_milestoneTask_complete-"+m_id).length > 0){
				if ( !$.fn.DataTable.isDataTable( '#db_milestoneTask_complete-'+m_id ) ) {
					var table = $("#db_milestoneTask_complete-"+m_id).DataTable($scope.dt_options);
					if($scope.Milestone_tasks_list[m_id] == undefined){
						$scope.Milestone_tasks_list[m_id] = {"complete" : table};
					}else{
						$scope.Milestone_tasks_list[m_id]["complete"] = table;
					}
				}
			}
			
			/* Initialize Inprogress Tasks */
			if($("#db_milestoneTask_inprogress-"+m_id).length > 0){
				if ( !$.fn.DataTable.isDataTable( '#db_milestoneTask_inprogress-'+m_id ) ) {
					var table = $("#db_milestoneTask_inprogress-"+m_id).DataTable($scope.dt_options);
					if($scope.Milestone_tasks_list[m_id] == undefined){
						$scope.Milestone_tasks_list[m_id] = {"inprogress" : table};
					}else{
						$scope.Milestone_tasks_list[m_id]['inprogress'] = table;
					}
				}
			}
		};
	/*************************************************************************************************
	Get tasks under milestone
	*************************************************************************************************/
		$scope.get_milestone_tasks = function(m_id)
		{
				$scope.milestone_task_id = m_id;
				var final_data = {m_id : m_id, action: "get_milestone_tasks", csrf_gd : Cookies.get('csrf_gd')};
				
				$scope.file =  $http({
						method  : 'POST',
						data : $httpParamSerializerJQLike(final_data),
						url     : $scope.url_get_milestone_tasks,
						headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
				}).success(function(response){
						var data = response.milestone_tasks;
						var count = response.count;	
						var m_id = $scope.milestone_task_id
						if($scope.Milestone_tasks_list[m_id] != undefined){
							$scope.Milestone_tasks_list[m_id]['complete'].clear().draw();
							$scope.Milestone_tasks_list[m_id]['inprogress'].clear().draw();
							if(count > 0){
								var x = 0;
								while(x < count){
									$scope.add_task_toTaskDatatables(data[x], "milestone_task", false);
									x++;
								}
							}
						}
						
						/*  Update milestone column to already loaded tasks to TRUE */
						var current_row = angular.element("tr#milestone_row-"+m_id);
						var milestone_row_id = "#milestone_row-"+m_id;
						var milestone_column = angular.element(milestone_row_id).find(".m_loaded_tasks");
						update_column = Milestone_list.cell(milestone_column).data(1); 
				});
		};
	/*************************************************************************************************
	Get milestones from the Milestone_list datatable and convert it for milestones dropdown list
	*************************************************************************************************/
		$scope.get_milestones_dropdownList = function()
		{
			var tmp_milestones = [];
			var milestone_rows = Milestone_list.rows().data();
			
			tmp_milestones.push({value: 0, label : '' });
			
			if(milestone_rows.length > 0){
				var x = 0;
				while(x < milestone_rows.length){
					tmp_milestones.push({value: milestone_rows[x][1], label : milestone_rows[x][0] });
					x++;
				}
			}
			$scope.milestonesDropdownList = tmp_milestones;
		}
/******************************************************************************************************************************************
***
***  TASKS	
***
******************************************************************************************************************************************/	
	/* Inline edit owner */
	$("#milestone_tabs_container").on("change", ".task_dropdown_owner", function()
	{
		var current_row = angular.element(this).parents('tr');
		var table_name = current_row.attr("data_table_name");
		var task_id = $(this).attr('data-task_id');
		var selected_owner_id = $(this).val();
		var data = { action : "edit_task_owner", task_id : task_id, owner_id : selected_owner_id };
		
		$scope.process_Task(data, "inline_edit_owner", "", $scope.url_inline_edit_owner, table_name);
	});
	
	/* Inline edit status */
	$("#milestone_tabs_container").on("change", ".task_dropdown_status", function()
	{
		var current_row = angular.element(this).parents('tr');
		var table_name = current_row.attr("data_table_name");
		var task_id = $(this).attr('data-task_id');
		if(table_name == "task"){
			var row_data = $scope.Tasks_list.row( current_row ).data();
			
			var status_id = row_data[11];
		}else{
			var m_id = current_row.attr("data_m_id");
			var status_type = current_row.attr("data_status_type");
			var row_data = $scope.Milestone_tasks_list[m_id][status_type].row( current_row ).data();
			var status_id = row_data[11];
		}
		
		var selected_status = $(this).val();
		var data = { action : "edit_task_status", task_id : task_id, status : selected_status };
		
		if(status_id == 10){
			var status_type = "complete";
		}else{
			var status_type = "inprogress";
		}
		
		$scope.temp_inline_edit_status_type = status_type;
		$scope.process_Task(data, "inline_edit_status", "", $scope.url_inline_edit_status, "task");
	});
	
	/* Inline edit priority */
	$("#milestone_tabs_container").on("change", ".task_dropdown_priority", function()
	{
		var current_row = angular.element(this).parents('tr');
		var table_name = current_row.attr("data_table_name");
		var task_id = $(this).attr('data-task_id');
		var selected_priority = $(this).val();
		var data = { action : "edit_task_priority", task_id : task_id, priority : selected_priority };
		
		$scope.process_Task(data, "inline_edit_priority", "", $scope.url_inline_edit_priority, table_name);
	});
	
/* OnClick */	
	/*************************************************************************************************
	On Click Plain Tab
	*************************************************************************************************/
		$( "#milestones_tablist a[href='#plain_tab']" ).on('show.bs.tab', function (e) 
		{
			if($scope.load_all_tasks == 0){
				$scope.get_tasks("plain");	
				$scope.load_plain_tab = 1;
			}
		});
	/*************************************************************************************************
	On Click Kanban Tab
	*************************************************************************************************/
		$( "#milestones_tablist a[href='#kanban_tab']" ).on('show.bs.tab', function (e) 
		{
			if($scope.load_all_tasks == 0){
				$scope.get_tasks("kanban");	
			}else{
				$scope.kanban_filter_tasks();
			}
		});	
	/*************************************************************************************************
	On Click open Add Task modal
	*************************************************************************************************/
		angular.element('#add_task_modal').on('show.bs.modal', function(e) 
		{
			$scope.is_add = true;
			$scope.is_edit = false;
			$scope.is_delete = false;
			
			$scope.get_milestones_dropdownList();
			$scope.participants = [];
			
			var this_button = angular.element(e.relatedTarget);
			var table_name = this_button.attr('data-table_name');
			var section_target = this_button.attr('data-section_target');
			var m_id = $scope.milestonesDropdownList[0];
			var priority = $scope.priorityList[0];
			var status = $scope.statusList[0];
			
			
			if(section_target == "milestone_task"){
				var current_row = angular.element(e.relatedTarget).parents('tr');
				var row_data = Milestone_list.row( current_row ).data();
				var m_id = getSelectedItem($scope.milestonesDropdownList, {value:row_data[1]});
			}else if(section_target == "kanban-priority"){
				var priority_val = this_button.attr('data-priority');
				var priority = getSelectedItem($scope.priorityList, {value:priority_val});
				var table_name = "task";
				
			}else if(section_target == "kanban-percentage"){
				var status_val = this_button.attr('data-status');
				var status = getSelectedItem($scope.statusList, {value:status_val});
				var table_name = "task";
				
			}else if(section_target == "kanban-milestone"){
				var data_m_id = this_button.attr('data-m_id');
				var m_id = getSelectedItem($scope.milestonesDropdownList, {value:data_m_id});
				var table_name = "task";
			
			}
			
			$scope.$apply(function(){
				$scope.task_name = "";
				$scope.task_m_id = m_id;
				$scope.task_owner = getSelectedItem($scope.organ_usersList, {value:$scope.user_id}); 
				$scope._multipleUser.user_task = ""; 
				$scope.task_description = "";
				$scope.task_priority = priority;
				$scope.task_status = status;
				
				$scope.temp_add_task_table_name = table_name;
			});
			
			angular.element("#task_start_date").val("");
			angular.element("#task_due_date").val("");
		});
	/*************************************************************************************************
	On Click open Edit Task modal
	*************************************************************************************************/
		angular.element('#edit_task_modal').on('show.bs.modal', function(e) 
		{
			$scope.is_add = false;
			$scope.is_edit = true;
			$scope.is_delete = false;
			$scope.get_milestones_dropdownList();
			$scope.edit_participants = [];
			
			var this_button = angular.element(e.relatedTarget);
			var section_target = this_button.attr("data-section_target");
			
			
			if(section_target == "milestone_task" || section_target == "task"){
				var current_row = this_button.parents('tr');
				var table_name = current_row.attr('data_table_name');
			}else{
				var task_id = this_button.attr("data-task_id");
				var current_row = angular.element("#task_row-"+task_id);
				var table_name = "task";
			}
			
			if(table_name == "task"){
				var row_data = $scope.Tasks_list.row( current_row ).data();
				var m_id = row_data[14];
			}else{
				var current_row_id = current_row.attr('id');
				var task_id = current_row.attr('data_task_id');
				var m_id = current_row.attr('data_m_id');
				var status_type = current_row.attr('data_status_type');
				
				var row_data = $scope.Milestone_tasks_list[m_id][status_type].row(current_row).data();
			}
			
			var comment_count = row_data[1];
			var task_id = row_data[0];
			var task_name = row_data[2];
			var startDate = row_data[4];
			var startDate_format = row_data[5];
			var dueDate = row_data[7];
			var dueDate_format = row_data[8];
			var dueDate_format_final  = row_data[9];
			var owner_id = row_data[10];
			var status_id = row_data[11];
			var priority_id = row_data[12];
			var participant_id = row_data[13];
			
			var tmp_m_id = getSelectedItem($scope.milestonesDropdownList, {value:m_id});
			var tmp_owner = getSelectedItem($scope.organ_usersList, {value:owner_id});
			if(tmp_m_id != undefined){
				var edit_task_m_id = tmp_m_id;
			}else{
				var edit_task_m_id = $scope.milestonesDropdownList[0];
			}
			if(tmp_owner != undefined){
				var edit_task_owner = tmp_owner;
			}else{
				var edit_task_owner = $scope.organ_usersList[0];
			}
			$scope.$apply(function(){
				$scope.edit_task_id = task_id;
				$scope.edit_task_name = task_name;
				
				$scope.edit_task_description = row_data[3];
				$scope.edit_task_m_id = edit_task_m_id;
				$scope.edit_task_owner = edit_task_owner; 
				$scope.edit_task_status = getSelectedItem($scope.statusList, {value:status_id});  
				$scope.edit_task_priority = getSelectedItem($scope.priorityList, {value:priority_id});  
				
				angular.forEach( participant_id ,function(participant){
							angular.forEach($scope.organ_usersList,function(_user){
								if(_user.value == participant){
									$scope.edit_participants.push(_user);
								}
							});
						});	
				
				$scope._edit_multipleUser.user_task = $scope.edit_participants; 
				
				if(status_id == 10){
					var status_type = "complete";
				}else{
					var status_type = "inprogress";
				}
				$scope.temp_edit_task_id = task_id;
				$scope.temp_edit_task_m_id = m_id;
				$scope.temp_edit_task_status_type = status_type;
				$scope.temp_edit_task_status_id = status_id;
				$scope.temp_edit_task_priority_id = priority_id;
				$scope.temp_edit_section_target = section_target;
			});
			
			if(startDate == null || startDate == '0000-00-00'){
					angular.element('#edit_task_start_date').val('');
			}else{
				angular.element('#edit_task_start_date').val(startDate_format);
			}
			
			if(dueDate_format_final == '9999-99-99'){
				angular.element('#edit_task_due_date').val('');
			}else{
				angular.element('#edit_task_due_date').val(dueDate_format);
			}
			
			$scope.task_counter = 0;
			$scope.task_comments = [];
			/* if(comment_count > 0){ */
				$scope.get_task_comments(task_id);
			/* } */
		});
	/*************************************************************************************************
	On Click Delete Task : Show Delete Task Modal
	*************************************************************************************************/
		angular.element('#delete_task_modal').on('show.bs.modal', function(e) 
		{
			var current_row = angular.element(e.relatedTarget).parents('tr');
			var table_name = current_row.attr('data_table_name');
			var task_id = 0;
			var m_id = 0;
			
			if(table_name == "task"){
				var row_data = $scope.Tasks_list.row( current_row ).data();	
				var task_id = row_data[0];
				var m_id = row_data[14];
				var status = row_data[11];
				var priority = row_data[11];
				if(status == 10){
					var status_type = "complete";
				}else{
					var status_type = "inprogress";
				}
			}else{
				var current_row_id = current_row.attr('id');
				var id_split = current_row_id.split("-");
				
				var task_id = id_split[1];
				var m_id = current_row.attr('data_m_id');
				var status_type = current_row.attr('data_status_type');
				if( $scope.Milestone_tasks_list[m_id][status_type] != undefined){
					var row_data = $scope.Milestone_tasks_list[m_id][status_type].row( current_row ).data();		
					var task_id = row_data[0];
					var status = row_data[11];
					var priority = row_data[11];
				}
				
			}
			
			$scope.temp_delete_task_id = task_id;
			$scope.temp_delete_task_status_id = status;
			$scope.temp_delete_task_priority_id = priority;
			$scope.temp_delete_task_m_id = m_id;
			$scope.temp_delete_task_status_type = status_type;
			$scope.temp_delete_task_table_name = table_name;
		});
	/*************************************************************************************************
	On Click Save Task button 
	*************************************************************************************************/
		$scope.save_task = function()
		{
			$scope.participants = [];
			/* start date */
			if(angular.element('#task_start_date').val() != ''){
				var temp_start_date  =  angular.element('#task_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#task_due_date').val() != ''){
				var temp_due_date = angular.element('#task_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			angular.forEach($scope._multipleUser.user_task,function(user){
				$scope.participants.push(user.value);	
			});
				
			var data = {
				action : "add_task",
				name: $scope.task_name,
				m_id: $scope.task_m_id.value,
				owner_id: $scope.task_owner.value,
				participants: $scope.participants,
				description: $scope.task_description,
				start_date: start_date,
				due_date : due_date,
				priority: $scope.task_priority.value,
				status: $scope.task_status.value
			};
			var has_error = $scope.validate_Task("add", $scope.task_name, start_date, due_date, start_date_comparison, due_date_comparison, $scope.task_status.value, $scope.task_priority.value);
			if(has_error == false){
				$scope.process_Task(data, "add", $scope.id_add_task_modal, $scope.url_add_task, $scope.temp_add_task_table_name);
			}
		}
	/*************************************************************************************************
	VALIDATE Milestone : Add, Edit
	*************************************************************************************************/	
		$scope.validate_Task = function(process_type, t_name, t_start_date, t_due_date, start_date_comparison, due_date_comparison, t_status, t_priority)
		{
			$scope.isopen_task_name = false;
			$scope.isopen_edit_task_name = false;
			$scope.isopen_due_date = false;
			$scope.isopen_edit_due_date = false;
			
			
			var error = false;
			if(parseInt(t_status) < 0 || parseInt(t_status) > 10){
				error = true;
			}
			
			if(parseInt(t_priority) < 1 || parseInt(t_priority) > 4){
				error = true;
			}
			
			if(t_name == ""){
				error = true;
				if(process_type == "add"){
					$scope.isopen_task_name = true;
					$scope._error_task_name = "Task Name is required";
				}else{
					$scope.isopen_edit_task_name = true;
					$scope._error_edit_task_name = "Task Name is required";
				}
			}
			
			if(t_start_date != null && t_due_date != null && t_start_date != t_due_date && start_date_comparison > due_date_comparison){
				error = true;
				if(process_type == "add"){
					$scope.isopen_due_date = true;
					$scope._error_t_due_date = "Start date is Greater than Due date.";
				}else{
					$scope.isopen_edit_due_date = true;
					$scope._error_edit_t_due_date = "Start date is Greater than Due date.";
				}
			}
			
			return error;	
		}	
	/*************************************************************************************************
	On Click Update Task button 
	*************************************************************************************************/
		$scope.update_task = function()
		{
			$scope.edit_participants = [];
			
			/* start date */
			if(angular.element('#edit_task_start_date').val() != ''){
				var temp_start_date  =  angular.element('#edit_task_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#edit_task_due_date').val() != ''){
				var temp_due_date = angular.element('#edit_task_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			angular.forEach($scope._edit_multipleUser.user_task,function(user){
				$scope.edit_participants.push(user.value);	
			});
				
			var data = {
				action : "edit_task",
				name: $scope.edit_task_name,
				task_id: $scope.edit_task_id,
				m_id: $scope.edit_task_m_id.value,
				owner_id: $scope.edit_task_owner.value,
				participants: $scope.edit_participants,
				description: $scope.edit_task_description,
				start_date: start_date,
				due_date : due_date,
				priority: $scope.edit_task_priority.value,
				status: $scope.edit_task_status.value
			};
			var has_error = $scope.validate_Task("edit", $scope.edit_task_name, start_date, due_date, start_date_comparison, due_date_comparison, $scope.edit_task_status.value, $scope.edit_task_priority.value);
			if(has_error == false){
				$scope.process_Task(data, "edit", $scope.id_edit_task_modal, $scope.url_edit_task, "task");
			}
		}
	/*************************************************************************************************
	On Click Delete Task button 
	*************************************************************************************************/
		$scope.delete_task = function()
		{
			var data = { action : "delete_task", task_id: $scope.temp_delete_task_id };
			$scope.process_Task(data, "delete", $scope.id_delete_task_modal, $scope.url_delete_task, "task");
		};
	/*************************************************************************************************
	OnPage load get all tasks 
	called by ng-init="get_tasks(tab_id)"
	*************************************************************************************************/
	$scope.get_tasks = function(tab_id)
	{
		var final_data = {action: "get_tasks", csrf_gd : Cookies.get('csrf_gd')};
		$scope.file =  $http({
				method  : 'POST',
				data : $httpParamSerializerJQLike(final_data),
				url     : $scope.url_get_tasks,
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
				var tasks_data = response.tasks;
				var count = response.count;	
				var users_count = response.users_count;	
				var organ_users = response.organ_users;
				
				$scope.Tasks_list.clear().draw();
				
				if(count > 0){
					var x = 0;
					while(x < count){
						/* call function to add task to Milestone List / Milestone Datatable */
						$scope.add_task_toTaskDatatables(tasks_data[x], "task", false);
						x++;
					}
				}
				
				$scope.load_all_tasks = 1;
				
				if(tab_id == "kanban"){
					$scope.kanban_filter_tasks();
				}
		});
	};		
/******************************************************************************************************************************************
***
*** Process POST of TASKS/Milestone TASKS : Add, Edit, Delete
***
******************************************************************************************************************************************/		
	$scope.process_Task =  function(data, process_type, modal_id, url, table_name){
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var final_data = angular.extend(data, csrf_object);
		
		$scope.file =  $http({
				method  : 'POST',
				data : $httpParamSerializerJQLike(final_data),
				url     : url,
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.error == 0){
				var data = response.task;
				
				switch(process_type ){
					case "add" : 
						$scope.add_task_toTaskDatatables(data, table_name, true);
					break;
					case "edit":
						$scope.update_task_toTaskDatatables(data);
					break;
					case "delete":
						$scope.delete_task_toTaskDatatables(data);
					break;
					case "inline_edit_owner":
						$scope.update_inline_task_toTaskDatatables(data, "owner_id");
					break;
					case "inline_edit_status":
						$scope.update_inline_task_toTaskDatatables(data, "status");
					break;
					case "inline_edit_priority":
						$scope.update_inline_task_toTaskDatatables(data, "priority");
					break;
				}
				$.alert({
					title: 'Success',
					content: response.message,
					confirmButtonClass: 'btn-success',
					confirm: function(){
						if(modal_id != ""){
							$(modal_id).modal('hide');
						}
					}
				});
			}else{
				$.alert({
					title: 'Error',
					content: response.message,
					confirmButtonClass: 'btn-danger',
					confirm: function(){
						if(modal_id != ""){
							$(modal_id).modal('hide');
						}
					}
				});
			}
		}).error(function(){
			$.alert({
				title: 'Error',
				content: "Failed to process operation.",
				confirmButtonClass: 'btn-danger',
				confirm: function(){
					if(modal_id != ""){
						$(modal_id).modal('hide');
					}
				}
			});
		});
	}	
/******************************************************************************************************************************************
***
***  TASKS/Milestone TASKS DATATABLES 	
***
******************************************************************************************************************************************/	
	
	/*************************************************************************************************
	INLINE UPDATE Task from Task List Datatable
	*************************************************************************************************/
		$scope.update_inline_task_toTaskDatatables = function(data, column)
		{
			var data_count = Object.keys(data).length;
			var data_keys = Object.keys(data);
			var task_id = data.task_id
			var current_row = angular.element("#task_row-"+task_id).parents('tr');
			var row_task_id = "#task_row-"+task_id;
			var row_milestone_task_id = "#milestone_task_row-"+task_id;
			
			if( data_count > 0){
				if(data['status'] == 10){
					var status_type = "complete";	
					var temp_edit_task_status = "inprogress";
				}else{
					var status_type = "inprogress";
					var temp_edit_task_status = "complete";
				}
				
				var new_column_data = data[column];
				var task_column = angular.element(row_task_id).find(".t_"+column);
				
				if(task_column.length > 0)
				{
					$scope.Tasks_list.cell(task_column).data(new_column_data); 
				}
					
				/* Update task in Tasks List datatable here */
				if(column != "status" || (column == "status" && $scope.temp_inline_edit_status_type == status_type) )
				{
					var milestone_task_column = angular.element(row_milestone_task_id).find("td.m_task_"+column);
					if(milestone_task_column.length > 0){
						$scope.Milestone_tasks_list[data['milestone_id']][status_type].cell(milestone_task_column).data(new_column_data); 
					}
				}
				else
				{
					var milestone_task_row = angular.element(row_milestone_task_id);
					if(milestone_task_row.length > 0){
						var MilestoneTasks_rowNode = $scope.Milestone_tasks_list[data['milestone_id']][temp_edit_task_status].rows(milestone_task_row).remove().draw();
					}
					
					$scope.add_task_toTaskDatatables(data, "milestone_task", false);
				}
			}
		};
	/*************************************************************************************************
	Add Task from Tasks/Milestones Tasks Datatable
	*************************************************************************************************/
		$scope.add_task_toTaskDatatables = function(data, table_name, add_to_other_table)
		{
			var row_data = [
					data.task_id, 
					data.comment_count, 
					data.task_name, 
					data.task_description, 
					data.task_startDate, 
					data.task_startDate_format,
					data.task_startDate_format_final,
					data.task_dueDate,
					data.task_dueDate_format,
					data.task_dueDate_format_final, 
					data.owner_id,
					data.status, 
					data.priority, 
					data.participant_id, 
					data.milestone_id, 
					data.task_id
				];
			if(table_name == "task"){
				var rowNode = $scope.Tasks_list.row.add(row_data).draw().node();	
				if(angular.element("#taskdatatable_start_date-"+data.task_id).length > 0){
					startdatepicker(angular.element('#taskdatatable_start_date-'+data.task_id) );
					if(data.task_startDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_startDate_format;
					}
					$('#taskdatatable_start_date-'+data.task_id).val(value);
				}
				if(angular.element("#taskdatatable_due_date-"+data.task_id).length > 0){
					duedatepicker(angular.element('#taskdatatable_due_date-'+data.task_id) );
					if(data.task_dueDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_dueDate_format;
					}
					$('#taskdatatable_due_date-'+data.task_id).val(value);
				}
				
				if(add_to_other_table == true && $scope.Milestone_tasks_list[data.milestone_id] != undefined){
					/* Check if Milestone exist then add the task to the milestone */
					if(data.status == 10){
						var rowNode = $scope.Milestone_tasks_list[data.milestone_id]['complete'].row.add( row_data).draw().node();
					}else{
						var rowNode = $scope.Milestone_tasks_list[data.milestone_id]['inprogress'].row.add( row_data).draw().node();
					}	
					if(angular.element("#milestone_taskdatatable_start_date-"+data.task_id).length > 0){
						startdatepicker(angular.element('#milestone_taskdatatable_start_date-'+data.task_id) );
						if(data.task_startDate_format_final == "9999-99-99"){
							var value = "-";
						}else{
							var value = data.task_startDate_format;
						}
						$('#milestone_taskdatatable_start_date-'+data.task_id).val(value);
					}
					if(angular.element("#milestone_taskdatatable_due_date-"+data.task_id).length > 0){
						duedatepicker(angular.element('#milestone_taskdatatable_due_date-'+data.task_id) );
						if(data.task_dueDate_format_final == "9999-99-99"){
							var value = "-";
						}else{
							var value = data.task_dueDate_format;
						}
						$('#milestone_taskdatatable_due_date-'+data.task_id).val(value);
					}
				}
				
			}else{
				if($scope.Milestone_tasks_list[data.milestone_id] != undefined){
					if(data.status == 10){
						var rowNode = $scope.Milestone_tasks_list[data.milestone_id]['complete'].row.add( row_data).draw().node();
					}else{
						var rowNode = $scope.Milestone_tasks_list[data.milestone_id]['inprogress'].row.add( row_data).draw().node();
					}
					
					if(angular.element("#milestone_taskdatatable_start_date-"+data.task_id).length > 0){
						startdatepicker(angular.element('#milestone_taskdatatable_start_date-'+data.task_id));
						if(data.task_startDate_format_final == "9999-99-99"){
							var value = "-";
						}else{
							var value = data.task_startDate_format;
						}
						$('#milestone_taskdatatable_start_date-'+data.task_id).val(value);
					}
					if(angular.element("#milestone_taskdatatable_due_date-"+data.task_id).length > 0){
						duedatepicker(angular.element('#milestone_taskdatatable_due_date-'+data.task_id), data.task_id );
						if(data.task_dueDate_format_final == "9999-99-99"){
							var value = "-";
						}else{
							var value = data.task_dueDate_format;
						}
						$('#milestone_taskdatatable_due_date-'+data.task_id).val(value);
					}
				}
				
				if(add_to_other_table == true){
					var rowNode = $scope.Tasks_list.row.add(row_data).draw().node();	
					if(angular.element("#taskdatatable_start_date-"+data.task_id).length > 0){
						startdatepicker(angular.element('#taskdatatable_start_date-'+data.task_id));
						if(data.task_startDate_format_final == "9999-99-99"){
							var value = "-";
						}else{
							var value = data.task_startDate_format;
						}
						$('#taskdatatable_start_date-'+data.task_id).val(value);
					}
					if(angular.element("#taskdatatable_due_date-"+data.task_id).length > 0){
						duedatepicker(angular.element('#taskdatatable_due_date-'+data.task_id), data.task_id );
						if(data.task_dueDate_format_final == "9999-99-99"){
							var value = "-";
						}else{
							var value = data.task_dueDate_format;
						}
						$('#taskdatatable_due_date-'+data.task_id).val(value);
					}
				}
			}	
			
			if(	$scope.load_all_tasks == 1 && $scope.is_add == true && $scope.load_kanban_tab == 1)
			{
				var owner_index = getSelectedItem($scope.organ_usersList, {value:data.owner_id});
				var task = {
					'id' : data.task_id,
					'task_id' : data.task_id,
					'comment_counter' : data.comment_counter,
					'task_name' : data.task_name,
					'owner_name' : owner_index.label
				};
				
				/* add to filter status */
				switch(data.status){
					case "0" : $scope.filter_0.push(task); break;
					case "1" : $scope.filter_1.push(task); break;
					case "2" : $scope.filter_2.push(task); break;
					case "3" : $scope.filter_3.push(task); break;
					case "4" : $scope.filter_4.push(task); break;
					case "5" : $scope.filter_5.push(task); break;
					case "7" : $scope.filter_7.push(task); break;
					case "6" : $scope.filter_6.push(task); break;
					case "8" : $scope.filter_8.push(task); break;
					case "9" : $scope.filter_9.push(task); break;
					case "10": $scope.filter_10.push(task); break;
				}
				
				/* add to filter priority */
				$scope.add_filter_priority(data.priority, task);
				
				/* add to filter milestone */
				$scope.add_filter_milestone(data.milestone_id, task);
			
			}	
		};
		
		
		function startdatepicker(datepicker_id){
			var is_initialize = $(datepicker_id).attr('data-initialize');
			if(is_initialize == 0 || is_initialize == "0"){
				$(datepicker_id).datetimepicker({
					format: "DD/MM/YYYY",
					useCurrent: false
				}).on("dp.change", function(e) {
					var defaultdate = $(e.currentTarget).attr('data-defaultdate');
					var isclicked = $(e.currentTarget).attr('data-isclicked');
					$(datepicker_id).attr('data-isclicked', 1);
					
					if((e.oldDate == null && defaultdate == "-") || (isclicked == "1" || isclicked == 1)){
						var date = $(e.currentTarget).data('date');
						var id =  $(e.currentTarget).attr('data-task_id');
						var second  =  date.split(/\//);
						var temp_due_date = [ second[2], second[1], second[0] ].join('-');
						var final_data = {
							task_id : id,
							date: temp_due_date,
							action: "edit_task_start_date",
							csrf_gd : Cookies.get('csrf_gd')
						};
						$http({ method  : 'POST',
							url     : $scope.url_inline_edit_startDate,
							data    : $httpParamSerializerJQLike(final_data), 
							headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
						}).success(function(data){
								if(data.error == 1){
									$.alert({
										title: 'Error',
										content: data.message,
										confirmButtonClass: 'btn-danger',
									});
								}else{
									var task = data.task;
									
									var row_task_id = "#task_row-"+task['task_id'];
									var row_milestone_task_id = "#milestone_task_row-"+task['task_id'];
									var status_type = "inprogress";
									if(task['status'] == 10){
										var status_type = "complete";
									}
									
									var task_row = angular.element(row_task_id);
									var milestone_task_row = angular.element(row_milestone_task_id);
									if(task_row.length > 0){
										$scope.Tasks_list.rows(task_row).remove().draw();
										$scope.add_task_toTaskDatatables(task, "task", false);	
										if(task.task_startDate_format_final == "9999-99-99"){
											var value = "-";
										}else{
											var value = task.task_startDate_format;
										}
										$('#taskdatatable_start_date-'+task.task_id).val(value);
									}
									if(milestone_task_row.length > 0){
										if($scope.Milestone_tasks_list[task['milestone_id']] != undefined){
											$scope.Milestone_tasks_list[task['milestone_id']][status_type].rows(milestone_task_row).remove().draw();
											
											$scope.add_task_toTaskDatatables(task, "milestone_task", false);	
											if(task.task_startDate_format_final == "9999-99-99"){
												var value = "-";
											}else{
												var value = task.task_startDate_format;
											}
											$('#milestone_taskdatatable_start_date-'+task.task_id).val(value);
										}
									}
									
									$.alert({
										title: 'Success',
										content: 'Start Date is updated.',
										confirmButtonClass: 'btn-success'
									});
								}
						});
						
					}else{
						$(datepicker_id).attr('data-isclicked', 1);
					}
					
				});
				$(datepicker_id).attr('data-initialize', 1);
			}
				
			
		}
		function duedatepicker(datepicker_id){
				$(datepicker_id).datetimepicker({
					format: "DD/MM/YYYY",
					useCurrent: false
				}).on("dp.change", function(e) {
					var defaultdate = $(e.currentTarget).attr('data-defaultdate');
					var isclicked = $(e.currentTarget).attr('data-isclicked');
					
					$(datepicker_id).attr('data-isclicked', 1);
					
					if((e.oldDate == null && defaultdate == "-") || (isclicked == "1" || isclicked == 1)){
						var date = $(e.currentTarget).data('date');
						var id =  $(e.currentTarget).attr('data-task_id');
						var second  =  date.split(/\//);
						var temp_due_date = [ second[2], second[1], second[0] ].join('-');
						var final_data = {
							task_id : id,
							date: temp_due_date,
							action: "edit_task_due_date",
							csrf_gd : Cookies.get('csrf_gd')
						};
						$http({ method  : 'POST',
							url     : $scope.url_inline_edit_dueDate,
							data    : $httpParamSerializerJQLike(final_data), 
							headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
						}).success(function(data){
								
								if(data.error == 1){
									$.alert({
										title: 'Error',
										content: data.message,
										confirmButtonClass: 'btn-danger',
									});
								}else{
									var task = data.task;
									var row_task_id = "#task_row-"+task['task_id'];
									var row_milestone_task_id = "#milestone_task_row-"+task['task_id'];
									var status_type = "inprogress";
									if(task['status'] == 10){
										var status_type = "complete";
									}
									
									var task_row = angular.element(row_task_id);
									var milestone_task_row = angular.element(row_milestone_task_id);
									if(task_row.length > 0){
										$scope.Tasks_list.rows(task_row).remove().draw();
										$scope.add_task_toTaskDatatables(task, "task", false);	
										if(task.task_dueDate_format_final == "9999-99-99"){
											var value = "-";
										}else{
											var value = task.task_dueDate_format;
										}
										$('#taskdatatable_due_date-'+task.task_id).val(value);
									}
									if(milestone_task_row.length > 0){
										if($scope.Milestone_tasks_list[task['milestone_id']] != undefined){
											$scope.Milestone_tasks_list[task['milestone_id']][status_type].rows(milestone_task_row).remove().draw();
											
											$scope.add_task_toTaskDatatables(task, "milestone_task", false);	
											if(task.task_dueDate_format_final == "9999-99-99"){
												var value = "-";
											}else{
												var value = task.task_dueDate_format;
											}
											$('#milestone_taskdatatable_due_date-'+task.task_id).val(value);
										}
									}
									
									$.alert({
										title: 'Success',
										content: 'Due Date is updated.',
										confirmButtonClass: 'btn-success',
									});
								}
						});
						
						
					}else{
						$(datepicker_id).attr('data-isclicked', 1);
					}
					
				});
				
			
		}
	/*************************************************************************************************
	UPDATE Task from Tasks/Milestones Tasks Datatable
	*************************************************************************************************/
		$scope.update_task_toTaskDatatables = function(data)
		{
			var data_count = Object.keys(data).length;
			var data_keys = Object.keys(data);
			
			var task_id = data.task_id
			var row_task_id = "#task_row-"+task_id;
			var row_milestone_task_id = "#milestone_task_row-"+task_id;
			
			if( data_count > 0)
			{
				/* Update task in Tasks List*/
				var x = 0;
				while(x < data_count)
				{
					var task_column = angular.element(row_task_id).find(".t_"+data_keys[x]);
					if(task_column.length > 0){
						$scope.Tasks_list.cell(task_column).data(data[data_keys[x]]); 
					}
					x++;
				}
				
				/* Update task in Milestone Tasks List*/
				if(data['status'] == 10){
					var status_type = "complete";	
				}else{
					var status_type = "inprogress";
				}
				
				var temp_edit_m_id = $scope.temp_edit_task_m_id;
				var temp_edit_task_status = $scope.temp_edit_task_status_type;
					
				if(temp_edit_m_id == data['milestone_id'] && temp_edit_m_id != 0 && temp_edit_task_status == status_type)
				{
					var x = 0;
					while(x < data_count)
					{
						var milestone_task_column = angular.element(row_milestone_task_id).find(".m_task_"+data_keys[x]);
						if(milestone_task_column.length > 0){
							$scope.Milestone_tasks_list[data['milestone_id']][status_type].cell(milestone_task_column).data(data[data_keys[x]]); 
						}
						x++;
					}
				}
				else
				{
					var milestone_task_row = angular.element(row_milestone_task_id);
					if(milestone_task_row.length > 0){
						var MilestoneTasks_rowNode = $scope.Milestone_tasks_list[temp_edit_m_id][temp_edit_task_status].rows(milestone_task_row).remove().draw();
					}
					
					$scope.add_task_toTaskDatatables(data, "milestone_task", false);
					
				}
				/* task */
				if(angular.element("#taskdatatable_start_date-"+data.task_id).length > 0){
					startdatepicker(angular.element('#taskdatatable_start_date-'+data.task_id));
					if(data.task_startDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_startDate_format;
					}
					$('#taskdatatable_start_date-'+data.task_id).val(value);
				}
				if(angular.element("#taskdatatable_due_date-"+data.task_id).length > 0){
					duedatepicker(angular.element('#taskdatatable_due_date-'+data.task_id));
					if(data.task_dueDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_dueDate_format;
					}
					$('#taskdatatable_due_date-'+data.task_id).val(value);
				}
				/* milestone task */
				if(angular.element("#milestone_taskdatatable_start_date-"+data.task_id).length > 0){
					startdatepicker(angular.element('#milestone_taskdatatable_start_date-'+data.task_id));
					if(data.task_startDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_startDate_format;
					}
					$('#milestone_taskdatatable_start_date-'+data.task_id).val(value);
				}
				if(angular.element("#milestone_taskdatatable_due_date-"+data.task_id).length > 0){
					duedatepicker(angular.element('#milestone_taskdatatable_due_date-'+data.task_id));
					if(data.task_dueDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_dueDate_format;
					}
					$('#milestone_taskdatatable_due_date-'+data.task_id).val(value);
				}
				if(	$scope.load_all_tasks == 1 && $scope.load_kanban_tab == 1)
				{
					var owner_index = getSelectedItem($scope.organ_usersList, {value:data['owner_id']});
					var task = {
						'id' : data['task_id'],
						'task_id' : data['task_id'],
						'comment_counter' : data['comment_counter'],
						'task_name' : data['task_name'],
						'owner_name' : owner_index.label
					};
					
					if($scope.temp_edit_task_status_id != data['status'])
					{
						$scope.remove_from_filter_status(data['task_id'], $scope.temp_edit_task_status_id);
						switch(data.status){
							case "0" : $scope.filter_0.push(task); break;
							case "1" : $scope.filter_1.push(task); break;
							case "2" : $scope.filter_2.push(task); break;
							case "3" : $scope.filter_3.push(task); break;
							case "4" : $scope.filter_4.push(task); break;
							case "5" : $scope.filter_5.push(task); break;
							case "7" : $scope.filter_7.push(task); break;
							case "6" : $scope.filter_6.push(task); break;
							case "8" : $scope.filter_8.push(task); break;
							case "9" : $scope.filter_9.push(task); break;
							case "10": $scope.filter_10.push(task); break;
						}
					}else{
						switch(data.status){
							case "0" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_0,{task_id:data.task_id} );
								if($scope.filter_0[task_index] != undefined){
									$scope.filter_0[task_index] = task;
								}
							break;
							case "1" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_1,{id:data.task_id} );
								if($scope.filter_1[task_index] != undefined){
									$scope.filter_1[task_index] = task;
								}
							break;
							case "2" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_2,{task_id:data.task_id} );
								if($scope.filter_2[task_index] != undefined){
									$scope.filter_2[task_index] = task;
								}
							break;
							case "3" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_3,{task_id:data.task_id} );
								if($scope.filter_3[task_index] != undefined){
									$scope.filter_3[task_index] = task;
								}
							break;
							case "4" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_4,{task_id:data.task_id} );
								if($scope.filter_4[task_index] != undefined){
									$scope.filter_4[task_index] = task;
								}
							break;
							case "5" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_5,{task_id:data.task_id} );
								if($scope.filter_5[task_index] != undefined){
									$scope.filter_5[task_index] = task;
								}
							break;
							case "6" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_6,{task_id:data.task_id} );
								if($scope.filter_6[task_index] != undefined){
									$scope.filter_6[task_index] = task;
								}
							break;
							case "7" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_7,{task_id:data.task_id} );
								if($scope.filter_7[task_index] != undefined){
									$scope.filter_7[task_index] = task;
								}
							break;
							case "8" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_8,{task_id:data.task_id} );
								if($scope.filter_8[task_index] != undefined){
									$scope.filter_8[task_index] = task;
								}
							break;
							case "9" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_9,{task_id:data.task_id} );
								if($scope.filter_9[task_index] != undefined){
									$scope.filter_9[task_index] = task;
								}
							break;
							case "10": 
								var task_index = $filter('GetIndexNumeric')($scope.filter_10,{task_id:data.task_id} );
								if($scope.filter_10[task_index] != undefined){
									$scope.filter_10[task_index] = task;
								}
							break;
						}
					}
					
					if($scope.temp_edit_task_priority_id != data.priority)
					{
						$scope.remove_from_filter_priority(data.task_id, $scope.temp_edit_task_priority_id);
						$scope.add_filter_priority(data.priority, task);
					}else{
						switch(data.priority){
							case "1" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_nones,{task_id:data.task_id} );
								if($scope.filter_nones[task_index] != undefined){
									$scope.filter_nones[task_index] = task;
								}
							break;
							case "2" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_lows,{task_id:data.task_id} );
								if($scope.filter_lows[task_index] != undefined){
									$scope.filter_lows[task_index] = task;
								}
							break;
							case "3" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_mediums,{task_id:data.task_id} );
								if($scope.filter_mediums[task_index] != undefined){
									$scope.filter_mediums[task_index] = task;
								}
							break;
							case "4" : 
								var task_index = $filter('GetIndexNumeric')($scope.filter_highs,{task_id:data.task_id} );
								if($scope.filter_highs[task_index] != undefined){
									$scope.filter_highs[task_index] = task;
								}
							break;
						}
					}
					
					if($scope.temp_edit_task_m_id != data.milestone_id && $scope.temp_edit_task_m_id != 0)
					{
						$scope.remove_from_filter_milestone(data.task_id, $scope.temp_edit_task_m_id);
					}
					if($scope.temp_edit_task_m_id != data.milestone_id && data.milestone_id != 0)
					{
						$scope.add_filter_milestone(data.milestone_id, task);
					}
				}		
			}
		};
	/*************************************************************************************************
	Delete Task from Task List Datatable
	*************************************************************************************************/
		$scope.delete_task_toTaskDatatables = function(task_id)
		{
			var task_row = angular.element("#task_row-"+task_id);
			var milestone_task_row = angular.element("#milestone_task_row-"+task_id);
			if(task_row.length > 0){
				$scope.Tasks_list.rows(task_row).remove().draw();	
			}
			
			if(milestone_task_row.length > 0)
			{
				var temp_delete_m_id = $scope.temp_delete_task_m_id;
				var temp_delete_status_type = $scope.temp_delete_task_status_type;
				
				$scope.Milestone_tasks_list[temp_delete_m_id][temp_delete_status_type].rows(milestone_task_row).remove().draw();
			}
			
			if(	$scope.load_kanban_tab == 1 )
			{
				$scope.remove_from_filter_status(task_id, $scope.temp_delete_task_status_id);
				$scope.remove_from_filter_priority(task_id, $scope.temp_delete_task_priority_id);
				$scope.remove_from_filter_milestone(task_id, $scope.temp_delete_task_m_id);
				
			}		
		};
/******************************************************************************************************************************************
***
***  KANBAN TAB LOAD Functions
***
******************************************************************************************************************************************/		
		$scope.kanban_filter_tasks = function()
		{
			var task_rows = $scope.Tasks_list.rows().data();
			var milestone_rows = Milestone_list.rows().data();
			$scope.milestones = [];
			$scope.filter_nones = [];
			$scope.filter_lows = [];
			$scope.filter_mediums = [];
			$scope.filter_highs = [];
			
			var temp_filter_0 = [];
			var temp_filter_1 = [];
			var temp_filter_2 = [];
			var temp_filter_3 = [];
			var temp_filter_4 = [];
			var temp_filter_5 = [];
			var temp_filter_6 = [];
			var temp_filter_7 = [];
			var temp_filter_8 = [];
			var temp_filter_9 = [];
			var temp_filter_10 = [];
			
			if(milestone_rows.length > 0){
				var x = 0;
				while(x < milestone_rows.length){
					var row_data = milestone_rows[x];
					
					var milestone = {
							id : row_data[1],
							name : row_data[0],
							array_task : []
						};
					
					$scope.milestones.push(milestone);
					x++;
				}
			}
			
			if(task_rows.length > 0){
				var x = 0;
				while(x < task_rows.length){
					var row_data = task_rows[x];
					var owner_index = getSelectedItem($scope.organ_usersList, {value:row_data[10]});
					var status = row_data[11];
					var priority = row_data[12];
					var m_id = row_data[14];
					
					if(owner_index != undefined){
						var owner_name = owner_index.label;
					}else{
						var owner_name = "";
					}
					var task = {
						'id' : row_data[0],
						'task_id' : row_data[0],
						'comment_counter' : row_data[1],
						'task_name' : row_data[2],
						'owner_name' : owner_name
					};
					/* add to filter status */
					switch(status){
						case "0" : temp_filter_0.push(task); break;
						case "1" : temp_filter_1.push(task); break;
						case "2" : temp_filter_2.push(task); break;
						case "3" : temp_filter_3.push(task); break;
						case "4" : temp_filter_4.push(task); break;
						case "5" : temp_filter_5.push(task); break;
						case "7" : temp_filter_7.push(task); break;
						case "6" : temp_filter_6.push(task); break;
						case "8" : temp_filter_8.push(task); break;
						case "9" : temp_filter_9.push(task); break;
						case "10": temp_filter_10.push(task); break;
					}
					
					/* add to filter priority */
					$scope.add_filter_priority(priority, task);
					
					/* add to filter milestone */
					$scope.add_filter_milestone(m_id, task);
					
					x++;
				}
			}
			
			if($scope.load_plain_tab == 0 && $scope.load_kanban_tab == 0){
					$scope.filter_0 = temp_filter_0;
					$scope.filter_1 = temp_filter_1;
					$scope.filter_2 = temp_filter_2;
					$scope.filter_3 = temp_filter_3;
					$scope.filter_4 = temp_filter_4;
					$scope.filter_5 = temp_filter_5;
					$scope.filter_6 = temp_filter_6;
					$scope.filter_7 = temp_filter_7;
					$scope.filter_8 = temp_filter_8;
					$scope.filter_9 = temp_filter_9;
					$scope.filter_10 = temp_filter_10;
			}else{
				$scope.$apply(function(){
					$scope.filter_0 = temp_filter_0;
					$scope.filter_1 = temp_filter_1;
					$scope.filter_2 = temp_filter_2;
					$scope.filter_3 = temp_filter_3;
					$scope.filter_4 = temp_filter_4;
					$scope.filter_5 = temp_filter_5;
					$scope.filter_6 = temp_filter_6;
					$scope.filter_7 = temp_filter_7;
					$scope.filter_8 = temp_filter_8;
					$scope.filter_9 = temp_filter_9;
					$scope.filter_10 = temp_filter_10;
				});
			}
			$scope.load_kanban_tab = 1;
			
		};		
		
		
		
		$scope.add_filter_priority = function(priority, task){
			switch(priority){
				case "1" : $scope.filter_nones.push(task); 		break;
				case "2" : $scope.filter_lows.push(task); 		break;
				case "3" : $scope.filter_mediums.push(task); 	break;
				case "4" : $scope.filter_highs.push(task); 		break;
			}
		}
		
		
		$scope.add_filter_milestone = function(milestone_id, task){
			if(milestone_id != 0){
				var m_index = $filter('GetIndexNumeric')($scope.milestones,{id:milestone_id} );
				if($scope.milestones[m_index] != undefined){
					$scope.milestones[m_index]['array_task'].push(task);
				}
			}
		}
		
		$scope.remove_from_filter_priority = function(task_id, priority){
			var temp_var = "";
			switch(priority){
				case "1" : var temp_var = $scope.filter_nones;	break;
				case "2" : var temp_var = $scope.filter_lows;	break;
				case "3" : var temp_var = $scope.filter_mediums;	break;
				case "4" : var temp_var = $scope.filter_highs;		break;
				default  : var temp_var = "";					break;
			}
			
			if(temp_var != ""){
				var index = $filter('GetIndexNumeric')(temp_var,{task_id:task_id} );	
				
				if(temp_var[index] != undefined) {
					temp_var.splice(index, 1);
				}
			}
		}
		
		$scope.remove_from_filter_status = function(task_id, status){
			var temp_var = "";
			switch(status){
				case "0" : var temp_var = $scope.filter_0; break;
				case "1" : var temp_var = $scope.filter_1; break;
				case "2" : var temp_var = $scope.filter_2; break;
				case "3" : var temp_var = $scope.filter_3; break;
				case "4" : var temp_var = $scope.filter_4; break;
				case "5" : var temp_var = $scope.filter_5; break;
				case "6" : var temp_var = $scope.filter_6; break;
				case "7" : var temp_var = $scope.filter_7; break;
				case "8" : var temp_var = $scope.filter_8; break;
				case "9" : var temp_var = $scope.filter_9; break;
				case "10": var temp_var = $scope.filter_10; break;
				default  : var temp_var = "";					break;
			}
			
			if(temp_var != ""){
				var index = $filter('GetIndexNumeric')(temp_var,{task_id:task_id} );	
				
				if(temp_var[index] != undefined) {
					temp_var.splice(index, 1);
				}
			}
		}
		
		$scope.remove_from_filter_milestone = function(task_id, m_id ){
			if(m_id != 0){
				var m_index = $filter('GetIndexNumeric')($scope.milestones,{id:m_id} );
				if($scope.milestones[m_index] != undefined){
					var temp_milestone = $scope.milestones[m_index];
					var task_index = $filter('GetIndexNumeric')(temp_milestone['array_task'],{task_id:task_id} );
					if(temp_milestone['array_task'][task_index] != undefined){
						temp_milestone['array_task'].splice(task_index, 1);
					}
				}
			}
		}
/******************************************************************************************************************************************
***
***  TASK COMMENTS Functions
***
******************************************************************************************************************************************/				
	/*************************************************************************************************
	OnPage edit task modal get task comments
	*************************************************************************************************/
		$scope.get_task_comments = function(task_id)
		{
			var final_data = {action: "get_all_task_comment", csrf_gd : Cookies.get('csrf_gd'), task_id : task_id};
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : $scope.url_get_all_task_comment,
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				$scope.task_comments = response.comments;
				$scope.task_counter = response.count;
			});
		};	
	$scope.comment_profile_pic = function (profile_pic, user_id){
			if(profile_pic == null || profile_pic == "null"){
				return base_url +"public/images/unknown.png";
			}else{
				return base_url +"uploads/"+user_id+"/"+profile_pic;
			}
		}	
		
	/*************************************************************************************************
	Process POST of Milestone : Add, Edit, Delete
	*************************************************************************************************/
		$scope.process_TaskComment =  function(data, process_type, url)
		{
			var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
			var final_data = angular.extend(data, csrf_object);
			
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : url,
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.error == 0){
					
					
					switch(process_type ){
						case "add" : 	
							var data = response.comment;
							$scope.task_comment.comment = '';
							$scope.task_comments.push(data);
							$scope.task_counter += 1;
						break;
						case "edit": 
							
							
						break;
						case "delete":	
							var comment_index = $filter('GetIndexNumeric')($scope.task_comments,{task_progress_id:response.task_progress_id} );
							if($scope.task_comments[comment_index] != undefined) {
								$scope.task_comments.splice(comment_index, 1);
								$scope.task_counter -= 1;
							}
						break;
					}
						$.alert({
							title: 'Success',
							content: response.message,
							confirmButtonClass: 'btn-success'
						});
				}else{
					$.alert({
						title: 'Error',
						content: response.message,
						confirmButtonClass: 'btn-danger'
					});
				}
			}).error(function(){
				$.alert({
					title: 'Error',
					content: "Failed to process operation.",
					confirmButtonClass: 'btn-danger'
				});
			});
		}	
	/* Save task Comment */
		$scope.save_task_comment = function(){
			if($scope.task_comment.comment ==  undefined){
                  $scope.comment_error_message = 'Comment field is required.'
                  $scope.comment_field = true;
                  return;
            }    
			
			var data = {
				action : "add_task_comment",
				comment: $scope.task_comment.comment,
				task_id : $scope.edit_task_id
			};
			$scope.process_TaskComment(data, "add", $scope.url_add_task_comment);
			
		}

	/* Save update Comment */
		$scope.save_update_comment = function($comment,$comment_id,$task_id){
			if($comment ==  undefined || $comment == ""){
                  $scope.comment_error_message = 'Comment field is required.'
                  $scope.comment_field = true;
                  return;
            }    
			var data = {
				action : "edit_task_comment",
				comment: $comment,
				comment_id : $comment_id,			
				task_id : $task_id			
			};

			$scope.process_TaskComment(data, "edit", $scope.url_edit_task_comment);
		}
	/* Delete Comment */
		$scope.delete_comment = function($id,$task_id){
			var data = {
					action : "delete_task_comment",
					comment_id:$id, 
					task_id:$task_id
				};
			$scope.process_TaskComment(data, "delete", $scope.url_delete_task_comment);
		}
		
	/* Edit and Delete tooltip  */
		$scope.tooltip_comment = function(type){
			if(type == "delete"){
				$('.delete_comment_btn').tooltip({placement: "left"}); 	
			}else{
				$('.edit_comment_btn').tooltip({placement: "bottom"}); 
			}
		}
	/* Open comment modal */
		$scope.open_task_comment_link = function($id,$task_name){
			$scope.comment_field = false;
			$scope.task_comment = '';
			$scope.comment_task_name = $task_name;
			$scope.comment_task_id = $id;
			$scope.get_task_comments($id);
			$('#task_comment_standalone_modal').modal('show');
		}
	/* Save task Comment Standalone*/
		$scope.save_task_comment_standalone = function(){
			if($scope.task_comment.comment ==  undefined){
                  $scope.comment_error_message = 'Comment field is required.'
                  $scope.comment_field = true;
                  return;
            }    
			
			var data = {
				action : "add_task_comment",
				comment: $scope.task_comment.comment,
				task_id : $scope.comment_task_id
			};
			$scope.process_TaskComment(data, "add", $scope.url_add_task_comment);
			
		}
/******************************************************************************************************************************************
***
***  END CONTROLLER	
***
******************************************************************************************************************************************/		
}); 

/******************************************************************************************************************** 
Function extension
*********************************************************************************************************************/
function GetIndexNumeric(){
	return function(items, props) {
		if (angular.isArray(items)) {
			var itemMatches = false;
			var value_index = 0;
			var indexKey = false;
			items.forEach(function(item) {
				var keys = Object.keys(props);
				for (var i = 0; i < keys.length; i++) {
					var prop = keys[i];
					var value = props[prop];
					if(item[prop] != undefined){
						if (item[prop].toString().toLowerCase().indexOf(value) !== -1) {
							itemMatches = true;
							break;
						}
					}
				}
				if(itemMatches){ return false; }	 
				value_index++;
			});
			return value_index;
		}else{
			return 0;
		}
	};
}
