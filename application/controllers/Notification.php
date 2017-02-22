<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller{
	var $user_id = 0;
	var $organ_id = 0;
	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in')) 
		{
			redirect('account/sign_in');
		}
		
		$this->load->model('Notification_model');
		
		$this->user_id = $this->session->userdata("user_id");
		$this->organ_id = $this->session->userdata("organ_id");
		
	}
	

	public function update_userNotification()
	{
		if(isset($_POST['action']) && $_POST['action'] == "update_notif")
		{
			$item['unseen_notification'] = $this->Notification_model->get_all_unseen_notification($this->user_id);

			foreach ($item['unseen_notification'] as $value ) {
				/* update the status on the unseen */
				$this->Notification_model->update_all_notification_status($value->notification_id);
			}	
			die(json_encode($item));
		}
		else
		{
			show_404_page("404_page" );
		}	
	}
	
	
	
	public function get_userNotification()
	{
		/* echo var_dump($_POST); */
		if(isset($_POST['action']) && $_POST['action'] == "get_notif")
		{
			$item = array();
			$item['user_notification'] = $this->Notification_model->get_all_Notification($this->user_id);
			$item['count'] = $this->Notification_model->count_Notification($this->user_id);
			
			die(json_encode($item));	
		}
		else
		{
			show_404_page("404_page" );
		}
	}

	public function get_userNotification_by_organ()
	{
		/* echo var_dump($_POST); */
		if(isset($_POST['action']) && $_POST['action'] == "get_notif")
		{
			$item = array();
			$item['user_notification'] = $this->Notification_model->get_all_Notification_by_organ($this->user_id,$this->organ_id);
			$item['count'] = $this->Notification_model->count_Notification($this->user_id);
			
			die(json_encode($item));	
		}
		else
		{
			show_404_page("404_page" );
		}
	}
	
	public function delete_notification()
	{
		if(isset($_POST['ids']) && isset($_POST['action']) && $_POST['action'] == "delete_notif")
		{
			$this->response_code = 1;
			$this->response_message = "Failed to delete.";
			$deleted = $this->Notification_model->delete_notification($_POST['ids'], $this->user_id);
			
			if($deleted == true)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted.";
			}
			
			$array = array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message
			);
			
			die(json_encode($array));
		}
		else
		{
			show_404_page("404_page" );
		}
		
	}

	
	public function delete_all_notification()
	{
		
		if(isset($_POST['action']) && $_POST['action'] == "delete_notif")
		{
			$this->response_code = 1;
			$this->response_message = "Failed to delete.";
			$deleted = $this->Notification_model->delete_all_notification($this->user_id);

			if($deleted == true)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted.";
			}
			
			$array = array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
			);
			
			die(json_encode($array));	
		}
		else
		{
			show_404_page("404_page" );
		}
	}
	
	public function delete_all_notification_by_organ()
	{
		
		if(isset($_POST['action']) && $_POST['action'] == "delete_notif")
		{
			$this->response_code = 1;
			$this->response_message = "Failed to delete.";
			$deleted = $this->Notification_model->delete_all_notification_by_organ($this->organ_id, $this->user_id);

			if($deleted == true)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted.";
			}
			
			$array = array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
			);
			
			die(json_encode($array));	
		}
		else
		{
			show_404_page("404_page" );
		}
	}
	
} 
?>