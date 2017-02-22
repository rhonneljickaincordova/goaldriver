<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kpi_dashboard extends CI_Controller {
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
		$this->load->model('Users_model');
		$this->load->model('Kpi_model');
		$this->load->model('Graph_model');
		$this->load->model('Kpiusers_model');
		$this->load->model('Organisation_model');
		
		
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
	

	public function ajax_highchart(){
		if(isset($_POST['action']) && $_POST['action'] == "get_highcharts")
		{
			$this->load->library('Highcharts');
			$this->load->library('Kpi_data');
			$this->load->library('Highcharts_gauge');
			$this->load->library('Kpi_data_gauge');
			
			$array = array(
				"result" => "success",
				"graph_count" => 0,
				"gauge_count" => 0
			);
			
			if( $this->kpi_permission_name == "readwrite"){
				$graphs = $this->Graph_model->get_graphs($this->organ_id, $this->user_id);
				$gauges = $this->Graph_model->get_kpiDash_gauges_v3("kpi_dash", $this->organ_id, $this->user_id);
			}else{
				$graphs = $this->Graph_model->get_shared_graphs($this->organ_id, $this->user_id);
				$gauges = $this->Graph_model->get_kpiDash_gauges_v3("kpi_dash_shared", $this->organ_id, $this->user_id);
			}
			
			if($graphs){
				$array['highcharts_graphs'] = $this->highcharts->ajax_generate_highchart($graphs);
				$array['graph_count'] = count($graphs);
			}
			if($gauges){
				$array['highcharts_gauges'] = $this->highcharts_gauge->generate_gauges($gauges);
				$array['gauge_count'] = count($gauges);
			}
			
			die(json_encode($array));
		}
		
	}
	
	
	/* Generate Highcharts Graph with filtered data */
	function ajax_filter_graph(){
		$this->load->library('Highcharts');
		$this->load->library('Kpi_data');
		
		if(isset($_POST['action']) && $_POST['action'] == "filter_graph")
		{
			if(isset($_POST['highchart_id'])){
				$highchart_id = $_POST['highchart_id'];
				$from_date = null;
				$to_date = null;
				$users = null;
				$show_average = (isset($_POST['show_average']) && $_POST['show_average'] == "true") ? true : false;
				$show_break_down = (isset($_POST['show_break_down']) && $_POST['show_break_down'] == "true") ? true : false;
				
				if(isset($_POST['from_date']) && isset($_POST['to_date'])){
					$valid_from = $this->is_valid_date($_POST['from_date']);
					$valid_to = $this->is_valid_date($_POST['to_date']);
					if($valid_from == true && $valid_to == true){
						$from_date = $_POST['from_date'];
						$to_date = $_POST['to_date'];	
					}	
				}
				
				if(isset($_POST['users']) && !empty($_POST['users']) ){
					$users = $_POST['users'];
				}
				
				
				$array = $this->highcharts->ajax_filter_graph($from_date, $to_date, $highchart_id, $users, $show_average, $show_break_down);	
				
			}else{
				$array = array("result"=>"error");
			}
			die(json_encode($array));	
			
		}
	}
	
	/* Generate Highcharts Gauge with filtered data */
	public function ajax_filter_gauge_v3(){
		$this->load->library('Highcharts_gauge');
		
		if(isset($_POST['action']) && $_POST['action'] == "get_gauge_filter_data")
		{
			if(isset($_POST['gauge_id']) && isset($_POST['prev_date']) && isset($_POST['next_date']) && isset($_POST['direction'])){
				$gauge_id = (int)$_POST['gauge_id'];
				$prev_date = $_POST['prev_date'];
				$next_date = $_POST['next_date'];	
				$direction = $_POST['direction'];	
				
				$valid_from = $this->is_valid_date($prev_date);
				$valid_to = $this->is_valid_date($next_date);
				if($valid_from == false || $valid_to == false){
					die(json_encode(array("result"=>"error")));	
				}	
				
				
				if( $this->kpi_permission_name == "readwrite"){
					$graph = $this->Graph_model->get_kpiDash_gauge_filter_v3("kpi_dash", $this->organ_id, $this->user_id, $_POST['gauge_id']);
				}else{
					$graph = $this->Graph_model->get_kpiDash_gauge_filter_v3("kpi_dash_shared", $this->organ_id, $this->user_id, $_POST['gauge_id']);
				}
				
				$new_dates = $this->kpi_calendar->get_new_gauge_dates($graph->frequency, $graph->reset_frequency_type, $direction, $prev_date, $next_date);
				/* die(json_encode($new_dates)); */
				if($graph){
					if($graph->frequency == "daily"){
						$final_graph = $this->Graph_model->get_kpiDash_gaugeData_filter_v3($graph, $graph->reset_frequency_type, $new_dates['new_prev_date'], $new_dates['new_next_date']);	
					}else{
						$final_graph = $this->Graph_model->get_kpiDash_gaugeData_filter_v3($graph, $graph->reset_frequency_type, $new_dates['new_prev_date'], $new_dates['new_next_date']);
					}
				}
				
				$array['result'] = "success";	
				$array['gauge'] = $this->highcharts_gauge->ajax_filter_gauge($graph);	
				die(json_encode($array));	
			}
			
		}
		die(json_encode(array("result"=>"error")));	
	}
	
	
	public function ajax_graph_users()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_graph_users")
		{
			$graph_id = (int)$_POST['graph_id'];
		
			$graph = $this->Graph_model->get_graphs($this->organ_id, $this->user_id, $graph_id);
			
			if($graph)
			{
				$graph_users =  $this->Graph_model->get_graph_users($graph_id);	
				$tmp_final_users = array();
				if($graph_users)
				{
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
	}
	
	/******************************************************************************************************************************************
***
***  KPI TAB ON DASHBOARD	
***
******************************************************************************************************************************************/		
	/* KPI STARTS HERE */
	public function ajax_highchart_dash(){
		if($_SERVER){
			$this->load->library('Highcharts');
			$this->load->library('Kpi_data');
			$this->load->library('Highcharts_gauge');
			$this->load->library('Kpi_data_gauge');
			
			$array = array(
				"result" => "success",
				"graph_count" => 0,
				"gauge_count" => 0
			);
			
			$graphs = $this->Graph_model->get_mainDash_graphs_load($this->organ_id, $this->user_id);
			$gauges = $this->Graph_model->get_kpiDash_gauges_v3("main_dash", $this->organ_id, $this->user_id);
			
			if($graphs){
				$array['highcharts_graphs'] = $this->highcharts->ajax_generate_highchart($graphs);
				$array['graph_count'] = count($graphs);
			}
			if($gauges){
				$array['highcharts_gauges'] = $this->highcharts_gauge->generate_gauges($gauges);
				$array['gauge_count'] = count($gauges);
			}
			
			die(json_encode($array));
		}
	}
	
	
	/* Generate Highcharts Gauge with filtered data */
	public function ajax_filter_gauge_v3_dash(){
		$this->load->library('Highcharts_gauge');
		$this->load->library('Kpi_calendar');
		
		if(isset($_POST['action']) && $_POST['action'] == "get_gauge_filter_data")
		{
			if(isset($_POST['gauge_id']) && isset($_POST['prev_date']) && isset($_POST['next_date']) && isset($_POST['direction'])){
				$gauge_id = (int)$_POST['gauge_id'];
				$prev_date = $_POST['prev_date'];
				$next_date = $_POST['next_date'];	
				$direction = $_POST['direction'];	
				
				$valid_from = $this->is_valid_date($prev_date);
				$valid_to = $this->is_valid_date($next_date);
				if($valid_from == false || $valid_to == false){
					die(json_encode(array("result"=>"error")));	
				}	
				
				$graph = $this->Graph_model->get_kpiDash_gauge_filter_v3("main_dash", $this->organ_id, $this->user_id, $_POST['gauge_id']);
				
				$new_dates = $this->kpi_calendar->get_new_gauge_dates($graph->frequency, $graph->reset_frequency_type, $direction, $prev_date, $next_date);
				/* die(json_encode($new_dates)); */
				if($graph){
					if($graph->frequency == "daily"){
						$final_graph = $this->Graph_model->get_kpiDash_gaugeData_filter_v3($graph, $graph->reset_frequency_type, $new_dates['new_prev_date'], $new_dates['new_next_date']);	
					}else{
						$final_graph = $this->Graph_model->get_kpiDash_gaugeData_filter_v3($graph, $graph->reset_frequency_type, $new_dates['new_prev_date'], $new_dates['new_next_date']);
					}
					
				}
				
				$array['result'] = "success";	
				$array['gauge'] = $this->highcharts_gauge->ajax_filter_gauge($graph);	
				die(json_encode($array));	
			}
			
		}
		die(json_encode(array("result"=>"error")));	
	}
	
	
	
	/* Generate Highcharts Graph with filtered data */
	function ajax_filter_graph_dash(){
		$this->load->library('Highcharts');
		$this->load->library('Kpi_data');
		
		if($_POST)
		{
			if(isset($_POST['highchart_id'])){
				$highchart_id = $_POST['highchart_id'];
				$from_date = null;
				$to_date = null;
				$users = null;
				$show_average = (isset($_POST['show_average']) && $_POST['show_average'] == "true") ? true : false;
				$show_break_down = (isset($_POST['show_break_down']) && $_POST['show_break_down'] == "true") ? true : false;
				
				if(isset($_POST['from_date']) && isset($_POST['to_date'])){
					$valid_from = $this->is_valid_date($_POST['from_date']);
					$valid_to = $this->is_valid_date($_POST['to_date']);
					if($valid_from == true && $valid_to == true){
						$from_date = $_POST['from_date'];
						$to_date = $_POST['to_date'];	
					}	
				}
				
				if(isset($_POST['users']) && !empty($_POST['users']) ){
					$users = $_POST['users'];
				}
				
				
				$array = $this->highcharts->ajax_filter_graph($from_date, $to_date, $highchart_id, $users, $show_average, $show_break_down);	
				
			}else{
				$array = array("result"=>"error");
			}
			die(json_encode($array));	
			
		}
	}
	
	public function ajax_graph_users_dash()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_graph_users")
		{
			$graph_id = (int)$_POST['graph_id'];
		
			$graph = $this->Graph_model->get_mainDash_graphs_load($this->organ_id, $this->user_id, $graph_id);
			
			if($graph)
			{
				$graph_users =  $this->Graph_model->get_graph_users($graph_id);	
				$tmp_final_users = array();
				if($graph_users)
				{
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
	}
	
	
	
	private function is_valid_date($date){
		preg_match('/\d{4}-\d{2}-\d{2}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}

}