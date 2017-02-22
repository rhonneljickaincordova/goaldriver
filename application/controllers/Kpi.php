<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kpi extends CI_Controller {
	private $user_id = 0;
	private $organ_id = 0;
	private $plan_id = 0;
	private $is_organisation_owner = false;
	private $kpi_permission = array();
	
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
		$this->load->library('Kpi_data');
		$this->load->library('Highcharts');
		
		$this->load->model('Users_model');
		$this->load->model('Kpi_model');
		$this->load->model('Kpidata_model');
		$this->load->model('Organisation_model');
		$this->load->model('Organisationusers_model');
		$this->load->model('Kpiusers_model');
		
		$this->is_organisation_owner = $this->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
		$this->kpi_permission = check_access($this->user_id, $this->organ_id, 5);
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
				redirect("dashboard");
			}
			if($this->kpi_permission[0]['readwrite'] == 1){
				$this->kpi_permission_name = "readwrite";
			}
			if($this->kpi_permission[0]['readonly'] == 1){
				$this->kpi_permission_name = "readonly";
			}
		}else{
			redirect("dashboard");
		}
	}
	
	
	

	
	public function index()
	{
		$data['kpi_permission_name'] = $this->kpi_permission_name;

		$data['title'] = 'Key Performance Indicators (KPIs)';
		$data['kpis'] = false;
		$data['kpis_count'] = $this->Kpi_model->get_kpis_count($this->user_id, $this->organ_id, $this->plan_id);
		$data['enterData_daily_dates'] = $this->Kpidata_model->enterData_dates('daily', 3, 1);
		$data['enterData_weekly_dates'] = $this->Kpidata_model->enterData_dates('weekly', 3, 1);
		$data['enterData_monthly_dates'] = $this->Kpidata_model->enterData_dates('monthly', 3, 1);
		$data['enterData_quarterly_dates'] = $this->Kpidata_model->enterData_dates('quarterly', 3, 1);
		$data['enterData_yearly_dates'] = $this->Kpidata_model->enterData_dates('yearly', 3, 1);
		
		
		$js = array();
		if($this->kpi_permission[0]['readwrite'] == 1 && $this->kpi_permission[0]['readonly'] == 0)
		{
			$kpis = $this->Kpi_model->get_kpis($this->organ_id, $this->plan_id, $this->user_id);
			$js[] = base_url() ."public/app/public/kpi/kpi.js";
		}else{
			$kpis = $this->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id);
		}
		
		if($kpis != false && !empty($kpis)){
			$new_kpis = array();
			foreach($kpis as $kpi){
				$kpi->entered = gd_date($kpi->entered, "Y-m-d H:i:s");
				$kpi->kpi_days = array($kpi->in_sun, $kpi->in_mon,$kpi->in_tue,$kpi->in_wed,$kpi->in_thu,$kpi->in_fri,$kpi->in_sat);
				$new_kpis[] = $kpi;
			}
			$data['kpis'] = $new_kpis;
		}
		
		
		$js[] = base_url() ."public/app/public/kpi/kpi_datatable.js";
		$js[] = base_url() ."public/app/public/kpi/kpi_data_datatable.js";
		$js[] = base_url() ."public/app/public/kpi/kpi_data.js";		
		$js[] = base_url() ."public/app/public/kpi/graphs_datatable.js";
		$js[] = base_url() ."public/app/public/kpi/graphs.js";
		$js[] = base_url() ."public//highcharts/highcharts.js";
		$js[] = base_url() ."public//highcharts/highcharts-more.js";
		$js[] = base_url() ."public//highcharts/modules/solid-gauge.js";
		$js[] = base_url() ."public/highcharts/modules/exporting.js";
		$js[] = base_url() ."public/app/public/options/highchart_options.js";
		$js[] = base_url() ."public/app/public/kpi/dashboard.js";
		
		
		$data['js'] = $js;
		$data['css'] = array(base_url() ."public/app/css/goal.css");
		
		
		if($this->kpi_permission[0]['readonly'] == 1)
		{
			$this->load->view('kpi/member', $data);
		}
		else if($this->kpi_permission[0]['readwrite'] == 1)
		{
			$this->load->view('kpi/index', $data);	
		}
	}
	
	
	
	
	
	public function ajax_get_kpis()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_kpis"){
			$kpis = $this->Kpi_model->get_kpis($this->organ_id, $this->plan_id, $this->user_id);
			
			if($kpis){
				$array = array( "result"=> "success", "count" => count($kpis), "data" => $kpis );
			}else{
				$array = array( "result"=> "success", "count" => 0, "data" => array());
			}	
			
			die(json_encode($array));
		}
	}
	
	
	
	
	
	public function ajax_get_kpis_as_member()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_kpis_as_member"){
			$kpis = $this->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id);
			
			if($kpis){
				$array = array( "result"=> "success", "count" => count($kpis), "data" => $kpis );
			}else{
				$array = array( "result"=> "success", "count" => 0, "data" => array());
			}	
			
			die(json_encode($array));
		}
	}
	
	
	
	
	
	public function ajax_get_kpis_count()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_kpis_count"){
			$array = $this->Kpi_model->get_kpis_count($this->user_id, $this->organ_id, $this->plan_id);
			die(json_encode($array));
		}
	}
	
	
	
	
	
	public function ajax_add_kpi()
	{
		$this->is_readwrite();
		$error = false;
		if(isset($_POST['action']) && $_POST['action'] == "add_kpi")
		{
			$name = $_POST["name"];
			$icon = '';
			$description = $_POST["description"];
			$frequency = $_POST["frequency"];
			$format = $_POST["format"];
			$best_direction = $_POST["best_direction"];
			$target = $_POST["target"];
			$agg_type = $_POST["agg_type"];
			$users = @$_POST["users"];
			$users = $this->check_users_exist($users);
			
			
			if(trim($target) == ""){
				$target = null;
			}else if(!is_numeric($target)){
				$error = true;
			}
			
			$rags = $this->validate_rags();
			
			if($rags['result'] == "error"){
				$error = true;
			}else{
				$rag_1 = $rags['rag_1'];
				$rag_2 = $rags['rag_2'];
				$rag_3 = $rags['rag_3'];
				$rag_4 = $rags['rag_4'];
			}
			
				
			if($error == false){
				$add = $this->Kpi_model->kpi_add($this->organ_id, $this->plan_id, $this->user_id, $name, $icon, $description, $frequency, $format, $best_direction, $target, $agg_type, $rag_1, $rag_2, $rag_3, $rag_4, '', 0, 0);
				if($add)
				{
					$kpi_data = $add;	
					$kpi_data->kpi_days = implode(',',array(
											$kpi_data->in_sun,
											$kpi_data->in_mon,
											$kpi_data->in_tue,
											$kpi_data->in_wed,
											$kpi_data->in_thu,
											$kpi_data->in_fri,
											$kpi_data->in_sat
									));
					foreach($users as $user_id){
						$add_user = $this->Kpiusers_model->kpi_user_add($kpi_data->kpi_id, $user_id);
					}
					
					$array = array("result"=>"success", "message" => "Added successfully.", "kpi_id"=> $kpi_data->kpi_id, "data"=> $kpi_data);
					die(json_encode($array));
				}
			}
			
			$array = array("result"=>"error", "message"=>"Failed to add new kpi.");
			die(json_encode($array));
			
		}
	}
	
	
	
	
	
	public function ajax_edit_kpi()
	{
		$this->is_readwrite();	
		$error = false;
		
		if(isset($_POST['action']) && $_POST['action'] == "edit_kpi")
		{
			$kpi_id = (int)$_POST['kpi_id'];
			$name = $_POST['name'];
			$description = $_POST['description'];
			$frequency = $_POST['frequency'];
			$kpi_format_id = $_POST['kpi_format_id'];
			$best_direction = $_POST['best_direction'];
			$target = $_POST['target'];
			$agg_type = $_POST['agg_type'];
			$kpi_days = $_POST['kpi_days'];
			$users = @$_POST["users"];
			$users = $this->check_users_exist($users);
			
			if(trim($target) == ""){
				$target = null;
			}else if(!is_numeric($target)){
				$error = true;
			}
			
			$rags = $this->validate_rags();
			
			if($rags['result'] == "error"){
				$error = true;
			}
			
			if(empty($users) || $error == true){
				$array = array("result"=>"error", "message"=>"Failed to update new kpi.");
				die(json_encode($array));
			}
			
			
			
			/* 
			$icon = $_POST['icon'];
			$plan_id = $_POST['plan_id'];
			$current_trend = $_POST['current_trend'];
			$rollup_to_parent = $_POST['rollup_to_parent'];
			$parent_kpi_id = $_POST['parent_kpi_id']; */
			$update_data = array(
				'name' => $name, 
				"description" => $description,
				"kpi_format_id" => $kpi_format_id,
				"best_direction" =>$best_direction,
				"target" => $target,
				"rag_1" => $rags['rag_1'],
				"rag_2" => $rags['rag_2'],
				"rag_3" => $rags['rag_3'],
				"rag_4" => $rags['rag_4'],
				"agg_type" => $agg_type,
				"in_sun" => (int)$kpi_days[0],
				"in_mon" => (int)$kpi_days[1],
				"in_tue" => (int)$kpi_days[2],
				"in_wed" => (int)$kpi_days[3],
				"in_thu" => (int)$kpi_days[4],
				"in_fri" => (int)$kpi_days[5],
				"in_sat" => (int)$kpi_days[6]
			);
			
			
			$kpi = $this->Kpi_model->get_kpis($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);
			if($kpi->islocked == 0){
				$update_data['frequency'] = $frequency;	
			}
			
			$validate_graphs_kpi_users = $this->validate_graphs_kpi_users($kpi->kpi_id, $users);
			if($validate_graphs_kpi_users['result'] == "error"){
				$array = array("result"=>"error", "error_type" => "users", "message"=>$validate_graphs_kpi_users['msg']);
				die(json_encode($array));
			}
			
			/*update kpi*/
			$update = $this->Kpi_model->update($kpi_id, $update_data, TRUE);
			
			$format = $this->Kpi_model->get_kpi_formats($kpi_format_id);
			$update_data['format'] = $format->name;
			$update_data['frequency'] = ucfirst($frequency);
			$update_data['kpi_days'] = implode(',', array(
										$update_data['in_sun'], 
										$update_data['in_mon'], 
										$update_data['in_tue'], 
										$update_data['in_wed'], 
										$update_data['in_thu'], 
										$update_data['in_fri'], 
										$update_data['in_sat']
									));
			
			$update_users = $this->Kpiusers_model->update_kpi_users($this->organ_id, $this->user_id, $kpi_id, $users);
			
			if($update){
				$data = $update_data;
				$array = array("result"=>"success", "message" => "Updated successfully.", "kpi_id"=> $kpi_id, "data"=> $data);
				die(json_encode($array));
			}else{
				$array = array("result"=>"error", "error_type" => "update", "message"=>"Failed to update kpi.");
				die(json_encode($array));
			}
		}
	}
	
	
	
	
	
	
	private function validate_graphs_kpi_users($kpi_id, $users){
		$array = array("result" => "", "msg"=>"");
		$this->load->model('Graph_model');
		
		$graphs_kpi_check = $this->Graph_model->graph_kpi_users_checker($kpi_id, $users, $this->user_id);
		if($graphs_kpi_check == false || $graphs_kpi_check->graph_count == 0){
			$array['result'] = "success";
		}else{
			if($graphs_kpi_check->graph_kpi_count < $graphs_kpi_check->graph_count){
				$array['result'] = "error";
				$array['msg'] = 'Assigned users are being used in graphs. Atleast one assigned user remain used in a graph as "Whos data will be shown on this graph?".';
			}else{
				$array['result'] = "success";
			}
		}
		
		
		return $array;
	}
	
	
	
	
	
	private function validate_rags(){
		$array = array();
		if(
			(
				!isset($_POST["rag_1"])  && 
				!isset($_POST["rag_2"])  && 
				!isset($_POST["rag_3"])  && 
				!isset($_POST["rag_4"]) 
			) ||
			(
				(isset($_POST["rag_1"]) && !is_numeric($_POST["rag_1"])) &&
				(isset($_POST["rag_2"]) && !is_numeric($_POST["rag_2"])) &&
				(isset($_POST["rag_3"]) && !is_numeric($_POST["rag_3"])) &&
				(isset($_POST["rag_4"]) && !is_numeric($_POST["rag_4"]))
			) ||	
			(
				(isset($_POST["rag_1"]) && $_POST["rag_1"] == null ) &&
				(isset($_POST["rag_2"]) && $_POST["rag_2"] == null ) &&
				(isset($_POST["rag_3"]) && $_POST["rag_3"] == null ) &&
				(isset($_POST["rag_4"]) && $_POST["rag_4"] == null ) 
			)
		){
			$array = array(
				"result" => "success", 
				"rag_1" => null,
				"rag_2" => null,
				"rag_3" => null,
				"rag_4" => null
			);
		}else if(
			(isset($_POST["rag_1"]) && is_numeric($_POST["rag_1"])) &&
			(isset($_POST["rag_2"]) && is_numeric($_POST["rag_2"])) &&
			(isset($_POST["rag_3"]) && is_numeric($_POST["rag_3"])) &&
			(isset($_POST["rag_4"]) && is_numeric($_POST["rag_4"])) 
		){
			$array = array(
				"result" => "success", 
				"rag_1" => $_POST["rag_1"],
				"rag_2" => $_POST["rag_2"],
				"rag_3" => $_POST["rag_3"],
				"rag_4" => $_POST["rag_4"]
			);
		}else{
			$array = array("result"=>"error");
		}
		
		return $array;
	}
	
	
	
	
	
	public function ajax_delete_kpi()
	{
		$this->is_readwrite();
		
		if(isset($_POST['kpi_id']) && isset($_POST['action']) && $_POST['action'] == "delete_kpi")
		{
			$kpi_id = (int)$_POST['kpi_id'];	
			$delete = $this->Kpi_model->kpi_delete($this->user_id, $kpi_id, $this->organ_id, $this->plan_id);
			
			if($delete)
			{
				$array = array("result"=>"success", "message" => "Deleted successfully.", "kpi_id"=> $kpi_id);
				die(json_encode($array));
			}
			else
			{
				$array = array("result"=>"error", "message"=>"Failed to delete kpi.");
				die(json_encode($array));
			}
		}
	}
	
	
	
	
	
	public function ajax_get_users(){
		$this->is_readwrite();	
		$data = array();
		
		if(isset($_POST['get_users']) && isset($_POST['get_kpi_users']))
		{
			$users = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);
			if(!empty($users)){
				foreach($users as $user){
					$user_data = array("user_id"=>$user->user_id, "name" => $user->first_name ." ". $user->last_name);
					if($_POST['get_kpi_users'] == true && isset($_POST['kpi_id'])){
						 $user_exist = $this->Kpiusers_model->kpi_user_exists($_POST['kpi_id'], $user->user_id);
						 $user_data['assigned'] = $user_exist;
					}
					
					$data[] = $user_data;
					
				}
				$new_data  = array_values($data);
				$user_count = count($users);
				$array = array("response"=>"success", "count"=>$user_count, "message"=>"", "users"=>$new_data, "user_id"=>$this->user_id);
			}else{
				$array = array("response"=>"success", "count"=>0, "message"=>"No other users available");
				
			}
		}
		die(json_encode($array));
	}
	
	
	
	
	
	private function is_readwrite(){
		if($this->kpi_permission[0]['readwrite'] != 1 || ($this->kpi_permission[0]['readwrite'] == 1 && $this->kpi_permission[0]['readonly'] != 0))
		{
			$array = array("response"=>"error", "message"=>"No enough permission.");
			die(json_encode($array));
		}	
	}
	
	
	
	
	
	private function check_users_exist($users){
		if(empty($users)){
			return false;
		}
		$tmp_users = array();
		foreach($users as $user_id){
			$existence = $this->Organisationusers_model->organisation_member_exists($user_id, $this->organ_id);
			if($existence){
				$user_id = (int)$user_id;
				$tmp_users[] = $user_id;
			}
		}
		return (!empty($tmp_users)) ? $tmp_users : false;
	}
}