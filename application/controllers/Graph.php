<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Graph extends CI_Controller {
	var $user_id = 0;
	var $organ_id = 0;
	var $plan_id = 0;
	var $is_organisation_owner = false;
	private $kpi_permission = array();
	public $kpi_permission_name = "hidden";
	public $frequencies = array("daily", "weekly", "monthly", "quarterly", "yearly");
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in')) 
		{
			redirect('account/sign_in');
		}
		
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');
		$this->plan_id = $this->session->userdata('plan_id');
		
		if($this->organ_id == null)
		{
			$session_data = array(
				'error_message'	=> "Please select an organisation first."
			);	
			$this->session->set_userdata($session_data);	
			redirect("user-settings/organisations"); 
		}
  
		$this->load->library('Kpi_calendar');
		$this->load->model('Users_model');
		$this->load->model('Kpi_model');
		$this->load->model('Organisationusers_model');
		$this->load->model('Graph_model');
		$this->load->model('Kpiusers_model');
		$this->load->model('Organisation_model');
		
		$this->kpi_permission = check_access($this->user_id, $this->organ_id, 5);
		$this->is_organisation_owner = $this->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
		if($this->is_organisation_owner){
			$this->kpi_permission = array(
				array(
					"hidden" => 0,
					"readonly" => 0,
					"readwrite" => 1
				)
			);
		}
		if(!empty($this->kpi_permission)){
			if($this->kpi_permission[0]['hidden'] == 1){
				$array = array("response"=>"error", "message"=>"No enough permission.");
				die(json_encode($array));
			}
			if($this->kpi_permission[0]['readwrite'] == 1){
				$this->kpi_permission_name = "readwrite";
			}
			if($this->kpi_permission[0]['readonly'] == 1){
				$this->kpi_permission_name = "readonly";
			}
		}else{
			$array = array("response"=>"error", "message"=>"No enough permission.");
			die(json_encode($array));
		}
	}
	
	
	public function ajax_get_graphs(){
		
		if(isset($_POST['action']) && $_POST['action'] == "get_graphs")
		{	
			if($this->kpi_permission_name == "readwrite")
			{
				$graphs = $this->Graph_model->get_graphs($this->organ_id, $this->user_id);
			}
			else
			{
				$graphs = $this->Graph_model->get_shared_graphs($this->organ_id, $this->user_id);
			}
			
			if($graphs)
			{
				$new_graphs = array();
				foreach($graphs as $graph)
				{
					$graph->entered = gd_date($graph->entered, "Y-m-d H:i:s");
					$new_graphs[] = $graph;	
				}
				
				$array = array( "result"=> "success", "count" => count($new_graphs), "data" => $new_graphs );
			}
			else
			{
				$array = array( "result"=> "success", "count" => 0, "data" => array());
			}	
			
			die(json_encode($array));
		}
	}
	
	
	public function ajax_get_graph_types(){
		if(isset($_POST['action']) && $_POST['action'] == "get_graph_types")
		{	
			$graph_types = $this->Graph_model->get_graph_types();
			
			if($graph_types)
			{
				$array = array( "result"=> "success", "count" => count($graph_types), "data" => $graph_types );
			}
			else
			{
				$array = array( "result"=> "success", "count" => 0, "data" => array());
			}	
			
			die(json_encode($array));
		}	
	}
	
	
	public function ajax_add_graph()
	{
		$this->is_readwrite();
		
		if(isset($_POST['action']) && $_POST['action'] == "add_graph")
		{	
			$graph_name = $_POST["graph_name"];
			$graph_description = $_POST["graph_description"];
			$graph_type_id = $_POST["graph_type"];
			$kpi_id = (int)$_POST["graph_kpi_id"];
			$show_on_dash = (@$_POST["show_on_dash"] == "true")? 1 : 0;
			$show_average = (@$_POST["show_average"] == "true")? 1 : 0; 
			$show_break_down = (@$_POST["show_break_down"] == "true")? 1 : 0;
			$show_gauge_on_dash = (@$_POST["show_gauge_on_dash"] == "true")? 1 : 0;
			$users = @$_POST["users"];
			$shared_users = @$_POST["shared_users"];
			$reset_frequency = $_POST["reset_frequency"];
			$tmp_final_users = array();
			$tmp_users = array();
			
			$kpi_users = $this->Kpiusers_model->get_kpi_users($this->organ_id, $kpi_id);
			$check_kpi = $this->Kpi_model->get_kpis($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);
			$final_reset_frequency =  $check_kpi->frequency;
			
			if(in_array($reset_frequency, $this->frequencies)){
				$final_reset_frequency = $reset_frequency;
			}
			if($kpi_users == false || empty($users) || $check_kpi == false){
				die(json_encode($array));
			}
			/* check if has graph users */
			foreach($kpi_users as $kpi_user){
				$tmp_users[] = $kpi_user->user_id;
			}
			
			foreach($users as $user){
				$user_id = (int)$user['id'];
				if(in_array($user_id, $tmp_users)){
					$tmp_final_users[] = array('id'=>$user_id);
				}
			}
			
			if(count($tmp_final_users) == 0){
				die(json_encode($array));
			}
			$array = array("result"=>"error", "message"=>"Failed to add new graph.");
			
			if($check_kpi){
				$kpi_frequency = $check_kpi->frequency;
				
				
				$add = $this
				->Graph_model
				->graph_add($graph_name, $graph_description, $graph_type_id, $kpi_id, $this->user_id, $this->organ_id, $show_on_dash, $show_average, $show_break_down, $show_gauge_on_dash, $reset_frequency);
				
				if($add)
				{
					$add_graph_users = $this->Graph_model->graph_users_add($this->organ_id, $this->plan_id, $add->graph_id, $kpi_id, $users);
					$shared_users_final= $this->Graph_model->shared_users_add($add->graph_id, $shared_users, $this->organ_id);
					$graph_data = $add;
					$array = array("result"=>"success", "message" => "Added successfully.", "graph_id"=> $graph_data->graph_id, "data"=> $graph_data);
				}
			}
			
			die(json_encode($array));
		}
	}
	
	
	public function ajax_delete_graph(){
		$this->is_readwrite();	
		
		if(isset($_POST['action']) && $_POST['action'] == "delete_graph")
		{	
			$graph_id = $_POST['graph_id'];	
			$delete = $this->Graph_model->graph_delete($graph_id);
			
			if($delete)
			{
				$array = array("result"=>"success", "message" => "Deleted successfully.", "graph_id"=> $graph_id);
				die(json_encode($array));
			}
			else
			{
				$array = array("result"=>"error", "message"=>"Failed to delete graph.");
				die(json_encode($array));
			}
		}
		
	}
	
	public function ajax_edit_graph(){
		$this->is_readwrite();	
		
		if(isset($_POST['action']) && $_POST['action'] == "edit_graph")
		{	
			$graph_id = (int)@$_POST['graph_id'];
			$graph_name = @$_POST["graph_name"];
			$graph_description = @$_POST["graph_description"];
			$graph_type_id = (int)@$_POST["graph_type_id"];
			$kpi_id = (int)@$_POST["kpi_id"];
			$show_on_dash = (@$_POST["show_on_dash"] == "true")? 1 : 0;
			$show_average = (@$_POST["show_average"] == "true")? 1 : 0; 
			$show_break_down = (@$_POST["show_break_down"] == "true")? 1 : 0;
			$show_gauge_on_dash = (@$_POST["show_gauge_on_dash"] == "true")? 1 : 0;
			$users = @$_POST["users"];
			$shared_users = @$_POST["shared_users"];
			$reset_frequency = $_POST["reset_frequency"];
			
			$array = array("result"=>"error", "message"=>"Failed to update graph.");
			$check_kpi = $this->Kpi_model->get_kpis($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);
			
			if($check_kpi){
				$kpi_frequency = $check_kpi->frequency;
				/* update users */
				$update_users = $this->Graph_model->graph_users_add($this->organ_id, $this->plan_id, $graph_id, $kpi_id, $users );
				/* update shared users */
				$shared_users_final= $this->Graph_model->shared_users_add($graph_id, $shared_users, $this->organ_id);
				
				/* update graph */
				$update = $this->Graph_model->graph_update($graph_name, $graph_description, $graph_type_id, $kpi_id, $this->user_id, $this->organ_id, $graph_id, $show_on_dash, $show_average, $show_break_down, $show_gauge_on_dash, $kpi_frequency, $reset_frequency);
				
				if($update)
				{
					$array = array(
						"result"=>"success", 
						"message" => "Updated successfully.", 
						"graph_id"=> $graph_id, 
						"data"=> $update
					);
					
					if($this->is_organisation_owner){
						$array["update_users"] = $update_users;
					}
				}
			}
			die(json_encode($array));
		}
	
	}
	
	
	public function ajax_get_assigned_users(){
		$this->is_readwrite();
		
		if(isset($_POST['action']) && $_POST['action'] == "get_assigned_users")
		{	
			$data = array();
			$kpi_id = (int)$_POST['kpi_id'];
			$graph_id = (int)$_POST['graph_id'];
			
			$kpi_users = $this->Kpiusers_model->get_kpi_users($this->organ_id, $kpi_id);
			$graph_users =  $this->Graph_model->get_graph_users($graph_id);	
			$tmp_graph_users = array();
			if($graph_users){
				foreach($graph_users as $user){
					$tmp_graph_users[] = $user->user_id;
				}
			}
			
			$tmp_final_users = array();
			foreach($kpi_users as $user){
				$is_graph_user = (!empty($tmp_graph_users) && in_array($user->user_id, $tmp_graph_users)) ? true : false;
				$tmp_final_users[] = array(
					"user_id"=> $user->user_id, 
					"first_name"=> $user->first_name, 
					"last_name" => $user->last_name, 
					"is_graph_user" => $is_graph_user
				);
			}
			
			$array = array("result"=>"success", "count"=> count($tmp_final_users), "users"=> $tmp_final_users);
			die(json_encode($array));
		}
	}
	
	public function ajax_get_graph_settings(){
		if(isset($_POST['action']) && $_POST['action'] == "get_graph_settings")
		{
			$graph_id = (int)$_POST['graph_id'];
		
			$graph = $this->Graph_model->get_graphs($this->organ_id, $this->user_id, $graph_id);
			
			/* get graph default users */
			$kpi_users = $this->Kpiusers_model->get_kpi_users($this->organ_id, $graph->kpi_id);
			$graph_users =  $this->Graph_model->get_graph_users($graph_id);	
			$tmp_graph_users = array();
			if($graph_users){
				foreach($graph_users as $user){
					$tmp_graph_users[] = $user->user_id;
				}
			}
			if($this->kpi_permission_name == "readwrite"){
				$tmp_final_users = array();
				foreach($kpi_users as $user){
					$is_graph_user = (!empty($tmp_graph_users) && in_array($user->user_id, $tmp_graph_users)) ? true : false;
					$tmp_final_users[] = array(
						"user_id"=> $user->user_id, 
						"first_name"=> $user->first_name, 
						"last_name" => $user->last_name, 
						"is_graph_user" => $is_graph_user
					);
				}
			}else{
				$tmp_final_users = array();
				foreach($graph_users as $user){
					$tmp_final_users[] = array(
						"user_id"=> $user->user_id, 
						"first_name"=> $user->first_name, 
						"last_name" => $user->last_name, 
						"is_graph_user" => true
					);
				}
			}
			
			$result = array(
				"result" => "success",
				"users" => $tmp_final_users,
				"users_count" => count($tmp_final_users),
				"graph_id" => $graph_id
			);
			
			die(json_encode($result));
		}
	}
	
	public function ajax_get_shared_users_default()
	{
		$this->is_readwrite();
		if(isset($_POST['action']) && $_POST['action'] == "get_default_shared_users")
		{	
			$users = $this->Organisationusers_model->get_organisation_users($this->organ_id);
			$user_count = ($users == false) ? 0 : count($users);
			$result = array(
				"result" => "success",
				"users" => $users,
				"count" => $user_count
			);
			
			die(json_encode($result));
		}		
		
	}
	
	public function ajax_shared_to_users(){
		$this->is_readwrite();
		
		if(isset($_POST['action']) && $_POST['action'] == "shared_to_users")
		{
			$graph_id = (int)$_POST['graph_id'];
			$users = $this->Graph_model->get_shared_users($this->organ_id, $graph_id);
			$user_count = ($users == false) ? 0 : count($users);
			$result = array(
				"result" => "success",
				"users" => $users,
				"count" => $user_count
			);
			
			die(json_encode($result));
		}
	}
	
	private function is_readwrite(){
		if($this->kpi_permission[0]['readwrite'] != 1 || ($this->kpi_permission[0]['readwrite'] == 1 && $this->kpi_permission[0]['readonly'] != 0))
		{
			$array = array("response"=>"error", "message"=>"No enough permission.");
			die(json_encode($array));
		}	
	}
}