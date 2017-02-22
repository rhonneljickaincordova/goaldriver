<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teams extends CI_Controller {
	var $user_id = 0;
	var $organ_id = 0;
	var $plan_id = 0;
	var $account_id = 0;

	function __construct(){
		parent::__construct();

		if(! $this->session->userdata('logged_in')) 
			redirect('account/sign_in');

		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');
		$this->account_id = $this->session->userdata('account_id');
		
		if($this->organ_id == null)
		{
			$session_data = array(
				'error_message'	=> "Please select an organisation first."
			);	
			$this->session->set_userdata($session_data);	
			redirect("user-settings/organisations"); 
		}

		$this->load->model('Team_model');
		$this->load->model('Users_model');
		$this->load->model('Team_users_model');
		$this->load->model('Organisation_model');
		$this->load->model('Organisationusers_model');
		$this->load->model('Permission_model');


		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}

		if($no_access == "yes")
		{
			redirect('dashboard','refresh');
		}

	}

	function index(){
		$data = array();

		$user_id = $this->session->userdata('user_id');
		$organ_id  = $this->session->userdata('organ_id');

		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($user_id, $organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 0)
			{
				$data['disabled'] = "disabled";
			}
			if($has_access[0]['readwrite'] == 1 && $has_access[0]['readonly'] == 0)
			{
				$data['disabled'] = "";
			}
			if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 1)
			{
				$data['disabled'] = "";
			}
		}

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}
		
		if($can_view_list == "yes")
		{
			$data['title'] = 'Teams';
			$data['user_id'] = $this->user_id;
			$data['organ_id'] = $this->organ_id;
			$this->load->view('teams/index', $data);
		}
		
		if($no_access == "yes")
		{
			show_404_page("404_page" );
		}

		if($access == "yes")
		{
			$data['title'] = 'Teams';
			$data['user_id'] = $this->user_id;
			$data['organ_id'] = $this->organ_id;
			$this->load->view('teams/index', $data);
		}
		
		if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
		{
			$data['title'] = 'Teams';
			$data['user_id'] = $this->user_id;
			$data['organ_id'] = $this->organ_id;
			$this->load->view('teams/index', $data);
		}

	}

	function user(){
		$data = array();

		$user_id = $this->session->userdata('user_id');
		$organ_id  = $this->session->userdata('organ_id');

		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($user_id, $organ_id, $tab_id);


		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 0)
			{
				$data['disabled'] = "disabled";
			}
			if($has_access[0]['readwrite'] == 1 && $has_access[0]['readonly'] == 0)
			{
				$data['disabled'] = "";
			}
			if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 1)
			{
				$data['disabled'] = "";
			}
		}

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}

		if($can_view_list == "yes")
		{
			$data['title'] = 'User';

			$data['user_id'] = $this->user_id;
			$data['organ_id'] = $this->organ_id;
			$data['users'] = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);
			$this->load->view('teams/user', $data);
		}
		if($no_access == "yes")
		{
			show_404_page("404_page" );
		}

		if($access == "yes")
		{
			$data['title'] = 'User';

			$data['user_id'] = $this->user_id;
			$data['organ_id'] = $this->organ_id;
			$data['users'] = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);
			$this->load->view('teams/user', $data);
		}
		
		if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
		{
			$data['title'] = 'User';

			$data['user_id'] = $this->user_id;
			$data['organ_id'] = $this->organ_id;
			$data['users'] = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);
			$this->load->view('teams/user', $data);
		}

	}

	function edit_user($encrypted_id='', $encrypted_organ_id=''){
		$update = array();
		$data = array();

		$user_id = decrypt($encrypted_id);
		$organ_id = decrypt($encrypted_organ_id);
		$user_organ_id  = $this->session->userdata('organ_id');
		
		$tab_id  = 7;
		$session_user_id = $this->session->userdata('user_id');
		$has_access = check_access($session_user_id, $user_organ_id, $tab_id); //check the permission
		$disabled = "";


		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1 || $has_access[0]['hidden'] == 1)
			{
				$disabled = "disabled";
			}
		}

		if($disabled != "disabled")
		{
			if(!empty($organ_id) && !empty($user_id))
			{
				if($user_organ_id == $organ_id)
				{
					$data['user_id'] = $user_id;
					$data['title'] = 'Edit User Information';
					$data['first_name'] = user_info('first_name', $user_id);
					$data['last_name'] = user_info('last_name', $user_id);
					//$data['username'] = user_info('username', $user_id);
					$data['company'] = user_info('company', $user_id);
					$data['tel_number'] = user_info('tel_number', $user_id);
					$data['job_title'] = user_info('job_title', $user_id);
					$data['my_user_id'] = $user_id;

					$data['organ_id'] = $organ_id;

					// validate these fields
					$this->Users_model->validate = array(
						array( 'field' => 'first_name',
			                 'label' => 'First Name',
			                 'rules' => 'required' ),
						array( 'field' => 'last_name',
			                 'label' => 'Last Name',
			                 'rules' => 'required' ),
						/* array( 'field' => 'username',
			                 'label' => 'Username',
			                 'rules' => 'required' )
						*/
					);

					if($this->input->post()){
						$update['first_name'] 	= $this->input->post('first_name');
						$update['last_name'] 	= $this->input->post('last_name');
						//$update['username'] 	= $this->input->post('username');
						$update['company'] 		= $this->input->post('company');
						$update['tel_number'] 	= $this->input->post('tel_number');
						$update['job_title'] 	= $this->input->post('job_title');

						if($this->Users_model->update($user_id, $update)){
							redirect('teams/user');
						}
					}

					$this->load->view('teams/edit_user', $data);
				}
				else
				{
					$data['title'] = '404 Page Not Found';
					show_404_page("404_page" );
				}
			}

			else
			{
				$data['title'] = '404 Page Not Found';
				show_404_page("404_page" );
			}
		}
		else
		{
			$data['title'] = '404 Page Not Found';
			show_404_page("404_page" );
		}
	}

	function delete_user(){

		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}

		if($access == "yes" || $this->session->userdata('user_type') == "admin") /** Check if user has read-write access to perform this action **/
		{
			if(isset($_POST['action']) && $_POST['action'] == "delete_user")
			{
				/** check if user is organisation admin - do not allow deleting if admin **/
				$is_admin = $this->Organisationusers_model->is_organisation_admin($_POST['user_id'], $this->organ_id);

				if(empty($is_admin))
				{
					if($this->user_id != $_POST['user_id']){
						
						// changed from Users_model to Organisationusers_model 
						// updated by James E.
						if($this->Organisationusers_model->delete_organisation_user($_POST['user_id'], $this->organ_id)){ 

							// check if user_id exists in team_users, we will update the member_count when user that is assigned to a team
							// will be deleted.
							$team_user_info = $this->Team_users_model->get_by('user_id', $_POST['user_id']);

							if(count($team_user_info)){
								$team_id = $team_user_info->team_id;	
								$team_user_id = $team_user_info->team_user_id;	

								$this->Team_model->update_member_count($team_id, 'down');
								$this->Team_users_model->delete($team_user_id);
							}
							$array = array("result"=>"success", "message"=>"User has been deleted.", "user_id"=>$_POST['user_id']);
							die(json_encode($array));
						}
					}
					else
					{
						$array = array("result"=>"error", "message"=>"Oops! You can not delete the owner.", "user_id"=>$_POST['user_id']);
						die(json_encode($array));
					}
				}
				else
				{
					$array = array("result"=>"error", "message"=>"Oops! You can not delete the organisation owner.", "user_id"=>$_POST['user_id']);
					die(json_encode($array));
				}
				
			}
		}
		else /** If user has no access - prevent them from doing this action **/
		{
			$array = array("result"=>"error", "message"=>"No rights.", "user_id"=>$_POST['user_id']);
			die(json_encode($array));
		}

	}
	
	public function has_owner_access($organ_id)
	{
		return $this->Organisation_model->get_owner_permission($organ_id, $this->user_id);
	}

	public function ajax_add_user()
	{
		/* check if is organisation owner */
		/*
		if(!$this->has_owner_access($this->organ_id))
		{
			$array = array("result"=>"error", "message"=>"No enough permission.", "type"=>1);
			die(json_encode($array));
		}
		*/

		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}


		if($this->organ_id == null)
		{
			$session_data = array(
				'error_message'	=> "Please select an organisation first."
			);	
			$this->session->set_userdata($session_data);	
			redirect("user-settings/organisations"); 
		}
		
		if($access == "yes" || $this->session->userdata('user_type') == "admin")
		{

			if(isset($_POST['action']) && $_POST['action'] == "add_user")
			{
				$first_name = (isset($_POST['first_name'])) ? $_POST['first_name'] : "";
				$last_name = (isset($_POST['last_name'])) ? $_POST['last_name'] : "";
				$user_email = @$_POST['user_email'];
				$email_from = $this->session->userdata('email');

				/** Query system_tabs - Ted **/
				$tabs	= $this->Permission_model->get("system_tabs", "", "");
						
				/* validate email */
				if(trim($user_email) == "")
				{
					$array = array("result"=>"error", "message"=> "Email is required.", "type" => 2);
					die(json_encode($array));
				}
				
				$valid_email = $this->validate_email($user_email);
				
				if($valid_email == false)
				{
					$array = array("result"=>"error", "message"=> "Invalid email format.", "type" => 2 );
					die(json_encode($array));
				}
				
				/* check email exist on this organisation*/
				$existing_email = $this->Organisationusers_model->organisation_user_exist($this->user_id, $user_email, $this->organ_id);
				
				if($existing_email[0]->count > 0 )
				{
					$array = array("result"=>"error", "message"=> "User alreay exists in this organisation.", "type" => 2	 );
					die(json_encode($array));
				}
				
				/* check email exist in any organisation*/
				$existing_user = $this->Organisationusers_model->organisation_user_exists_any($this->user_id, $user_email, $this->organ_id);
				
				if($existing_user->count > 0)
				{
					$organ_user_id = $this->Organisationusers_model->organisation_users_add($this->user_id, $existing_user->user_id, $this->organ_id);
					if($organ_user_id)
					{
						// if existing user,i think we need not to send them their username and password.
						// $send_email = $this->process_email($email_from, $existing_user->email, $existing_user, 1);

						/********************************************************************
						 * Mail notification when existing user is added  to this organisation
						 * added by: james
						 ********************************************************************/	
						$mail_notif = array();
						$mail_notif['organ_name'] = organ_info('name', $this->organ_id);
						$mail_notif['added_by'] = user_info('first_name', $this->user_id).' '.user_info('last_name', $this->user_id);
						
						$send_email = $this->mail_notification->send('user_added2organisation', array($existing_user->user_id), $mail_notif);	
						// End: Mail notification	

						$user_data = $this->Users_model->get_user_filter_by_id($existing_user->user_id);
						
						$job_title = (trim($user_data[0]->job_title) != "") ? $user_data[0]->job_title : "-";
						$company = (trim($user_data[0]->company) != "") ? $user_data[0]->company : "-";
						$tel_num = (trim($user_data[0]->tel_number) != "") ? $user_data[0]->tel_number : "-";
						
						$array = array( 
								"result" => "success", 
								"message" => "User exists in another organisation. Added successfully.", 
								"data" => array(
								"organ_user_id"=> $organ_user_id, 
								"user_id"=> $existing_user->user_id, 
								"first_name" => $existing_user->first_name,
								"last_name" => $existing_user->last_name,
								"full_name" => $existing_user->first_name . " ". $existing_user->last_name,
								"tel_num" => $tel_num,
								"job_title" => $job_title,
								"company" => $company,
								"email" => $existing_user->email,
								"send_email" => $send_email,
								"encrypted_userid" => encrypt($existing_user->user_id), //added by ted saavedra - to redirect to set permission page after creating new user
								"encrypted_organid" => encrypt($this->organ_id), 
								)
							);

						/*******************************************************************
						Adding of permission automatically after creating a user
						added by: Ted
						********************************************************************/
						$array_perm = array();

						foreach($tabs as $value)
						{
							$array_perm[] = array(
								'user_id' 	=> $existing_user->user_id,
								'organ_id'	=> $this->organ_id,
								'tab_id'	=> $value['id'],
								'hidden'	=> 0,
								'readonly'	=> 0,
								'readwrite'	=> 1,
							);
							$deleted = $this->Permission_model->delete_old_entry($existing_user->user_id, $this->organ_id, $value['id']);
						}

						if($deleted)
						{
							$inserted = $this->Permission_model->insert("permissions", $array_perm);
						}
						/******** END *******/
					}
					else
					{
						$array = array( "result" => "error", "message" => "Failed to add new user.", "type"=> 1);	
					}
				}
				else
				{
					/* create new user */
					$add = $this->member_user_add($first_name, $last_name, $user_email);		
					if($add)
					{
						$ext_data = array(
							"first_name" => $first_name, 
							"last_name" => $last_name, 
							"email" => $user_email, 
							"username" => $add['username'],
							"password" => $add['password']
						);	


						// $send_email = $this->process_email($email_from, $user_email, $ext_data, 0);	
						/********************************************************************
						 * Mail notification when user is added  to this organisation
						 * added by: james
						 ********************************************************************/	
						$mail_notif = array();
						$mail_notif['sender'] = user_info('first_name', $this->user_id).' '.user_info('last_name', $this->user_id);
						$mail_notif['your_name'] = $first_name.' '.$last_name;
						$mail_notif['your_email'] = $user_email;
						$mail_notif['your_password'] = $add['password'];
						
						$send_email = $this->mail_notification->send('new_user_added2organisation', null, $mail_notif, array($user_email));	
						// End: Mail notification

						$send_email = "";
						$array = array( 
							"result" => "success", 
							"message" => "Created new user. Added successfully.", 
							"data" => array(
								"organ_user_id"=> $add['organ_user_id'], 
								"username"=> $add['username'], 
								"user_id"=> $add['user_id'], 
								"password"=> $add['password'], 
								"first_name" => $first_name,
								"last_name" => $last_name,
								"full_name" => $first_name . " ".$last_name,
								"tel_num" => "-",
								"job_title" => "-",
								"company" => "-",
								"email" => $user_email,
								"send_email" => $send_email,
								"encrypted_userid" => encrypt($add['user_id']), //added by ted saavedra - to redirect to set permission page after creating new user
								"encrypted_organid" => encrypt($this->organ_id),
							)
						);	


						/*******************************************************************
						Adding of permission automatically after creating a user
						added by: Ted
						********************************************************************/
						$array_perm = array();

						foreach($tabs as $value)
						{
							$array_perm[] = array(
								'user_id' 	=> $add['user_id'],
								'organ_id'	=> $this->organ_id,
								'tab_id'	=> $value['id'],
								'hidden'	=> 0,
								'readonly'	=> 0,
								'readwrite'	=> 1,
							);
							$deleted = $this->Permission_model->delete_old_entry($add['user_id'], $this->organ_id, $value['id']);
						}

						if($deleted)
						{
							$inserted = $this->Permission_model->insert("permissions", $array_perm);
						}
						/******** END *******/


					}else{
						$array = array( "result" => "error", "message" => "Failed to add new user.", "type"=> 1);	
					}
				}
				die(json_encode($array));	
			}	
			
			$array = array( "result" => "error", "message" => "Invalid transaction." );
			die(json_encode($array));
		}

		else
		{
			$array = array( "result" => "error", "message" => "No rights." );
			die(json_encode($array));
		}
	}
	
	private function member_user_add($first_name, $last_name, $email){
		$data = array();
		//$username = $this->generate_username($first_name, $last_name);
		$username = "NA";
		$password = random_string('alnum', 6);
		$hash = $this->encrypt->encode($password);
		
		$add = $this->Organisationusers_model->organisation_user_member_add($this->account_id, $first_name, $last_name, $username, $email, $hash, $this->organ_id);		
		
		if($add)
		{
			$data['password'] = $password;	
			$data['username'] = $username;	
			$data['user_id'] = $add['user_id'];	
			$data['organ_user_id'] = $add['organ_user_id'];	
			
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	private function generate_username($first_name, $last_name){
		$first_name = (trim($first_name) == "") ? alpha('alpha', 3) : $this->db->escape_str($first_name);
		$last_name = (trim($last_name) == "") ? alpha('alpha', 3) : $this->db->escape_str($last_name);
		
		$is_unique = false;
		while($is_unique == false)
		{
			$username = strtolower(trim($first_name) . trim($last_name) . random_string('numeric', 4));
			$check_unique = $this->Organisationusers_model->username_is_unique($username);
			
			if($check_unique)
			{
				$is_unique = true;
			}
		}
		
		return $username;
	}
	
	// private function process_email($email_from, $email_to, $ext_data, $is_exist)
	// {
	// 	if(is_array($ext_data)){
	// 		$ext_data = (object)$ext_data;
	// 	}
	// 	$data = array();
	// 	$data['organisation_name'] = organ_info("name", $this->organ_id);
	// 	$data['user_from_info']['first_name'] = user_info("first_name", $this->user_id);
	// 	$data['user_from_info']['last_name'] = user_info("last_name", $this->user_id);
	// 	$data['user_to_info'] = $ext_data;
	// 	$data['is_exist'] = $is_exist;
	// 	$data['site_url'] = base_url();
		
	// 	//$subject = 'Welcome to ' . $data['organisation_name'];
	// 	$subject = 'Goaldriver: '.user_info("first_name", $this->user_id)." ".user_info("last_name", $this->user_id).' added you to his/her organization';
	// 	$email_body = $this->get_email_body($data);
		
	// 	$headers = "From:" . $email_from . "\r\n";
	// 	$headers .= "MIME-Version: 1.0\r\n";
	// 	$headers .= "Content-Type: text/html; charset=utf-8\r\n"; 
	// 	$headers .= 'Cc: '.$email_to.' ' . "\r\n";
	// 	//$headers .= 'Bcc: '.$email_from . "\r\n";
	// 	return $this->send_email($email_to, $subject, $email_body, $headers );
	// } 
	
	
	// private function get_email_body($data = array())
	// {
	// 	$content = $this->load->view('teams/email_template/thank_you_message', $data, TRUE);
	// 	return $content;
	// }
	
	// private function send_email($email_to, $subject, $email_body, $headers )
	// {
	// 	if(@mail($email_to, $subject, $email_body, $headers))
	// 	{
	// 		return true;
	// 	}
	// 	else
	// 	{
	// 		return false;
	// 	}
	// }
	
	private function validate_email($email)
	{
		$is_valid = (!filter_var($email, FILTER_VALIDATE_EMAIL))  ? false : true;
		return $is_valid;
	}

	// add new team
	function new_team(){
		if($this->input->post())
		{
			$new_team = $this->Team_model->insert(array(
				"name" => $this->input->post('team'),
				"deleted" => 0,
				"member_count" => 0,
				"entered_by_user_id" => $this->user_id,
				"organ_id" => $this->organ_id
				));

			$new_team_id = $this->db->insert_id();
			
			if($new_team)
			{
				echo json_encode(array(
					"team_id" => encrypt($new_team_id),
					"team_organ_id" => encrypt($this->organ_id),
					"action" => 'success'
				));
			}
			else{
				echo json_encode(array("action" => "failed"));
			}
		}
	}

	function ajax_new_team(){
		$data = array();
		$data['user_id'] = $this->user_id;
		$data['organ_id'] = $this->organ_id;

		$this->load->view('teams/ajax/new_team', $data);
	}

	function edit_team($encrpted_teamid='', $encrypted_organ_id=''){
		$data = array();
		$update = array();

		$team_id = decrypt($encrpted_teamid);
		$organ_id = decrypt($encrypted_organ_id);
		$user_organ_id  = $this->session->userdata('organ_id');

		$tab_id  = 7;
		$session_user_id = $this->session->userdata('user_id');
		$has_access = check_access($session_user_id, $user_organ_id, $tab_id); //check the permission
		$disabled = "";

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1 || $has_access[0]['hidden'] == 1)
			{
				$disabled = "disabled";
			}
		}

		if($disabled != "disabled")
		{
			if(!empty($team_id) && !empty($organ_id))
			{
				if($user_organ_id == $organ_id)
				{
					$data['title'] = 'Edit Team';
					$data['resp_msg'] = '';

					$data['name'] 		= team_info('name', $team_id);
					$data['description'] = team_info('description', $team_id);
					$data['manager_id'] = team_info('manager_id', $team_id);
					$data['team_id'] 	= $team_id;
					$data['user_id'] 	= $this->user_id;
					$data['organ_id'] 	= $this->organ_id;

					$data['team_organ_id'] = $organ_id;

					if($this->input->post()){
						//print_r($_POST);
						$update['name'] = $this->input->post('team');
						$update['description'] = $this->input->post('description');
						$update['manager_id'] = $this->input->post('manager');

						if($this->input->post('manager') != ""){
							if($this->Team_model->update($team_id, $update)){
								redirect('teams');
							}	
						}
						else{
							$data['resp_msg'] = '<div class="alert alert-danger">Please select a manager</div>';
						}
						
					}

					$this->load->view('teams/edit_team', $data);
				}
				else
				{
					$data['title'] = '404 Page Not Found';
					show_404_page("404_page" );
				}
			}
			else
			{
				$data['title'] = '404 Page Not Found';
				show_404_page("404_page" );
			}
		}
		else
		{
			$data['title'] = '404 Page Not Found';
			show_404_page("404_page" );
		}
	}

	// When they drag and drop users
	function add_team_user(){
		$team_user = array();

		if($this->input->post()){
			$team_user['user_id'] = $this->input->post('user_id');
			$team_user['team_id'] = $this->input->post('team_id');

			if($this->Team_users_model->insert($team_user, TRUE)){
				$this->Team_model->update_member_count($team_user['team_id'], 'up');	
				echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> User has been added.</div>';	
			}
		}
	}

	function remove_team_user(){
		if($this->input->post()){
			$user_id = $this->input->post('user_id');
			$team_id = $this->input->post('team_id');

			if($this->Team_users_model->delete_user_from_team($user_id)){
				$this->Team_model->update_member_count($team_id, 'down');
				echo '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> User has been removed.</div>';	
			}
		}
	}
	// When they drag and drop users

	function delete_team(){
		if($this->input->post()){
			$team_id = $this->input->post('team_id');
			$this->Team_model->delete($team_id);
		}
	}


}