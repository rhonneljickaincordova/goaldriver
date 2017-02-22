<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{

	var $dashboard_permission_name = "";
	var $milestone_permission_name = "";
	var $kpi_permission_name = "";
	var $response_message = "";
	var $response_code = 1;
	var $organ_id = null;
	var $user_id = null;
	var $plan_id = null;
	var $can_view_list = "";
	var $no_access = "";
	var $access = "";
	var $has_access = array();
	var $has_access_milestone = array();
	var $has_access_kpi = array();
		
	public function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('logged_in')) 
		{
			redirect('account/sign_in');
		}

		$this->load->model('Users_model');
		$this->load->model("Meeting_model");
		$this->load->model('Organisation_model');
		$this->load->model('Task_model');
		$this->load->model('Milestone_model');
		$this->load->model('Plan_model');
		$this->load->model('Dashboard_model');
		$this->load->model('Organisationusers_model');
		$this->load->library('Highcharts');
		
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id  = $this->session->userdata('organ_id');
		$this->plan_id  = $this->session->userdata('plan_id');

		
		if($this->organ_id == null)
		{
			$session_data = array(
				'error_message'	=> "Please select an organisation first."
			);	
			$this->session->set_userdata($session_data);	
			redirect("user-settings/organisations"); 
		}
		
		$this->kpi_permission_name = "hidden";
		$this->milestone_permission_name = "hidden";
		
		$this->is_organisation_owner = $this->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
		$this->has_access = check_access($this->user_id, $this->organ_id, 1);
		$this->has_access_milestone = check_access($this->user_id, $this->organ_id, 4);
		$this->has_access_kpi = check_access($this->user_id, $this->organ_id, 5);
		if($this->is_organisation_owner){
			$this->has_access = array(
				array(
					"hidden" => 0,
					"readonly" => 0,
					"readwrite" => 1
				)
			);
			$this->has_access_milestone = array(
				array(
					"hidden" => 0,
					"readonly" => 0,
					"readwrite" => 1
				)
			);
			$this->has_access_kpi = array(
				array(
					"hidden" => 0,
					"readonly" => 0,
					"readwrite" => 1
				)
			);
			$this->milestone_permission_name = "readwrite";
			$this->kpi_permission_name = "readwrite";
		}
		$has_strategy_access = check_access($this->user_id, $this->organ_id, 2);
		$has_plan_access = check_access($this->user_id, $this->organ_id, 3);
		$has_milestone_access = check_access($this->user_id, $this->organ_id, 4);
		$has_meeting_access = check_access($this->user_id, $this->organ_id, 6);
		$has_team_access = check_access($this->user_id, $this->organ_id, 7);
		$has_canvas_access = check_access($this->user_id, $this->organ_id, 8);

		if(!empty($this->has_access))
		{
			if($this->has_access[0]['readwrite'] == 1 && $this->has_access[0]['readonly'] == 0){
				$this->dashboard_permission_name = "readwrite";
			}else if($this->has_access[0]['readonly'] == 1 && $this->has_access[0]['readwrite'] == 0){
				$this->dashboard_permission_name = "readonly";
			}else if($this->has_access[0]['readonly'] == 1 && $this->has_access[0]['readwrite'] == 1){
				$this->dashboard_permission_name = "readwrite";
			}
		}
		
		if(!empty($this->has_access_milestone))
		{
			if($this->has_access_milestone[0]['readwrite'] == 1 && $this->has_access_milestone[0]['readonly'] == 0){
				$this->milestone_permission_name = "readwrite";
			}else if($this->has_access_milestone[0]['readonly'] == 1 && $this->has_access_milestone[0]['readwrite'] == 0){
				$this->milestone_permission_name = "readonly";
			}else if($this->has_access_milestone[0]['readonly'] == 1 && $this->has_access_milestone[0]['readwrite'] == 1){
				$this->milestone_permission_name = "readwrite";
			}
		}
		
		if(!empty($this->has_access_kpi))
		{
			if($this->has_access_kpi[0]['readwrite'] == 1 && $this->has_access_kpi[0]['readonly'] == 0){
				$this->kpi_permission_name = "readwrite";
			}else if($this->has_access_kpi[0]['readonly'] == 1 && $this->has_access_kpi[0]['readwrite'] == 0){
				$this->kpi_permission_name = "readonly";
			}else if($this->has_access_kpi[0]['readonly'] == 1 && $this->has_access_kpi[0]['readwrite'] == 1){
				$this->kpi_permission_name = "readwrite";
			}
		}
		
		if(!empty($this->has_access))
		{
			if($this->has_access[0]['readonly'] == 1)
			{
				$this->can_view_list = "yes";
			}
			if($this->has_access[0]['hidden'] == 1)
			{
				$this->no_access = "yes";
			}
			if($this->has_access[0]['readwrite'] == 1)
			{
				$this->access = "yes";
			}
		}else{
			$this->no_access = "yes";
		}
		
		if($this->no_access == "yes")
		{
			if(!empty($this->has_access))
			{
				if($has_canvas_access[0]['readwrite'] == 1 || $has_canvas_access[0]['readonly'] == 1){
					redirect(base_url("index.php/canvases"));
				}else if($has_strategy_access[0]['readwrite'] == 1 || $has_strategy_access[0]['readonly'] == 1){
					redirect(base_url("index.php/pitch"));
				}else if($has_plan_access[0]['readwrite'] == 1 || $has_plan_access[0]['readonly'] == 1){
					redirect(base_url("index.php/plan"));
				}else if($has_milestone_access[0]['readwrite'] == 1 || $has_milestone_access[0]['readonly'] == 1){
					redirect(base_url("index.php/schedule"));
				}else if($this->has_access_kpi[0]['readwrite'] == 1 || $this->has_access_kpi[0]['readonly'] == 1){
					redirect(base_url("index.php/kpi"));
				}else if($has_meeting_access[0]['readwrite'] == 1 || $has_meeting_access[0]['readonly'] == 1){
					redirect(base_url("index.php/meeting"));
				}else if($has_team_access[0]['readwrite'] == 1 || $has_team_access[0]['readonly'] == 1){
					redirect(base_url("index.php/teams/user"));
				}else{
					show_no_access_page("no_access");
					exit;
				}
			}
			else
			{
				show_404_page("404_page" );
				exit;
			}
		}
		
	}

	function index()
	{
		$this->load->model('Users_model');
		$data['title'] = "Dashboard";
		$this->session->set_userdata('from_dashboard', false);
		$js = array();
		$js[] =  base_url() ."public//highcharts/highcharts.js";
		$js[] =  base_url() ."public//highcharts/highcharts-more.js";
		$js[] =  base_url() ."public//highcharts/modules/solid-gauge.js";
		$js[] =  base_url() ."public/highcharts/modules/exporting.js";
		$js[] = base_url() ."public/app/public/dashboard/kpi_highcharts.js";
		$js[] = base_url() ."public/app/public/options/highchart_options.js";
		$js[] = base_url() ."public/app/public/dashboard/dashboard_milestone_highcharts.js";
		$js[] = base_url() ."public/app/public/dashboard/calendar.js";
		$js[] = base_url() ."public/app/public/dashboard/tasks.js";
		$data['js'] = $js;
		$data['css'] = array(
			base_url() ."public/app/css/dashboard.css"
		);
		
		$data['is_organisation_owner'] = $this->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
		
		$data['milestone_permission_name'] = $this->milestone_permission_name;
		$data['dashboard_permission_name'] = $this->dashboard_permission_name;
		$data['kpi_permission_name'] = $this->kpi_permission_name;
		$this->load->view('account/index', $data);
		
	}


	public function encrypt_id($meeting_id)
	{
		$this->load->helper('more_helper');
		$meeting_id = (int)$meeting_id;
		$this->session->set_userdata('from_dashboard', true);
    	redirect('/meeting/workspace/'.encrypt($meeting_id)."/".encrypt($this->organ_id));
	}
