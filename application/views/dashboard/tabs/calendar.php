<div class="panel">
	<div class="panel-body">
		<div class="calendar-header">
			<div class="clearfix">
				<h2>Tasks</h2>

				<ul id="dashboard-menu" class="pull-right">
					<li class="li_calendarView_icon active" data-toggle="tooltip" data-placement="bottom" title="Calendar View" ng-click="on_click_view_type('calendar')"><a class="calendar" data-toggle="tab" href="#home">Calendar</a></li>
					<li class="li_listView_icon" data-toggle="tooltip" data-placement="bottom" title="List View" ng-click="on_click_view_type('list')"> <a class="list" data-toggle="tab" href="#menu1">List</a></li>
				</ul>
			</div>
			<hr>
		</div>

		<div ng-show="list_view" class="col-md-12">
			<div class="tab-content">
				<div id="menu1" class="tab-pane fade">
					<div class="col-md-4" style="padding:1px;">
						<?php $this->load->view("dashboard/tabs/calendar/overdue.php"); ?>
					</div>
					<div class="col-md-4" style="padding: 1px;">
						<?php $this->load->view("dashboard/tabs/calendar/due_today.php"); ?>
					</div>
					<div class="col-md-4" style="padding:1px;">
						<?php $this->load->view("dashboard/tabs/calendar/upcoming.php"); ?>
					</div>
				</div>
			</div>
		</div>		
		<div ng-show="!list_view" class="col-md-9">
			<div class="tab-content">
				<div class="alert alert-success"  ng-show="delete_data">
					<a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
					{{delete_message}}
				</div>
				<div id="home" class="tab-pane fade in active">
					<br>
					<div ui-calendar="uiConfig.calendar" id="event_calendar_main" class="span8 calendar" ng-model="eventSources"></div> 
				</div>
			</div>
		</div>		
		
		<div ng-show="!list_view" class="col-md-3 notifications">
			
			<div class="clearfix" style="margin-bottom:1em;">
				<div class="pull-left" style="margin-top:5px;"> 
					<label><input type="checkbox" ng-model="selectedAll" ng-click="checkAll()"><span style="padding-left:10px;">Select All</span></label>
				</div>
				<div class="pull-right">
					<button type="button" class="btn btn-danger btn-sm" ng-click="delete_all('<?php echo $this->session->userdata('user_id'); ?>')">Delete</button>
				</div>
			</div>
			
			<div class="notifications-wrapper">
				<h3>Notifications</h3>
				<div class="notifications-scroller">
					<div class="notification" ng-repeat="notify in usernotification">
						
						<div class="clearfix">
							<div class="pull-left">
								<input type="checkbox" ng-model="notify.Selected" ng-click="onSelectNotification(notify.Selected,notify.notification_id)">
							</div>
							<div class="pull-left" style="margin-left:10px;width:88%;">
								<small>
									{{notify.text}}
									<div class="text-muted">{{notify.enteredon | date : "MMMM dd, yyyy 'at' h:mma "}}</div>
								</small>
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div> 					  		
	</div>
</div>	
<?php 
$this->load->view('dashboard/modals/calendar_tab/edit_milestone.php');
$this->load->view('dashboard/modals/calendar_tab/edit_task.php');
$this->load->view('dashboard/modals/calendar_tab/view_meeting.php');
?>	
