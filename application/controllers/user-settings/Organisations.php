<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organisations extends CI_Controller{
	var $user_id = 0;
	var $organ_id = 0;
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in')) 
		{
			redirect('account/sign_in');
		}
		
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');
		
		$this->load->model('Users_model');
		//$this->load->model('Account_model');
		$this->load->model('Organisationusers_model');
		$this->load->model('Organisation_model');
		$this->load->model('Subsection_model');
		//$this->load->model('Notification_model');
	}
	
	
	
	public function index()
	{
		$data['title'] = 'Organisations';
		$data['organisations'] = $this->Organisation_model->get_organisations_by_user($this->user_id);
		$data['is_owner'] = $this->Organisation_model->is_owner($this->user_id);
		$data['user_id'] = $this->user_id;
		$js = array( 
			base_url() ."public/app/admin/organisations_datatable.js"
		);
		
		if($data['is_owner'])
		{
			$js[] = base_url() ."public/app/admin/organisations.js";
		}
		$data['js'] = $js;
		
		
		$this->load->view('admin/organisations/index', $data);
	}
	
	
	
	public function ajax_add_organisation(){
		if(isset($_POST['action']) && $_POST['action'] == "add_organisation")
		{
			$organisation_name = @$_POST['organisation_name'];
			$organ_id = @$_POST['copy_organisation']; // organ id to copy
			$owner_id = $this->user_id;
			$date = date('Y-m-d H:i:s');

			//print_r($_POST);die();
			
			if(empty($organisation_name) || trim($organisation_name) == ""){
				$array = array("result"=>"error", "message"=>"Organisation Name is required.");
				die(json_encode($array));
			}
			
			if(!$this->Organisation_model->is_owner($this->user_id))
			{
				$array = array("result"=>"error", "message"=>"No enough permission.");
				die(json_encode($array));
			}
			
			if(strlen($organisation_name) > 45)
			{
				$array = array("result"=>"error", "message"=>"Name is too long.");
				die(json_encode($array));
			}
			
			/*create organisation*/
			if($organ_id != ''){
				$add = $this->Organisation_model->organisation_copy_existing($this->user_id, $organisation_name, $organ_id);
			}else{
				$add = $this->Organisation_model->organisation_add($this->user_id, $organisation_name);	
			}
			
			
			if($add)
			{

				$organisation_id = $add;	
				$user_info = $this->Users_model->get_user_filter_by_id($this->user_id);
				$last_viewed = date("F d, Y", strtotime($date)) . " by ".$user_info[0]->first_name . " ". $user_info[0]->last_name;
				$edit_delete = "";
				
				$data = array($organisation_name, $last_viewed, "Admin", $edit_delete);
				$array = array("result"=>"success", "message" => "Added successfully.", "organ_id"=> $organisation_id, "data"=> $data);
				die(json_encode($array));
			}
			else
			{
				$array = array("result"=>"error", "message"=>"Failed to add new organisation.");
				die(json_encode($array));
			}
		}
	}
	
	
	
	
	public function ajax_edit_organisation()
	{
		if(isset($_POST['action']) && $_POST['action'] == "edit_organisation")
		{
			$organ_id = (int)$_POST['organ_id'];
			$organisation_name = $_POST['organisation_name'];
			$date = date('Y-m-d H:i:s');
			
			if(!$this->has_owner_access($organ_id))
			{
				$array = array("result"=>"error", "message"=>"No enough permission.");
				die(json_encode($array));
			}
			
			if(strlen($organisation_name) > 45)
			{
				$array = array("result"=>"error", "message"=>"Name is too long.");
				die(json_encode($array));
			}
			
			$organisation_name = $this->db->escape_str($organisation_name);
			/*update organisation*/
			$update = $this->Organisation_model->update($organ_id, array('name' => $organisation_name, "updated_by" => $this->user_id, "updated" => $date), TRUE);
			
			if($update)
			{
				$user_info = $this->Users_model->get_user_filter_by_id($this->user_id);
				$last_viewed = date("F d, Y", strtotime($date)) . " by ".$user_info[0]->first_name . " ". $user_info[0]->last_name;
				
				$data = array($organisation_name, $last_viewed);
				$array = array("result"=>"success", "message" => "Updated successfully.", "organ_id"=> $organ_id, "data"=> $data);
				die(json_encode($array));
			}
			else
			{
				$array = array("result"=>"error", "message"=>"Failed to update organisation.");
				die(json_encode($array));
			}
		}
	}
	
	
	
	
	public function ajax_delete_organisation()
	{
		if(isset($_POST['action']) && $_POST['action'] == "delete_organisation")
		{
			$organ_id = (int)$_POST['organ_id'];	
			
			if(!$this->has_owner_access($organ_id))
			{
				$array = array("result"=>"error", "message"=>"No enough permission.", "data"=>$this->db->last_query());
				die(json_encode($array));
			}
			
			/*delete*/
			$delete = $this->Organisation_model->organisation_delete($this->user_id, $organ_id);
			
			if($delete)
			{
				$array = array("result"=>"success", "message" => "Deleted successfully.", "organ_id"=> $organ_id);
				die(json_encode($array));
			}
			else
			{
				$array = array("result"=>"error", "message"=>"Failed to delete organisation.");
				die(json_encode($array));
			}
		}
	}
	
	
	
	public function has_owner_access($organ_id)
	{
		return $this->Organisation_model->get_owner_permission($organ_id, $this->user_id);
	}
	
	
	public function ajax_get_organisations()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_organisations")
		{
			
			if($_POST['in_myplanner'] == "true")
			{
				$organ_id = 0;
			}
			else
			{
				$organ_id = $this->organ_id;	
			}
			
			$organisations = $this->Organisation_model->get_organisations_by_user_simple($this->user_id, $organ_id);
			
			if($organisations)
			{
				$array = array("result"=>"success", "message" => "Organisations List.", "count" => 1, "data"=> $organisations);
				die(json_encode($array));
			}
			else
			{
				$array = array("result"=>"success", "message"=>"No organisation available.", "count" => 0);
				die(json_encode($array));
			}
		}
	}
	
	
	public function change_organisation($organ_id)
	{
		$owner = $this->Organisation_model->organisation_login($this->user_id, $organ_id);
		
		if(!$owner)
		{
			redirect("user-settings/organisations");
			die();
		}
		
		$newdata = array(
			'organ_id' 	=> $organ_id,
			'plan_id'	=> $owner->plan_id
		);
		
		$this->session->set_userdata($newdata);
		redirect("dashboard");
	}

	public function change_organisation_main($encrypted_organ_id)
	{
		$organ_id = decrypt($encrypted_organ_id);
		$owner = $this->Organisation_model->organisation_login($this->user_id, $organ_id);
		
		if(!$owner)
		{
			redirect("user-settings/organisations");
			die();
		}
		
		$newdata = array(
			'organ_id' 	=> $organ_id,
			'plan_id'	=> $owner->plan_id
		);
		
		$this->session->set_userdata($newdata);
		redirect("dashboard");
	}
	
	
	
	
	
	
	
	
} 
?>