/******************************************************************************************************************************************
***
***  CALENDAR TAB	
***
******************************************************************************************************************************************/	
	/*********************************************************************
	AJAX Get Milestone
	*********************************************************************/
	public function ajax_get_milestone()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_milestone")
		{
			$this->response_message = "Invalid Milestone.";
			if(	isset($_POST['m_id']))
			{
				$milestone = $this->Milestone_model->get_milestone($_POST['m_id'], $this->organ_id);
				
				if($milestone){
					$this->response_code = 0;	
					$this->response_message = "Valid Milestone.";	
					$this->response_data = $milestone;	
				}
			}	
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"milestone"		=> $this->response_data
			)));	
		}	
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Get Task
	*********************************************************************/
	public function ajax_get_task()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_task")
		{
			$comments = array();
			$comments_count = 0;
			$this->response_message = "Invalid Task.";
			if(	isset($_POST['task_id']))
			{
				$task = $this->Task_model->get_task($_POST['task_id'], $this->organ_id);
				
				if($task){
					$comments = $this->Task_model->get_all_task_comment($this->user_id, $_POST['task_id'], $this->organ_id);
					if($comments){
						$comments_count = count($comments);
					}
					$task->participant_id = unserialize($task->participant_id);
					$this->response_code = 0;	
					$this->response_message = "Valid Task.";	
					$this->response_data = $task;	
				}
			}	
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data,
					"comments"		=> $comments,
					"comments_count" => $comments_count
			)));	
		}	
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	
	public function get_monthly_schedule()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_monthly_schedule")
		{
			if(isset($_POST['month_year'])){
				$month_year = explode(" ", $_POST['month_year']);
				$month_name = strtolower($month_year[0]);
				$year = $month_year[1];
				$month_num = $this->month_to_num($month_name);
				
				$array = array(
					"count" => 0, 
					"milestones" => array(),
					"tasks_count" => 0, 
					"tasks" => array(),
					"meetings" => array(),
					"users_count" => 0,
					"organ_users" => array(),
					"events" => array(),
					"user_id" => $this->user_id
				);
			
				$milestones = $this->Milestone_model->get_monthly_milestones($this->user_id, $this->organ_id, $month_num, $year);
				$tasks = $this->Task_model->get_monthly_tasks($this->user_id, $this->organ_id, $month_num, $year);
				$organ_users = $this->Organisationusers_model->get_organisation_users($this->organ_id);	
				$meetings = $this->Meeting_model->get_monthly_meetings($this->user_id, $this->organ_id, $month_num, $year); 
				
				if($milestones){
					$array['count'] = count($milestones);
					$array['milestones'] = $milestones;
					foreach($milestones as $milestone){
						$event = array( 
										'title' => $milestone->name,
										'start' => gd_date($milestone->dueDate . " 4:00:00"),
										'end' => gd_date($milestone->dueDate . " 12:00:00"),
										'id' => $milestone->id,
										'imageurl' => '../uploads/folder.png',
										'className' => 'label-warning',
										'allDay' => false,
										'type' => "milestone"										
								);
						
						array_push($array['events'],$event);
					};
				}
				
				if($tasks)
				{
					$array['tasks_count'] = count($tasks);
					$array['tasks'] = $tasks;
					foreach ($tasks as $task ) {
						$event = array( 
										'title' => $task->task_name,
										'start' => gd_date($task->task_dueDate. " 4:00:00"),
										'end' => gd_date($task->task_dueDate. " 12:00:00"),
										'id' => $task->task_id,
										'className' => 'label-success',
										'imageurl' 		=> '../uploads/menu.png', 
										'allDay' => false,	 
										'type' => "task"
								);

						array_push($array['events'],$event);
					};
				}
				
				if(isset($meetings))
				{
					$array['meetings'] = $meetings;
					foreach($meetings as $meeting){
						$datetime_to = $meeting['when_to_date'];
						$datetime_string_to = $datetime_to;
						$date_to = strtok($datetime_string_to, " ");
						$format_to = str_replace('/', '-', $date_to);
						$formatted_date_to = date('Y-m-d', strtotime($format_to));

						$datetime_from = $meeting['when_from_date'];
						$datetime_string_from = $datetime_from;
						$date_from = strtok($datetime_string_from, " ");
						$format_from = str_replace('/', '-', $date_from);
						$formatted_date_from = date('Y-m-d', strtotime($format_from));


						$event = array( 'title' => $meeting['meeting_title'],
										'start' =>gd_date($formatted_date_from),
										'end' =>gd_date($formatted_date_to),
										'id' => $meeting['meeting_id'],
										'imageurl' => '../uploads/profile.png',
										'className' => 'label-info',
										'allDay' => false	 
						);	

						array_push($array['events'],$event);
					}
				}
				
				if($organ_users){
					$array['users_count'] = count($organ_users);
					$array['organ_users'] = $organ_users;
				}
				
				die(json_encode($array));
				
			}
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}	


	public function get_shared_task()
	{
		$shared_from =  $_POST['shared_from'];

		$item['shared_to'] = $this->Dashboard_model->get_shared_task($shared_from);
		$item['user'] = $this->Users_model->get_users();
		
		foreach ($item['shared_to'] as $row ) {
			$row->shared_to = unserialize($row->shared_to);
		}
		return print json_encode($item);
	}
	
	public function save_shared_task()
	{
		$participant = serialize($_POST['shared_to']);	
		$insert_data = array(
			'shared_to' => $participant,
			'shared_from' =>  $this->session->userdata('user_id'),
			'shared_date' => date("Y-m-d H:i:s")
		);

		$inserted = $this->Dashboard_model->save_shared_task($insert_data);

		if($inserted > 0 ){
			$this->response_code = 0;	
			$this->response_message = "Save successfully.";	
		}else{
			$this->response_code = 1;	
			$this->response_message = "Failed to update.";
		}

			die( json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
			)));
	}

	/*********************************************************************
	AJAX Get All Meetings
	*********************************************************************/
	public function ajax_get_all_meetings()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_meetings")
		{
			$array = array(
				"count" => 0, 
				"meetings" => array()
			);
			$meetings = $this->Meeting_model->get_all_meetings($this->user_id, $this->organ_id);
			
			if($meetings){
				$array['count'] = count($meetings);
				$array['meetings'] = $meetings;
			}
			
			die(json_encode($array));
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	public function ajax_get_meeting()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_meeting" && isset($_POST['meeting_id']))
		{
			$meeting_id = (int)$_POST['meeting_id'];
			$this->response_message = "Invalid Meeting.";
			
			$meeting = $this->Meeting_model->get_meeting_info($meeting_id);
			
			if($meeting){
				$this->response_code = 0;	
				$this->response_message = "Valid Meeting.";	
				$this->response_data = $meeting[0];	
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"meeting"		=> $this->response_data
			)));	
		}
		show_404_page("404_page" );	
	}

	
	
	private function month_to_num($month_name)
	{
		$month_num = 0;
		switch($month_name)
		{
			case "january" : 	$month_num = 1; break;	
			case "february" : 	$month_num = 2; break;	
			case "march" : 		$month_num = 3; break;	
			case "april" : 		$month_num = 4; break;	
			case "may" : 		$month_num = 5; break;	
			case "june" : 		$month_num = 6; break;	
			case "july" : 		$month_num = 7; break;	
			case "august" : 	$month_num = 8; break;	
			case "september" : 	$month_num = 9; break;	
			case "october" : 	$month_num = 10; break;	
			case "november" : 	$month_num = 11; break;	
			case "december" : 	$month_num = 12; break;	
		}
		
		return $month_num;
	}
	
	private function is_valid_date($date)
	{
		preg_match('/\d{4}-\d{2}-\d{2}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}
	
	private function is_readwrite()
	{
		if($this->has_access[0]['readwrite'] != 1 || ($this->has_access[0]['readwrite'] == 1 && $this->has_access[0]['readonly'] != 0))
		{
			$array = array("response"=>"error", "message"=>"No enough permission.");
			die(json_encode($array));
		}	
	}
		
}
?>