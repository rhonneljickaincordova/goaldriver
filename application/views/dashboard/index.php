<input type="hidden" id="dashboard_permission_name" value="<?php echo $dashboard_permission_name; ?>" />
<input type="hidden" id="milestone_permission_name" value="<?php echo $milestone_permission_name; ?>" />
<input type="hidden" id="kpi_permission_name" value="<?php echo $kpi_permission_name; ?>" />
<input type="hidden" id="taskTab_taskList_update" value="0" />
<input type="hidden" id="calendarTab_taskList_update" value="0" />
<input type="hidden" id="calendarTab_milestoneList_update" value="0" />
<input type="hidden" id="taskTab_milestoneList_update" value="0" />


<div id="dashboard">
		<div class="col-md-12">
			<ul class="nav nav-tabs" role="tablist" id='dashboard_main_tablist'>
				<?php 
				if($kpi_permission_name != "hidden")
				{
					echo '<li role="presentation" class="active"><a href="#graphs_main" aria-controls="graphs_main" role="tab" data-toggle="tab">KPIs</a></li>';
				}
				?>
				<?php 
				if($milestone_permission_name != "hidden")
				{
					$li_class = ($kpi_permission_name == "hidden") ? "active" : "";
					echo '<li role="presentation" class="'.$li_class.'"><a href="#milestone" aria-controls="milestone" role="tab" data-toggle="tab">Milestones</a></li>';
				}
				
				$li_class = ($kpi_permission_name == "hidden" && $milestone_permission_name == "hidden") ? "active" : "";
				
				?>
				
				<li role="presentation" class='<?php echo $li_class; ?>'><a href="#task_main" aria-controls="task_main" role="tab" data-toggle="tab">Tasks</a></li>
				<li role="presentation"><a href="#calendar_main_tab" aria-controls="calendar_main_tab" role="tab" data-toggle="tab">Calendar</a></li>
			</ul>
		</div>
		<div class="col-md-12">
			<div class="tab-content">
				<?php
				$div_class_kpi = ($kpi_permission_name != "hidden") ? "active" : "";
				$div_class_milestone = ($kpi_permission_name == "hidden" && $milestone_permission_name != "hidden") ? "active" : "";
				$div_class_task = ($kpi_permission_name == "hidden" && $milestone_permission_name == "hidden") ? "active" : "";
				
				?>
				<div role="tabpanel" class="tab-pane <?php echo $div_class_kpi; ?>" id="graphs_main">
					<?php $this->load->view("dashboard/tabs/kpi.php"); ?>
				</div>
				<div role="tabpanel" class="tab-pane <?php echo $div_class_milestone; ?>" id='milestone'>
					<?php $this->load->view("dashboard/tabs/milestone.php"); ?>
				</div>
				<div role="tabpanel" class="tab-pane <?php echo $div_class_task; ?>" id='task_main' ng-controller="taskTabCtrl" ng-init="init_functions()">
					<?php $this->load->view("dashboard/tabs/task.php");  ?>
				</div>
				<div role="tabpanel" class="tab-pane" id='calendar_main_tab' ng-controller="calendarTabCtrl" ng-init="init_functions()" >
					<?php $this->load->view("dashboard/tabs/calendar.php"); ?>
				</div>
				
				
				
			</div>	
		</div>	
		
		<div class="col-md-10">
			<div class="alert alert-success"  ng-show="reminder_sent">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{{reminder_message}}
			</div>
		</div>	
</div>
<!-- Loading image -->
<div id="loading_processing_div">
	<div id="loading_processing" style="display: none;">
		<div style="line-height: 205px;"><i class="fa fa-spinner fa-pulse" style="margin-right:10px;"></i><label>Loading...</label></div>
	</div>
</div>

<script type="text/javascript">
	jQuery(function($) {
        var panelList_percentage = $('#draggablePanelList');

        panelList_percentage.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_percentage).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });
</script>


