<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

	var $user_id;
	var $organ_id;
	var $plan_id;

	function __construct(){
		parent::__construct();

		$this->plan_id = $this->session->userdata('plan_id');
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');

		
	}

	function index(){
		if(! $this->session->userdata('logged_in')){ 
			redirect('account/sign_in');
		}else{
			redirect('dashboard');
		}
	}

	function reset_pass($token_id){
			$this->load->model('Passwordtoken_model');
			$data['title'] = "Reset Password";
			$data['token_id'] = $token_id;
			/* $link = $_SERVER['PHP_SELF'];
			$link_array = explode('/',$link);
			$page = end($link_array); */
			$password_token_id =  $this->Passwordtoken_model->get_value($token_id);
			if($password_token_id > 0){

				// session timed out
						$update_data = array(
							'is_used' => 1,
						);

						$this->Passwordtoken_model->update_token($update_data,$password_token_id);	

						$data['user_id'] = $this->Passwordtoken_model->get_user_id($password_token_id);
						$data['password_token_id'] = $password_token_id;
						$this->load->view('account/reset_pass',$data);

				
			}else{
				redirect('account/sign_in');
			}
	}

	function close_token(){
		$this->load->model('Users_model');
		$_POST = json_decode(file_get_contents('php://input'), true);
		$password_token_id =  $_POST['password_token_id'];

			$update_data = array(
				'is_used' => 1,
			);

		$this->Passwordtoken_model->update_token($update_data,$password_token_id);	
	}

	function forgot_password(){
		if($this->session->userdata('logged_in') === TRUE) 
			redirect('dashboard');


		$data = array();
		$this->load->model('Users_model');
		$this->load->model('Passwordtoken_model');
		$data['msg'] = '';
		$data['title'] = "Password Recovery";
		$token = encrypt(rand());
		if($this->input->post()){

			$email = $this->input->post('email');
			
			if($email != '')
			{
				$userinfo = $this->Users_model->get_by('email', $email);	
				
				if(!empty($userinfo)){
					// $send_email = $this->process_email_ForgotPassword("tim.pointon@crunchersaccountants.co.uk", $email,  $userinfo,$token);

					/******************************************************
					 * Password reset email notification.
					 * by james
					 *******************************************************/
					$mail_notif = array();
					$mail_notif['first_name'] = $userinfo->first_name;
					$mail_notif['token'] = $token;
					
					$send_email = $this->mail_notification->send('reset_password', array($userinfo->user_id), $mail_notif);

					if($send_email){
						//send token
						$insert_data = array(
						'user_id' => $userinfo->user_id,
						'token_key' =>  $token,
						'is_used' => 0
						);	
						$this->Passwordtoken_model->send_token($insert_data);
		

						$data['msg'] = '<div class="alert alert-success">We have sent your password to your email. please check your email <strong>'.$email.'</strong></div>';	
					}else{
						$data['msg'] = '<div class="alert alert-danger">An error occured while processing your request. Please contact the administrator with this matter.</div>';
					}
				}
				else{
					$data['msg'] = '<div class="alert alert-danger">Sorry, <strong>'.$email.'</strong> is not exists.</div>';
				}
			 }
			else{
				$data['msg'] = '<div class="alert alert-danger">Enter your email address.</div>';
			}
		}
		
		$this->load->view('account/forgot_pass', $data);
	}

	function hash($password=null){
		if(! is_null($password)){
			echo $this->encrypt->encode($password);	
		}
		//echo $this->encrypt->encode("spluffy666?");
	}

	// function dashboard(){
		
	// 	$this->load->view('account/dashboard', $data);
	// }

	function profile(){
		if(! $this->session->userdata('logged_in')) 
			redirect('account/sign_in');

		$data['alert'] = '';
		$this->load->model('Users_model');
		$user_id = $this->user_id;

		// edit profile form was submitted
		$this->Users_model->validate = array(
			array( 'field' => 'firstname',
               'label' => 'First name',
               'rules' => 'required' ),

			array( 'field' => 'lastname',
               'label' => 'Last mame',
               'rules' => 'required' ),

			array( 'field' => 'tel',
               'label' => 'Telephone number',
               'rules' => 'required' ),

			array( 'field' => 'job',
               'label' => 'Job title',
               'rules' => 'required' ),
		);

		if($this->input->post('save_profile') == TRUE){
			$update = array();
			$update['first_name'] = $this->input->post('firstname');
			$update['last_name'] = $this->input->post('lastname');
			$update['tel_number'] = $this->input->post('tel');
			$update['job_title'] = $this->input->post('job');
			$update['about_me'] = $this->input->post('about_me');
			$update['email'] = $this->input->post('email');
			$update['utc_timezoneoffset'] = $this->input->post('timezone');

			// handle file uploading...
			if(! empty($_FILES['profile_pic']['name'])){
				$config['upload_path'] = $this->config->item('upload_dir').$user_id.'/';
				$config['allowed_types'] = $this->config->item('upload_file_type');
				$config['encrypt_name'] = TRUE;
				$config['max_size'] = $this->config->item('upload_max_size'); // 2MB

				$this->upload->initialize($config);

				if (! is_dir('./uploads/'.$user_id.'/')) {
			        mkdir('./uploads/'.$user_id, 0777, true);
				}

			    // create thumb folder for thumbnails
			    if(! is_dir('./uploads/'.$user_id.'/thumb/')){
			    	mkdir('./uploads/'.$user_id.'/thumb', 0777, true);
			    }	

				if ( $this->upload->do_upload('profile_pic')) {
					$data = array('upload_data' => $this->upload->data());

					// print_r($data);die();
                    $config['image_library'] = 'gd2';
                    $config['create_thumb'] = TRUE;
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $data['upload_data']['full_path'];
                    $config['width']    = 100;
                    $config['height']   = 100;
                    $config['thumb_marker']   = '';
                    $config['maintain_ratio'] = FALSE;
                    $config['new_image'] = $this->config->item('upload_dir').$user_id.'/thumb/'.$data['upload_data']['file_name'];

                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();

					$update['profile_pic'] = $data['upload_data']['file_name'];
				}
				else {
					$data['alert'] = $this->upload->display_errors();	
				}
			}
			
			if($this->Users_model->update($user_id, $update)){
				// set the timezone session
				$this->session->set_userdata(array('timezone' => $update['utc_timezoneoffset']));
				$data['alert'] = '<div class="alert alert-success" role="alert">Success! Profile settings updated.</div>';	
			}
		}

		$info = $this->Users_model->get($this->user_id);
		$data['title'] = 'Profile Settings';
		$data['user_dir'] = $data['user_id'] = $user_id;
		$data['firstname'] = $info->first_name;
		$data['lastname'] = $info->last_name;
		$data['email'] = $info->email;
		$data['tel'] = $info->tel_number;
		$data['job'] = $info->job_title;
		$data['about_me'] = $info->about_me;
		$data['profile_pic'] = $info->profile_pic;
		$data['timezone'] = $info->utc_timezoneoffset;

		$this->load->view('account/profile', $data);
	}

	// private function process_email_ForgotPassword($email_from, $email_to, $ext_data,$token)
	// {

	// 	if(is_array($ext_data)){
	// 		$ext_data = (object)$ext_data;
	// 	}
	// 	$data = array();
	// 	$data['password'] = $this->encrypt->decode($ext_data->hash); 
	// 	$data['token'] = $token;
		
	// 	$subject = 'Password Recovery';
	// 	$email_body = $this->get_email_body_ForgotPassword($data);
		
	// 	$headers = "From:" . $email_from . "\r\n";
	// 	$headers .= "MIME-Version: 1.0\r\n";
	// 	$headers .= "Content-Type: text/html; charset=utf-8\r\n"; 
	// 	$headers .= 'Cc: '.$email_to.' ' . "\r\n";
	// 	$headers .= 'Bcc: '.$email_from. "\r\n";
	// 	return $this->send_email_ForgotPassword($email_to, $subject, $email_body, $headers, $ext_data->user_id);
	// } 
	
	
	// private function get_email_body_ForgotPassword($data = array())
	// {
	// 	$content = $this->load->view('account/email_template/forgot_password', $data, TRUE);
	// 	return $content;
	// }
	
	// private function send_email_ForgotPassword($email_to, $subject, $email_body, $headers, $user_id )
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
	
	function settings(){
		if(! $this->session->userdata('logged_in')) 
			redirect('account/sign_in');


		if(! is_account_owner($this->user_id))
		{
			redirect('account/profile');
		}

		$this->load->model('Users_model');
		$this->load->model('Account_model');

		
		$account_info = $this->Account_model->get_by('account_owner_id', $this->user_id);

		$data['title'] = "Account Settings";
		$data['alert'] = '';

		$user = $account = $success = array();

		if($this->input->post()){
			$user['email'] = $this->input->post('email');
			$account['name'] = $this->input->post('account_name');
			

			$this->Users_model->validate = array(
				array( 	'field' => 'email',
	               		'label' => 'Email',
	               		'rules' => 'required|valid_email' ),
			);

			// removed
			//$this->Organisation_model->update($organ->organ_id, array('name' => $this->input->post('organ_name')), TRUE);

			if($this->Account_model->update_by(array('account_owner_id' => $this->user_id), $account))
			{
				$success[] = 1;
			}

			// update user info
			if($this->Users_model->update($this->user_id, $user))
			{
				$success[] = 2;	
			}

			if(! empty($success)){
				$this->session->set_flashdata('alert_msg', '<div class="alert alert-success">Account settings saved!</div>');
				redirect('account/settings');
			}
			
		}

		$data['account_name'] = $account_info->name;
		$data['email'] = user_info('email', $account_info->account_owner_id);
		

		$this->load->view('account/settings', $data);
	}

	function change_password($my_user_id=null){
		$this->load->model('Users_model');

		$response = array();

		if(! is_null($my_user_id)){
			$user_id = $my_user_id;
		}
		else{
			$user_id = $this->session->userdata('user_id');	
		}
		
		$userinfo = $this->Users_model->get_by('user_id', $user_id);	

		if($this->input->post()){
			$current 	= $this->input->post('oldpass');
			$new 		= $this->input->post('newpass');

			if($current == $this->encrypt->decode($userinfo->hash))
			{
				$this->Users_model->update($user_id, array('hash' => $this->encrypt->encode($new)), TRUE);
				
				$response['msg'] = 'Success! your password has been changed.';
				$response['action'] = 'success';
			}
			else{
				$response['msg'] = 'Your current password is incorrect.';
				$response['action'] = 'failed';
			}
		}
		echo json_encode($response);
	}

	function sign_up(){
		if($this->session->userdata('logged_in') === TRUE){
			redirect('welcome');
		}

		$this->load->model('Users_model');
		$this->load->model('Account_model');
		$this->load->model('Organisation_model');
		// $this->load->model('Plan_model');
		$this->load->model('Notification_model');
		$data['error'] = '';
		$data['title'] = "Signup";
		
		if($this->input->post()){
			// user should accept the terms and conditions
			if($this->input->post('terms') == 'on')
			{
				$new_user = array(
					'user_type' 	=> 'admin',
					'is_active' 	=> 0,
					'is_confirmed' 	=> 1,
					'deleted' 		=> 0,
					'first_name' 	=> $this->input->post('firstname'),
					'last_name' 	=> $this->input->post('lastname'),
					'username' 		=> "NA",
					'email' 		=> $this->input->post('email'),
					'hash' 			=> $this->encrypt->encode($this->input->post('password')),
					'ip_address' 	=> $_SERVER['REMOTE_ADDR'],
					'job_title' 	=> $this->input->post('job'),
					'company' 		=> $this->input->post('company'),
					'utc_timezoneoffset' => $this->input->post('timezone'),
					'tel_number' 	=> $this->input->post('tel'),
				);

				$user_added = $this->Users_model->insert($new_user);
				if($user_added)
				{
					// create a user account
					$account_id = $this->Account_model->create_user_account($user_added);
					
					// update the master_account_id. we want to skip the validation
					$this->Users_model->update($user_added, array(
						'master_account_id' => $account_id
					), TRUE);

					
					$organisation_id = $this->Organisation_model->organisation_add($user_added, 'My Organisation');
				

					//create user notification for new added user
					$data = array(
						'user_id' => $user_added ,
						'organ_id' => $organisation_id,
						'notification_type_id' => '1',
						'text' => 'Welcome to Business Planner',
						'link_value' => 'account/',
						'status' => 0,
						'enteredon' => date("Y-m-d H:i:s")	
					);
					$this->Notification_model->success($data);	
					
					redirect('account/signup_success');
				}
			}
			else{
				$data['error'] = 'You should accept the Terms and Conditions of this website.';
			}
			
		}
		$this->load->view('account/sign_up', $data);
	}

	// page shown after successful signup
	function signup_success(){
		if($this->session->userdata('logged_in') === TRUE){
			redirect('welcome');
		}

		$data['title'] = "Account creation successful";
		$this->load->view('account/signup_success', $data);
	}

	function sign_in(){
		if($this->session->userdata('logged_in') === TRUE){
			redirect('welcome');
		}

		$this->load->model('Users_model');
		$this->load->model('Organisation_model');
		$this->load->model('Plan_model');
		$data['error'] = '';
		$data['title'] = "Sign In to GoalDriver";

		if($this->input->post()){
			//$username = $this->input->post('username');
			$email_add = $this->input->post('email');
			$password  = $this->input->post('password');

			if($email_add != '' && $password != ''){
				$userinfo = $this->Users_model->get_by('email', $email_add);	
				//echo $password .'-'. $this->encrypt->decode($userinfo->hash);
				if(! empty($userinfo) && $userinfo->deleted == 0){
					// check if password belongs to this email
					if($password == $this->encrypt->decode($userinfo->hash)){
						// userinfo are legit, set a session to let them access their account
						
						$userdata = array(
		                   	'user_id'  	=> $userinfo->user_id,
		                   	'email'     => $userinfo->email,
		                   	'user_type' => $userinfo->user_type,
		                   	'account_id' => $userinfo->master_account_id,
		                   	'logged_in' => TRUE,
		                   	'timezone' => $userinfo->utc_timezoneoffset
		                );

		                // remember me?
	                   	if($this->input->post('remember_me') == 1){
	                   		$userdata['sess_expiration'] = 86400; // 1 day
	               		}

						$this->session->set_userdata($userdata);

						// update the last_logged_in field, skip validation
						$this->Users_model->update($this->session->userdata('user_id'), array('last_logged_in' => date('Y-m-d H:i:s')), TRUE);

						// all session set, now redirect them to their dashboard
						if($userinfo->user_type == 'superadmin'){
							redirect('admin');
						}
						else{
							redirect('user-settings/organisations');	
						}
						
						
					}
					else{
						$data['error'] = 'Email and password don\'t match!';
					}
				}
				else{
					$data['error'] = 'Email and password not found!';
				}
			}
		}

		$this->load->view('account/sign_in', $data);
	}

	// Logout a user
	function logout(){
		// clear all sessions
		$newdata = array(
                   'user_id'  => '',
                   'email'     => '',
                   'logged_in' => FALSE
               );
		$this->session->set_userdata($newdata);
		$this->session->sess_destroy();

		redirect('account/sign_in');
	}


	function send_mail() { 

		$_POST = json_decode(file_get_contents('php://input'), true);


		$this->load->model('Organisation_model');

		$sender_mail = $_POST['sender_email'];
		$sender_name = $_POST['sender_name'];
		$partcipant_email = $_POST['participant_email'];
		$partcipant_name = $_POST['participant_name'];
		$meeting_title = $_POST['meeting_title'];
		$meeting_time_duration = $_POST['meeting_time_duration']; 

		$user_id = $this->session->userdata('user_id');
		$organ = $this->Organisation_model->get_organ_id($user_id);


		$data = array();
		$data['organisation_name'] = organ_info("name", $organ);
		$data['sender_name'] = $sender_name ;
		$data['sender_mail'] = $sender_mail;
		$data['participant_name'] = $partcipant_name;
		$data['participant_email'] = $partcipant_email ;
		$data['meeting_title'] = $meeting_title ;
		$data['meeting_time_duration'] = $meeting_time_duration ;
				

		$subject = 'Reminder';
		$email_body = $this->get_email_body($data);
		
		$headers = "From:" . $sender_mail . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n"; 
		$headers .= 'Cc: '.$partcipant_email.' ' . "\r\n";
		$headers .= 'Bcc: '.$sender_mail . "\r\n";

		return $this->send_email($partcipant_email, $subject, $email_body, $headers );
		
    } 

    private function get_email_body($data = array())
	{
		$content = $this->load->view('dashboard/email_template/reminder_message', $data, TRUE);
		return $content;
	}

	private function send_email($email_to, $subject, $email_body, $headers )
	{
		if(@mail($email_to, $subject, $email_body, $headers))
		{
			$this->response_code = 0;	
			$this->response_message = "Email sent successfully.";	
		}
		else
		{
			$this->response_code = 1;	
			$this->response_message = "Error in sending Email.";
		}

		 	echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
			));
	}
	
	function remove_profile_photo()
	{	
		if($this->input->post())
		{
			$this->load->model('Users_model');
			$user_id = $this->input->post('user_id');

			$profile_pic = FCPATH.'uploads/'.$user_id.'/'.user_info('profile_pic', $user_id);
			$profile_pic_thumb = FCPATH.'uploads/'.$user_id.'/thumb/'.user_info('profile_pic', $user_id);
			
			if(unlink($profile_pic) && unlink($profile_pic_thumb))
			{
				if( $this->Users_model->remove_profile_pic($user_id) )
				{
					echo json_encode(array(
						'action' => 'success'
					));
				}
			}
			else{
				echo json_encode(array(
						'action' => 'falied'
					));
			}
		}
	}

	function mypass($password=null)
	{
		echo $this->encrypt->decode('dS2l7Nudppui/9uqUK+omj8TIj+QxFB6dpm0lz2BVOySPvWIMQv4paKqBd4E6TMvXzZ2UTMRHtKBuEhXA19lCg==');
	}

	public function save_new_password()
	{
		/* *
		David : Added conditions to prevent hacking. 
		* */
		
		$this->load->model('Users_model');
		$this->load->model('Passwordtoken_model');
		
		$this->response_code = 1;	
		$this->response_message = "Failed to update.";
			
		if(isset($_POST['id']) && isset($_POST['token_id']) && isset($_POST['password']))
		{
			$id = (int)$_POST['id'];	
			$password =  $_POST['password'];
			$user_id = $this->Passwordtoken_model->get_value($_POST['token_id'], "user_id", 1);

			$first_name = user_info('first_name', $user_id);
			$email = user_info('email', $user_id);
			
			if($user_id != false && $user_id == $id){
				$update_data = array(
					'hash' => $this->encrypt->encode($password)
				);
				$inserted = $this->Users_model->save_new_password($update_data,$id);
				
				if($inserted > 0 )
				{
					/******************************************************
					 * Password changed email notification.
					 * by james
					 *******************************************************/
					$mail_notif = array();
					$mail_notif['your_name'] = $first_name;
					$mail_notif['your_email'] = $email;
					
					$this->mail_notification->send('password_changed', array($user_id), $mail_notif);

					$this->response_code = 0;	
					$this->response_message = "New password successfully save.";	
				}
			}
		
		}	
		echo json_encode(array(
			"error"			=> $this->response_code,
			"message"		=> $this->response_message,
		));
	}


	
	
}