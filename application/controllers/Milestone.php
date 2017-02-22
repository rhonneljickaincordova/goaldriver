<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Milestone extends CI_Controller {
	
	var $can_view_list = "";
	var $no_access = "";
	var $access = "";
	var $has_access = array();
	
	var $organ_id = 0;
	var $user_id = 0;
	var $plan_id = 0;
	var $task_id = 0;
	var $response_code = 1;
	var $response_message = "";
	var $response_data = array();
	
	public function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('logged_in')){
			redirect('account/sign_in');
		}

		$this->organ_id = $this->session->userdata('organ_id');
		$this->user_id = $this->session->userdata('user_id');
		$this->plan_id = $this->session->userdata('plan_id');
		$this->task_id = ($this->session->userdata('task_id') == null) ? 0 : $this->session->userdata('task_id');
		$action = $this->uri->segment(2);
		
		$this->load->model('Users_model');
		$this->load->model("meeting_model");
		$this->load->model('Organisation_model');
		$this->load->model('Organisationusers_model');
		$this->load->model('Task_model');
		$this->load->model('Task_users_model');
		$this->load->model('Milestone_model');
		$this->load->model('Plan_model');
		$this->load->library('mail_notification');
		
		if($action != "open_task"){
			if($this->organ_id == null){
				$session_data = array(
					'error_message' => "Please select an organisation first."
				); 
				$this->session->set_userdata($session_data); 
				redirect("user-settings/organisations"); 
			}
			
			$this->is_organisation_owner = $this->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
			$this->has_access = check_access($this->user_id, $this->organ_id, 4);
			if($this->is_organisation_owner){
				$this->has_access = array(
					array(
						"hidden" => 0,
						"readonly" => 0,
						"readwrite" => 1
					)
				);
			}
			if(!empty($this->has_access))
			{
				if($this->has_access[0]['hidden'] == 1){
					$this->no_access = "yes";
				}else if($this->has_access[0]['readonly'] == 1){
					$this->can_view_list = "yes";
				}else if($this->has_access[0]['readwrite'] == 1){
					$this->access = "yes";
				}
			}

			if($this->can_view_list != "yes" && $this->access != "yes")
			{
				redirect('dashboard','refresh');
			}
		}
	}
	
	/********************************************************************* 
	Milestone Page
	*********************************************************************/
	function index()
	{		
		$data = array();
		$data['milestone_permission_name'] = ($this->access == "yes") ? "readwrite" : "readonly";
		$data['notif_task_id'] = $this->task_id;
		
		$data['js'] = array(
			base_url() ."public/app/public/milestones/milestones_datatable.js",
			base_url() ."public/app/public/milestones/classic.js",
			base_url() ."public/app/public/milestones/draggablePanel.js"
		);
		$data['css'] = array(
			base_url() ."public/app/css/milestone.css"
		);
		$data['title'] = 'Milestones';
		
		$this->load->view('milestone/index',$data);
		if($this->task_id != 0){
			$this->session->set_userdata('task_id', 0);
		} 
	}
	
	/*********************************************************************
	AJAX Get All Milestones
	*********************************************************************/
	public function ajax_get_all_milestone()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_milestones")
		{
			$array = array(
				"count" => 0, 
				"milestones" => array(),
				"users_count" => 0,
				"organ_users" => array(),
				"user_id" => $this->user_id
			);
			$milestones = $this->Milestone_model->get_all_milestone($this->user_id, $this->organ_id);	
			$organ_users = $this->Organisationusers_model->get_organisation_users($this->organ_id);	
			if($milestones){
				$array['count'] = count($milestones);
				$array['milestones'] = $milestones;
			}
			if($organ_users){
				$array['users_count'] = count($organ_users);
				$array['organ_users'] = $organ_users;
			}
			die(json_encode($array));
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Get All Tasks Under a Milestone
	*********************************************************************/
	public function ajax_get_milestone_tasks()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_milestone_tasks" && isset($_POST['m_id']) )
		{
			$array = array("count"=>0, "milestone_tasks"=>array());
			$tmp_milestone_tasks = $this->Milestone_model->get_all_milestone_tasks($this->user_id, $_POST['m_id'], $this->organ_id);	
			if($tmp_milestone_tasks){
				$milestone_tasks = array();
				foreach($tmp_milestone_tasks as $key=>$task_data){
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($task_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$task_data->participant_id = $participants;
					$milestone_tasks[] = $task_data;
				}
				
				$array['count'] = count($milestone_tasks);
				$array['milestone_tasks'] = $milestone_tasks;
			}
			die(json_encode($array));
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Add Milestone
	*********************************************************************/
	public function ajax_add_milestone()
	{
		if(isset($_POST['action']) && $_POST['action'] == "add_milestone")
		{
			$error = false;
			$this->is_readwrite();
			$this->load->model('Notification_model');
			$this->response_message = "Failed to add milestone.";
			
			if(	isset($_POST['owner_id']) && isset($_POST['name']) && isset($_POST['description']) && 
				isset($_POST['status']))
			{
					$owner_id =  (int)$_POST['owner_id'];
					$name = trim($_POST['name']);
					$desc = $_POST['description'];
					$status = (int)$_POST['status'];
					$tmp_start_date = @$_POST['start_date'];
					$tmp_due_date = @$_POST['due_date'];
					
					$error = $this->validate_milestone($owner_id, $status, $name);
				
					$dates = $this->validate_dates($tmp_start_date, $tmp_due_date);
					$start_date = $dates['start_date'];
					$due_date = $dates['due_date'];
					if($dates['error'] == 1){
						$error = true;
						$this->response_message = $dates['message'];
					}
					
					if($error == false){
						/* process milestone add */
						$milestone = $this
										->Milestone_model
										->milestone_add(
											$this->organ_id, $this->plan_id, $owner_id, $status, $name, $desc, $due_date, $start_date, 0, $this->user_id
										);
						
						if($milestone){
							$this->response_code = 0;	
							$this->response_message = "Added successfully.";	
							$this->response_data = $milestone;	
							
							/* Milestone Notification */
							$milestone_data = array(
								'user_id' => $milestone->owner_id,
								'organ_id' => $this->organ_id,
								'notification_type_id' => '5',
								'text' => 'You have created ' . htmlspecialchars($name) . ' Milestone ',
								'link_value' => 'milestone',
								'status' => 0,
								'enteredon' => date("Y-m-d H:i:s")
							);

							$this->Notification_model->success($milestone_data);	 
							/********************************************************************
							 * Mail notification when milestone is added
							 * added by: james
							 * 
							 * When we assign this milestone to another user, notify them.
							 ********************************************************************/	
							if($this->user_id != $milestone->owner_id)
							{
								$mail_notif = array();
								$mail_notif['name'] = $name;
								$mail_notif['owner'] = $this->user_id; // current logged in
								$mail_notif['milestone'] = site_url('milestone');
								$mail_notif['url'] = site_url('milestone');
								$mail_notif['start_date'] = $start_date;
								$mail_notif['due_date'] = $due_date;
								$this->mail_notification->send('milestone_added', array($$milestone->owner_id), $mail_notif);	
							}
							/* End: Mail notification */
						}
					}
					
					die(json_encode(array(
							"error"			=> $this->response_code,
							"message"		=> $this->response_message,
							"milestone"		=> $this->response_data
					)));	
			}
				
			
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Edit Milestone
	*********************************************************************/
	public function ajax_edit_milestone()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_milestone")
		{
			$error = false;
			$this->is_readwrite();
			$this->load->model('Notification_model');
			$this->response_message = "Failed to update milestone.";
			
			if(	isset($_POST['owner_id']) && isset($_POST['name']) && isset($_POST['description']) && 
				isset($_POST['status']) &&
				isset($_POST['m_id']))
			{
			
					$m_id = (int)$_POST['m_id'];
					$owner_id =  (int)$_POST['owner_id'];
					$name = trim($_POST['name']);
					$desc = $_POST['description'];
					$status = (int)$_POST['status'];
					
					$tmp_start_date = @$_POST['start_date'];
					$tmp_due_date = @$_POST['due_date'];
					
					$error = $this->validate_milestone($owner_id, $status, $name);
					
					$dates = $this->validate_dates($tmp_start_date, $tmp_due_date);
					$start_date = $dates['start_date'];
					$due_date = $dates['due_date'];
					
					$check_milestone = $this->Milestone_model->get_milestone($m_id, $this->organ_id);
					if($check_milestone == false){
						$error = true;
						$this->response_message = "Milestone does not exist.";
					}
					if($dates['error'] == 1){
						$error = true;
						$this->response_message = $dates['message'];
					}
					if($error == false){
						$milestone_update = $this
											->Milestone_model
											->milestone_update(
													$m_id, $this->organ_id, $owner_id, $status, $name, $desc, $due_date, $start_date, $this->user_id
												);
						
						if($milestone_update)
						{
							$update_data = $this->Milestone_model->get_milestone($m_id, $this->organ_id);
							
							$this->response_code = 0;	
							$this->response_message = "Updated successfully.";	
							$this->response_data = $update_data;	
							
							$change_owner = ($check_milestone->owner_id != $update_data->owner_id) ? true : false;
							
							if($change_owner)
							{
								$milestone_data = array(
											'user_id' => $update_data->owner_id,
											'organ_id' => $this->organ_id,
											'notification_type_id' => '5',
											'text' => 'You are the new owner of Milestone ' . $name ,
											'link_value' => 'milestone',
											'status' => 0,
											'enteredon' => date("Y-m-d H:i:s")
										);

									$this->Notification_model->success($milestone_data);	 
							}

							/********************************************************************
							 * Mail notification when milestone is updated
							 * added by: james
							 * 
							 * When we assign this milestone to another user, notify them.
							//  ********************************************************************/	
							if($this->user_id != $milestone_update->owner_id)
							{
								$mail_notif = array();
								$mail_notif['name'] = $name;
								$mail_notif['owner'] = $this->user_id; // current logged in
								$mail_notif['url'] = site_url('milestone');
								$mail_notif['start_date'] = $start_date;
								$mail_notif['due_date'] = $due_date;
								$this->mail_notification->send('milestone_updated', array($milestone_update->owner_id), $mail_notif);	
							}
							/* End: Mail notification */
						}
					}
					
					die(json_encode(array(
							"error"			=> $this->response_code,
							"message"		=> $this->response_message,
							"milestone"		=> $this->response_data
					)));	
			
			
			}
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Delete Milestone
	*********************************************************************/
	public function ajax_delete_milestone()
	{
		if(isset($_POST['action']) && $_POST['action'] == "delete_milestone" && isset($_POST['m_id']))
		{	
			$this->is_readwrite();
			$this->response_message = "Failed to delete milestone.";
			$m_id = $_POST['m_id'];	
			$delete_tasks = @$_POST['delete_tasks'] ;	
			
			$delete = $this->Milestone_model->milestone_delete($this->user_id, $m_id, $this->organ_id, $delete_tasks);
			
			if($delete){
				$this->response_code = 0;
				$this->response_message = "Deleted successfully.";
				$this->response_data = (int)$m_id;
			}
			
			die( json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"milestone"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	VALIDATE Milestone : ADD, EDIT
	*********************************************************************/
	private function validate_milestone($owner_id, $status, $name)
	{
		$error = false;
		$status_array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		
		$organ_user = $this->Organisationusers_model->organisation_member_exists($owner_id, $this->organ_id);
		if($organ_user == false){
			$error = true;
			$this->response_message = "Owner doesn't exist in the organisation.";
		}
		if(!in_array($status, $status_array)){
			$error = true;
			$this->response_message = "Invalid Milestone Status.";
		}
		if($name == ""){
			$error = true;
			$this->response_message = "Milestone Name is required.";
		}	
		return $error;	
	}
	/*********************************************************************
	AJAX Edit onShowDash Milestone
	*********************************************************************/
	public function ajax_edit_bShowOnDash(){
		if(isset($_POST['action']) && $_POST['action'] == "edit_bShowOnDash" && isset($_POST['m_id']) && isset($_POST['bShowOnDash']))
		{
			$this->is_readwrite();
			$this->response_message = "Failed to update.";
			
			$m_id = $_POST['m_id'];
			$bShowOnDash = $_POST['bShowOnDash'];
			
			$response = $this->Milestone_model->milestone_showondash_update($this->user_id, $this->organ_id, $m_id, $bShowOnDash);

			if($response == true)
			{
				$update_data = $this->Milestone_model->get_milestone($m_id, $this->organ_id);
				$this->response_code = 0;
				$this->response_message = "Successfully updated.";
				$this->response_data = $update_data;	
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"milestone" 	=> $this->response_data
			)));
		}	
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Get All Tasks for Plain Tab
	*********************************************************************/
	public function ajax_get_all_task()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_tasks")
		{
			$array = array(
				"count" => 0, 
				"tasks" => array(),
				"users_count" => 0,
				"organ_users" => array()
			);
			$tmp_tasks = $this->Task_model->get_all_task($this->user_id, $this->organ_id);	
			
			if($tmp_tasks){
				$tasks = array();
				foreach($tmp_tasks as $key=>$task_data){
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($task_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$task_data->participant_id = $participants;
					$tasks[] = $task_data;
				}
				$array['count'] = count($tasks);
				$array['tasks'] = $tasks;
			}
			
			die(json_encode($array));
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Get All Tasks Filtered by user
	*********************************************************************/
	public function get_all_task_by_user()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_tasks")
		{
			$array = array(
				"count" => 0, 
				"tasks" => array(),
			);
			$tmp_tasks = $this->Task_model->get_all_task_by_user($this->user_id, $this->organ_id);	
			
			if($tmp_tasks){
				$tasks = array();
				foreach($tmp_tasks as $key=>$task_data){
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($task_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$task_data->participant_id = $participants;
					$tasks[] = $task_data;
				}
				$array['count'] = count($tasks);
				$array['tasks'] = $tasks;
			}
			
			die(json_encode($array));
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Add Task
	*********************************************************************/
	public function ajax_add_task()
	{
		if(isset($_POST['action']) && $_POST['action'] == "add_task")
		{
			$error = false;
			$this->is_readwrite();
			$this->load->model('Notification_model');
			$this->response_message = "Failed to Add Task.";
			
			$owner_id =  (int)$_POST['owner_id'];
			$name = trim($_POST['name']);
			$desc = $_POST['description'];
			$tmp_due_date = @$_POST['due_date'];
			$tmp_start_date = @$_POST['start_date'];
			$status = (int)$_POST['status'];
			$priority = (int)$_POST['priority'];
			$m_id = (int)$_POST['m_id'];
			$tmp_participants = @$_POST['participants'];
			
			$participants = $this->validate_task_participants($tmp_participants);
			
			$error =  $this->validate_task($owner_id, $status, $name, $priority, $m_id);
			
			$dates = $this->validate_dates($tmp_start_date, $tmp_due_date);
			$start_date = $dates['start_date'];
			$due_date = $dates['due_date'];
			if($dates['error'] == 1){
				$error = true;
				$this->response_message = $dates['message'];
			}
			if($error == false){
				$task_added = $this
								->Task_model
								->task_add(
									$this->user_id, $this->organ_id, $participants, $owner_id, $name, $desc, $due_date, $start_date, $status, $priority, $this->plan_id, $m_id, 0
								);
				
				if($task_added)
				{
					$new_participants = $this->validate_task_participants($tmp_participants, false);
					$this->Task_users_model->task_users_add($this->organ_id, $task_added->task_id, $this->user_id, $new_participants);
					
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($task_added->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$task_added->participant_id = $participants;
					
					$this->response_code = 0;	
					$this->response_message = "Save successfully.";	
					$this->response_data = $task_added;	
					
					/* Task add notification */
					foreach ($task_added->participant_id as $___id) 
					{
							$task_data = array(
								'user_id' => $___id,
								'organ_id' => $this->organ_id,
								'notification_type_id' => '4',
								'text' => 'You have a task ' . $name . ' in Milestone ',
								'link_value' => 'milestone/open_task/' .$this->organ_id.'/'. $task_added->task_id,
								'status' => 0,
								'enteredon' => date("Y-m-d H:i:s")
							);


						$this->Notification_model->success($task_data);	 
						
					}

					/********************************************************************
					 * Send notification to people assigned to this task
					 * by: James
					 *********************************************************************/
					$recipients = $task_added->participant_id;
					if($this->user_id != $task_added->owner_id){
						array_push($recipients, $task_added->owner_id);	
					}
					

					$mail_notif = array();
					$mail_notif['name'] = $name;
					$mail_notif['owner'] = $this->user_id;
					$mail_notif['url'] = site_url('milestone');
					$mail_notif['milestone'] = $task_added->milestone_id;
					$mail_notif['start_date'] = $start_date;
					$mail_notif['due_date'] = $due_date;
					$this->mail_notification->send('task_added', $recipients, $mail_notif);
				}
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Update Task
	*********************************************************************/
	public function ajax_edit_task()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_task")
		{
			$error = false;
			$this->is_readwrite();
			$this->load->model('Notification_model');
			$this->response_message = "Failed to Update Task.";
			
			$task_id =  (int)$_POST['task_id'];
			$owner_id =  (int)$_POST['owner_id'];
			$name = trim($_POST['name']);
			$desc = $_POST['description'];
			$tmp_due_date = @$_POST['due_date'];
			$tmp_start_date = @$_POST['start_date'];
			$status = (int)$_POST['status'];
			$priority = (int)$_POST['priority'];
			$m_id = (int)$_POST['m_id'];
			$tmp_participants = @$_POST['participants'];
			
			$participants = $this->validate_task_participants($tmp_participants);
			$error =  $this->validate_task($owner_id, $status, $name, $priority, $m_id);
			
			$dates = $this->validate_dates($tmp_start_date, $tmp_due_date);
			$start_date = $dates['start_date'];
			$due_date = $dates['due_date'];
			if($dates['error'] == 1){
				$error = true;
				$this->response_message = $dates['message'];
			}
			if($error == false) {
				$task_update = $this
								->Task_model
								->task_update(
									$this->user_id, $task_id, $this->organ_id, $participants, $owner_id, $name, $desc, $due_date, $start_date, $status, $priority, $m_id, $this->user_id
								);
								
				$new_participants = $this->validate_task_participants($tmp_participants, false);
				$this->Task_users_model->task_users_edit($this->organ_id, $task_id, $this->user_id, $new_participants);
				
				if($task_update)
				{
					$update_data = $this->Task_model->get_task($task_id, $this->organ_id);
					
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($update_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$update_data->participant_id = $participants;
					
					$this->response_code = 0;	
					$this->response_message = "Save successfully.";	
					$this->response_data = $update_data;	
					/******************************************************
					 * update task email notification.
					 * by james
					 *******************************************************/
					$recipients = $update_data->participant_id;
					if($this->user_id != $update_data->owner_id)
					{
						array_push($recipients, $update_data->owner_id);
					}

					$mail_notif = array();
					$mail_notif['name'] = $name;
					$mail_notif['owner'] = $update_data->owner_id;
					$mail_notif['url'] = site_url('milestone');
					$mail_notif['milestone'] = $update_data->milestone_id;
					$mail_notif['start_date'] = $start_date;
					$mail_notif['due_date'] = $due_date;
					$this->mail_notification->send('task_updated', $recipients, $mail_notif);
				}
			}
			
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	VALIDATE Task : ADD, EDIT
	*********************************************************************/
	private function validate_task($owner_id, $status, $name, $priority, $m_id)
	{
		$error = false;
		$status_array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		$priority_array = array(1, 2, 3, 4);
		
		$organ_user = $this->Organisationusers_model->organisation_member_exists($owner_id, $this->organ_id);
		if($organ_user == false){
			$error = true;
			$this->response_message = "Owner doesn't exist in the organisation.";
		}
		if(!in_array($status, $status_array)){
			$error = true;
			$this->response_message = "Invalid Task Status.";
		}
		if(!in_array($priority, $priority_array)){
			$error = true;
			$this->response_message = "Invalid Task Priority.";
		}
		if($name == ""){
			$error = true;
			$this->response_message = "Task Name is required.";
		}	
		if($m_id != 0 && $m_id != null){
			$milestone = $this->Milestone_model->get_milestone($m_id, $this->organ_id);
			if($milestone == false){
				$error = true;
				$this->response_message = "Milestone doesn't exist.";
			}
		}
		return $error;	
	}
	/*********************************************************************
	VALIDATE Task Participants: ADD, EDIT
	*********************************************************************/
	private function validate_task_participants($participants, $serialize = true)
	{
		$tmp_participants = array();
		if(!empty($participants)){
			foreach($participants as $participant){
				if(is_numeric($participant)){
					$organ_user = $this->Organisationusers_model->organisation_member_exists($participant, $this->organ_id);
					if($organ_user){
						$tmp_participants[] = (int)$participant;
					}
				}
				
			}
		}
		
		return ($serialize == true) ? serialize($tmp_participants) : $tmp_participants;	
	}
	/*********************************************************************
	AJAX Delete Milestone
	*********************************************************************/
	public function ajax_delete_task()
	{
		if(isset($_POST['action']) && $_POST['action'] == "delete_task" && isset($_POST['task_id']))
		{	
			$this->is_readwrite();
			$this->response_message = "Failed to delete task.";
			$task_id = $_POST['task_id'];	
			
			$delete = $this->Task_model->task_delete($this->user_id, $task_id, $this->organ_id);
			
			if($delete){
				$this->response_code = 0;
				$this->response_message = "Deleted successfully.";
				$this->response_data = (int)$task_id;
			}
			
			die( json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Inline Edit Task status
	*********************************************************************/
	public function ajax_inline_edit_owner()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_task_owner" && isset($_POST['task_id']) && isset($_POST['owner_id']) )
		{	
			$this->is_readwrite();	
			$this->response_message = "Failed to update Task owner.";
			$task_id = $_POST['task_id'];	
			$owner_id = (int)$_POST['owner_id'];	
			$organ_user = $this->Organisationusers_model->organisation_member_exists($owner_id, $this->organ_id);
			if($organ_user){
				$update = $this->Task_model->task_owner_update($this->user_id, $task_id, $owner_id, $this->organ_id);
			
				if($update){
					$update_data = $this->Task_model->get_task($task_id, $this->organ_id);
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($update_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$update_data->participant_id = $participants;
					
					$this->response_code = 0;
					$this->response_message = "Updated successfully.";
					$this->response_data = $update_data;
				}
			}
			
			die( json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Inline Edit Task Status
	*********************************************************************/
	public function ajax_inline_edit_status()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_task_status" && isset($_POST['task_id']) && isset($_POST['status']) )
		{	
			$this->is_readwrite();
			$this->response_message = "Failed to update Task status.";
			$task_id = $_POST['task_id'];	
			$status = (int)$_POST['status'];	
			$status_array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
			if(in_array($status, $status_array)){
				$update = $this->Task_model->task_status_update($this->user_id, $task_id, $status, $this->organ_id);
			
				if($update){
					$update_data = $this->Task_model->get_task($task_id, $this->organ_id);
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($update_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$update_data->participant_id = $participants;
					
					$this->response_code = 0;
					$this->response_message = "Updated successfully.";
					$this->response_data = $update_data;
				}
			}
			
			die( json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Inline Edit Task Priority
	*********************************************************************/
	public function ajax_inline_edit_priority()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_task_priority" && isset($_POST['task_id']) && isset($_POST['priority']))
		{	
			$this->is_readwrite();
			$this->response_message = "Failed to update Task priority.";
			$task_id = $_POST['task_id'];	
			$priority = (int)$_POST['priority'];	
			
			if($priority >= 1 && $priority <= 4){
				$update = $this->Task_model->task_priority_update($this->user_id, $task_id, $priority, $this->organ_id);
			
				if($update){
					$update_data = $this->Task_model->get_task($task_id, $this->organ_id);
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($update_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$update_data->participant_id = $participants;
					
					$this->response_code = 0;
					$this->response_message = "Updated successfully.";
					$this->response_data = $update_data;
				}
			}
			
			die( json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Inline Edit Task Start Date
	*********************************************************************/
	public function ajax_inline_edit_startDate(){
		if(isset($_POST['action']) && $_POST['action'] == "edit_task_start_date" && isset($_POST['task_id']) && isset($_POST['date']))
		{
			$this->is_readwrite();
			$error = false;
			$task_id = $_POST['task_id'];
			$startDate = (isset($_POST['date']) && trim($_POST['date']) != "--") ? date("Y-m-d", strtotime($_POST['date'])) : null;

		
			$task  = $this->Task_model->get_task($task_id, $this->organ_id);
			if($task){
				$dueDate = ($task->task_dueDate_format_final != "9999-99-99") ? date("Y-m-d", strtotime($task->task_dueDate)) : null;
			}else{
				$error = true;
				$this->response_message = "Failed to update: Invalid Task.";
			}
			
			if($startDate != null){
				$validate_date = $this->is_valid_date_dash($startDate);
				if($validate_date == false){
					$error = true;
					$this->response_message = "Failed to update: Invalid Start Date.";
				}
			}
			
			if($startDate != null && $dueDate != null && $startDate > $dueDate){
				$error = true;
				$this->response_message = "Failed to update: Start Date is greater than Due Date.";
			}
			
			if($error == false){
				$response = $this->Task_model->task_startdate_update($this->user_id, $task->task_id, $startDate, $this->organ_id);
				
				if($response == true)
				{
					$updated_data  = $this->Task_model->get_task($task->task_id, $this->organ_id);
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($updated_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$updated_data->participant_id = $participants;
					
					$this->response_code = 0;
					$this->response_message = "Successfully updated.";
					$this->response_data = $updated_data;
				}
				else
				{
					$this->response_code = 1;
					$this->response_message = "Failed to update.";
				}
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));
		}	
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Inline Edit Task Start Date
	*********************************************************************/
	public function ajax_inline_edit_dueDate(){
		if(isset($_POST['action']) && $_POST['action'] == "edit_task_due_date" && isset($_POST['task_id']) && isset($_POST['date']))
		{
			$this->is_readwrite();
			$error = false;
			$task_id = $_POST['task_id'];
			$dueDate = (isset($_POST['date']) && trim($_POST['date']) != "--") ? date("Y-m-d", strtotime($_POST['date'])) : null;

		
			$task  = $this->Task_model->get_task($task_id, $this->organ_id);
			if($task){
				$startDate = ($task->task_startDate_format_final != "9999-99-99") ? date("Y-m-d", strtotime($task->task_startDate)) : null;
			}else{
				$error = true;
				$this->response_message = "Failed to update: Invalid Task.";
			}
			
			if($dueDate != null){
				$validate_date = $this->is_valid_date_dash($dueDate);
				if($validate_date == false){
					$error = true;
					$this->response_message = "Failed to update: Invalid Due Date.";
				}
			}
			
			if($startDate != null && $dueDate != null && $startDate > $dueDate){
				$error = true;
				$this->response_message = "Failed to update: Start Date is greater than Due Date.";
			}
			
			if($error == false){
				$response = $this->Task_model->task_duedate_update($this->user_id, $task->task_id, $dueDate, $this->organ_id);

				if($response == true)
				{
					$updated_data  = $this->Task_model->get_task($task->task_id, $this->organ_id);
					$participants = array();
					$task_users = $this->Task_users_model->get_task_users($updated_data->task_id);
					if($task_users){
						foreach($task_users as $task_user){
							$participants[] = $task_user->user_id;
						}
						
					}
					$updated_data->participant_id = $participants;
					
					$this->response_code = 0;
					$this->response_message = "Successfully updated.";
					$this->response_data = $updated_data;
				}
				else
				{
					$this->response_code = 1;
					$this->response_message = "Failed to update.";
				}
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task"		=> $this->response_data
			)));
		}	
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	Extension Functions 
	*********************************************************************/
	private function validate_dates($start_date, $due_date)
	{
		$array = array(
			"error" => 0,
			"message" => "",
			"start_date" => NULL,
			"due_date" => NULL
		);
		if($start_date != NULL){
			if($this->is_valid_date($start_date)){
				$array['start_date'] = date("Y-m-d", strtotime($start_date));	
			}else{
				$array['error'] = 1;
				$array['message'] = "Invalid Start Date";
			}
		}

		if($due_date != NULL){
			if($this->is_valid_date($due_date)){
				$array['due_date'] = date("Y-m-d", strtotime($due_date));	
			}else{
				$array['error'] = 1;
				$array['message'] = "Invalid Due Date";
			}
		}
		
		if($start_date != NULL && $due_date != NULL && strtotime($start_date) > strtotime($due_date)){ 
			$array['error'] = 1;
			$array['message'] = "Start Date is greater than Due Date.";
		}
		
		return $array;
	}
	
	private function is_valid_date($date){
		preg_match('/\d{2}\/\d{2}\/\d{4}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}
	private function is_valid_date_dash($date){
		preg_match('/\d{4}-\d{2}-\d{2}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}
	
	private function is_readwrite(){
		if($this->has_access[0]['readwrite'] != 1 || ($this->has_access[0]['readwrite'] == 1 && $this->has_access[0]['readonly'] != 0))
		{
			$array = array("response"=>"error", "message"=>"No enough permission.");
			die(json_encode($array));
		}	
	}
	
	/*********************************************************************
	AJAX Get All Task Comments
	*********************************************************************/
	public function ajax_get_all_task_comment()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_all_task_comment" && isset($_POST['task_id']))
		{
			$task_id = $_POST['task_id'];
			$array = array(
				"count" => 0, 
				"comments" => array()
			);
			$comments = $this->Task_model->get_all_task_comment($this->user_id, $task_id, $this->organ_id);
			
			if($comments){
				$array['count'] = count($comments);
				$array['comments'] = $comments;
			}
			
			die(json_encode($array));
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	/*********************************************************************
	AJAX Add Task Comment
	*********************************************************************/
	public function ajax_add_task_comment()
	{
		if(isset($_POST['action']) && $_POST['action'] == "add_task_comment" && isset($_POST['task_id']) && isset($_POST['comment']))
		{
			$error = false;
			$this->is_readwrite();
			$this->response_message = "Failed to add task comment.";
			
			$task_id = (int)$_POST['task_id'];
			$comment = $_POST['comment'];
			
			if(trim($comment) == ""){
				$error = true;
				$this->response_message = "Comment is required.";
			}
			
			if($error == false){
				/* process milestone add */
				$comment_add = $this
								->Task_model
								->task_comment_add($comment, $this->user_id, $task_id, $this->organ_id, $this->plan_id);
				
				if($comment_add){
					$this->response_code = 0;	
					$this->response_message = "Comment Added.";	
					$this->response_data = $comment_add;	
				}
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"comment"		=> $this->response_data
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Edit Milestone
	*********************************************************************/
	public function ajax_edit_task_comment()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_task_comment" && isset($_POST['comment_id']) && isset($_POST['comment']))
		{
			$error = false;
			$this->is_readwrite();
			$this->response_message = "Failed to update task comment.";
			
			$comment_id = $_POST['comment_id'];
			$comment = $_POST['comment'];
			
			if(trim($comment) == ""){
				$error = true;
				$this->response_message = "Comment is required.";
			}
			if($error == false){
				$comment_update = $this
									->Task_model
									->task_comment_update($comment, $this->user_id, $comment_id, $this->organ_id);
				
				if($comment_update)
				{
					$this->response_code = 0;	
					$this->response_message = "Comment Updated.";	
				}
			}
			
			die(json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"comment"		=> $comment
			)));	
			
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	/*********************************************************************
	AJAX Delete Task Comment
	*********************************************************************/
	public function ajax_delete_task_comment()
	{
		if(isset($_POST['action']) && $_POST['action'] == "delete_task_comment" && isset($_POST['comment_id']))
		{	
			$this->is_readwrite();
			$this->response_message = "Failed to delete task comment.";
			$comment_id = $_POST['comment_id'];	
			
			
			$delete = $this->Task_model->task_comment_delete($this->user_id, $comment_id, $this->organ_id);
			
			if($delete){
				$this->response_code = 0;
				$this->response_message = "Comment Deleted.";
			}
			
			die( json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
					"task_progress_id"		=> $comment_id
			)));	
		}
		/* show error page if no POST */
		show_404_page("404_page" );
	}
	
	public function open_task($organ_id = 0, $task_id = 0)
	{
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		if($task_id != 0 && $organ_id != 0)
		{
			$owner = $this->Organisation_model->organisation_login($this->user_id, $organ_id);
			if($owner){
				$this->session->set_userdata('task_id', $task_id);
				redirect('milestone');	
			}else{
				if($this->organ_id == null){
					$this->session->set_userdata('error_message', "You don't have permission to view the task.");
					redirect("user-settings/organisations"); 
				}
			}
		}else{
			redirect('dashboard');	
		}
	}
	
/******************************************************************************************************************************************
***
***  MILESTONE TAB ON DASHBOARD
***
******************************************************************************************************************************************/	
	public function ajax_milestone_highchart(){
		$this->load->library('Highcharts');
		$milestones = $this->Milestone_model->milestones_load_for_dash($this->user_id, $this->plan_id);
		if($milestones){
			foreach($milestones as $milestone){
				$status = $milestone->status;
				$final_status = ($status == null) ? 0 : ($status * 10);
				
				if(
					trim($milestone->duedate) != "0000-00-00" && 
					$milestone->duedate != null && 	
					trim($milestone->duedate) != "null"  &&
					trim($milestone->startDate) != "0000-00-00"  &&
					$milestone->startDate != null  &&
					trim($milestone->startDate) != "null"  
				){
					$startDate = date_create($milestone->startDate );
					$duedate = date_create($milestone->duedate );
					
					$diff_projectdays = date_diff($startDate, $duedate);
					$projectdays = $diff_projectdays->format("%a");
					
					$diff_actualprogress = date_diff($startDate, date_create(date("Y-m-d")));
					$actualprogress = $diff_actualprogress->format("%a");
					
					$percentagecomplete = $actualprogress/$projectdays * 100;
					
					if($final_status >= $percentagecomplete-5){
						$bg_color = "green";
					}else if(($final_status <= $percentagecomplete-5) && ($final_status >= $percentagecomplete-15)){
						$bg_color = "amber";
					}else if($final_status <= $percentagecomplete-16){
						$bg_color = "red";
					}
				}else{
					$bg_color = "grey";
				}
				
				if(
					trim($milestone->duedate) != "0000-00-00" && 
					$milestone->duedate != null && 	
					trim($milestone->duedate) != "null"
				){
					$duedate = date_create($milestone->duedate );
					$duedate_name = '<br /><span style="font-size: 14px;">due '.date_format($duedate,"F d, Y").' </span>';		
				}else{
					$duedate_name = "";	
				}
				
				
				$milestone_status =  $final_status;	
				
				$this->highcharts->addHighchart("milestone-".$milestone->id, array(), "gauge", $milestone->name, "")
					->setValue('width', "300px")
					->setValue('height', '300px')
					->setValue('milestone_status', $milestone_status)
					->setValue('bg_color', $bg_color)
					->setValue('duedate_name', $duedate_name)
					->setValue('class', "milestone_chart_fixed highchart_container_fixed")
					->load_default_options()
					->get_highchart_html(false, "highchart_container col-md-4"); 
					
			}	
			
			$highcharts = (array)$this->load->get_var('highcharts');
			$array = array("result" => "success", "count"=>count($highcharts));
			foreach($highcharts as $highchart){
				$array["milestones"][] = array(
							"id" => $highchart->highchart_id,  
							"options" => $highchart->options,
							"milestone_status" => $highchart->milestone_status,
							"html" => $highchart->html,							
							"type" => $highchart->type,							
							"bg_color" => $highchart->bg_color,							
							"duedate_name" => $highchart->duedate_name							
						);
			}
			die(json_encode($array));
		}
	}
	
}