<?php $this->load->view('includes/header'); ?>
<div class="bg-white-wrapper clearfix">
	<div id="milestones">
		<input type="hidden" id="milestone_permission" value="<?php echo $milestone_permission_name; ?>">
		<input type="hidden" id="notif_task_id" value="<?php echo $notif_task_id; ?>">
		
		<!-- Nav tabs -->
		<div class="col-md-12 m_tabs_container_fixed">
			<ul class="nav nav-tabs" role="tablist" id='milestones_tablist'>
				<li role="presentation" class="active col-md-4">
					<a id="classic_tab_id" href="#classic_tab" aria-controls="classic_tab" role="tab" data-toggle="tab">
						<i class="fa fa-list-alt"></i> Classic
					</a>
				</li>
				<li role="presentation" class=" col-md-4">
					<a id="plain_tab_id" href="#plain_tab" aria-controls="plain_tab" role="tab" data-toggle="tab">
						<i class="fa fa-list"></i> Plain
					</a>
				</li>
				<li role="presentation" class="col-md-4">
					<a id="kanban_tab_id" href="#kanban_tab" aria-controls="kanban_tab" role="tab" data-toggle="tab">
						<i class="fa fa-th-large"></i> Kanban
					</a>
				</li>
			</ul>
		</div>
		
		<!-- Container tabs -->
		<div class="col-md-12 m_tabs_container_fixed">
			<div class="tab-content" id="milestone_tabs_container"  ng-controller="milestoneClassic" ng-init="get_milestones()">
				<div role="tabpanel" class="tab-pane active" id='classic_tab'>
					<div class="panel m_panel">
					  <div class="panel-body m_panel_body">
						<?php 
							$this->load->view("milestone/tabs/classic.php", array("milestone_permission_name"=> $milestone_permission_name)); 
						?>	
					  </div>
					</div>
					
				</div>
				<div role="tabpanel" class="tab-pane" id='plain_tab'>
					<div class="panel m_panel">
					  <div class="panel-body m_panel_body">
						<?php 
							$this->load->view("milestone/tabs/plain.php", array("milestone_permission_name"=> $milestone_permission_name)); 
						?>
					  </div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id='kanban_tab'>
					<div class="panel m_panel">
					  <div class="panel-body m_panel_body">
							<!-- Kanban Tab -->
							<div class="container-fluid" style="margin-top: 20px;">
								<select ng-model="kanban_filter_id" 
									ng-options="kanban_filter_id as kanban_filter_id.label for kanban_filter_id in kanban_filterList" 
									ng-change="onChangeStatus_Kanban(kaban_filter_id)" class="form-control" id='kanban_dropdown' style="width:50%">
								</select>
							</div>
							<br>
							<?php 
								$this->load->view("milestone/tabs/kanban_milestone.php", array("milestone_permission_name"=> $milestone_permission_name)); 
								$this->load->view("milestone/tabs/kanban_priority.php", array("milestone_permission_name"=> $milestone_permission_name)); 
								$this->load->view("milestone/tabs/kanban_percentage.php", array("milestone_permission_name"=> $milestone_permission_name)); 

							?>
						</div>
					</div>
				</div>
				<?php 
					$this->load->view("milestone/modals/add_task.php"); 
					$this->load->view("milestone/modals/edit_task.php"); 
					$this->load->view("milestone/modals/delete_task.php"); 
						
					$this->load->view("milestone/modals/add_milestone.php"); 
					$this->load->view("milestone/modals/edit_milestone.php"); 
					$this->load->view("milestone/modals/delete_milestone.php"); 
					$this->load->view("milestone/modals/task_comment_standalone.php"); 
				?>	
			</div>
		</div>
		
		
		
		<div class="dataTables_processing" style="display:none;"><i class="fa fa-spinner fa-pulse"></i> Loading...</div>
	</div>
</div> <!-- .bg-white-wrapper -->

<?php $this->load->view('includes/footer'); ?>