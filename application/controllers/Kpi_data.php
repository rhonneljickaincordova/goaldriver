<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kpi_data extends CI_Controller {
	private $user_id = 0;
	private $organ_id = 0;
	private $plan_id = 0;
	private $is_organisation_owner = false;
	private $kpi_permission = array();
	private $frequencies = array("daily", "weekly", 'monthly', 'quarterly', 'yearly');
	private $integers = array(1, 3, 9, 10, 11, 12, 13, 14, 15, 16);
	private $decimals = array(2, 4, 5, 6, 7, 8);
	
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
		
		$this->load->model('Kpi_model');
		$this->load->model('Kpidata_model');
		$this->load->model('Organisation_model');
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
	

	
	/************************************************************************************************
	*
	GET KPI DATA 
	*
	************************************************************************************************/
	
	function ajax_get_kpi_data()
	{
		$array = array("result"=>"error", "message"=>"No data found.");
		$kpis = array();
		if(isset($_POST['action']) && $_POST['action'] == "get_kpi_data")
		{
			$process_type = $_POST['process_type'];
			$frequency = $_POST['frequency'];
			$prev_date = $_POST['prev_date'];
			$next_date = $_POST['next_date'];
			
			$to_unset_kd = array();
			$actual_dates = array();
			$data = array();
			
			if(!in_array($frequency, $this->frequencies))
			{
				die(json_encode($array));
			}	
			
			/* GET NEW DATES */
			if($process_type != "current")
			{
				if($this->is_valid_date($prev_date) == false || $this->is_valid_date($next_date) == false){
					die(json_encode($array));
				}
			
				$new_dates = $this->kpi_calendar->get_new_enterdata_dates($frequency, $process_type, $prev_date, $next_date, 1);	
				$prev_date = $new_dates['new_prev_date'];
				$next_date = $new_dates['new_next_date'];
				$list_dates = $this->Kpidata_model->enterData_dates($frequency, 0, 0, '0000-00-00', 1, 5, $prev_date, $next_date);
			}else{
				if($this->is_valid_date($next_date) == false){
					die(json_encode($array));
				}
				$list_dates = $this->Kpidata_model->enterData_dates($frequency, 4, 0, $next_date);
			}
			
			
			if($list_dates == false){
				die(json_encode($array));
			}
			
			foreach($list_dates['dates'] as $ld){
				$actual_dates[] = $ld->selected_date;
			}
			
			
			/**
			***GET KPIS : getting kpi data differs in daily and other remaining frequencies
			**/
			if($frequency == "daily")
			{
				/**
				***GETTING KPIS AND KPI DATA WHERE FREQUENCIES is daily
				**/
				$array = $this->get_kpiData_daily($prev_date, $next_date);
			}else{
				
				/**
				***GETTING KPIS AND KPI DATA WHERE FREQUENCIES IN (weekly, monthly, quarterly, yearly) 
				**/
				$array = $this->get_kpiData_otherFrequency($actual_dates, $frequency, $prev_date, $next_date);
			}
			
			$array['dates'] = $list_dates;
			$array['actual_dates'] = $actual_dates;
			$array['list_dates'] = $list_dates;
			$array['result'] = "success";
		}
		
		die(json_encode($array));
		
	}
	
	private function get_kpiData_daily($prev_date, $next_date)
	{
		$data = array();
		$kpis = $this->Kpi_model->get_assigned_kpis($this->organ_id, $this->user_id, 'daily');
		if($kpis){
			/* GET KPI DATA */
			foreach($kpis as $kpi)
			{
				$default_target_num = ($kpi->target == null) ? 0 : $kpi->target;
				$default_target_str = ($kpi->target == null) ? "" : $this->Kpidata_model->prepare_format($kpi->kpi_format_id, $kpi->target);
				
				$kpi_data = $this->Kpidata_model->get_kpi_data_daily($this->organ_id, $this->user_id, $default_target_num, $prev_date, $next_date, $kpi->in_sun, $kpi->in_mon, $kpi->in_tue, $kpi->in_wed, $kpi->in_thu, $kpi->in_fri, $kpi->in_sat, 5, $kpi->kpi_id); 
				
				$data[$kpi->kpi_id]['kpi_id'] = $kpi->kpi_id;
				$data[$kpi->kpi_id]['name'] = $kpi->name;
				$data[$kpi->kpi_id]['default_target'] = $default_target_str;
				$data[$kpi->kpi_id]['show_target'] = ($kpi->target != null) ? true : false;
				
				foreach($kpi_data as $kd)
				{
					$actual = ($kd->actual == null) ? "" : $this->Kpidata_model->prepare_format($kpi->kpi_format_id, $kd->actual);
					if($kpi->target != null){
						if($kd->target != null){
							$target = $this->Kpidata_model->prepare_format($kpi->kpi_format_id, $kd->target);	
						}else{
							$target = ($kd->target_null == 1) ? "" : $default_target_str;
						}
					}					
					
					$kpi_data_on_date = array(
						"kpi_data_id" => $kd->kpi_data_id,
						"actual" => $actual,
						"target" => $target,
						"display_inputData" => $kd->display_inputData,
						"target_null" => $kd->target_null
					);
										
					$data[$kpi->kpi_id]['dates'][$kd->selected_date] = $kpi_data_on_date;
				}		
			}
		}
		
		$array = array(
			"entries" => $data,
			"count" => count($kpis)
		);
		
		return $array;
	}
	
	
	private function get_kpiData_otherFrequency($actual_dates, $frequency, $prev_date, $next_date){
		$kpis_left_join_data = $this->Kpidata_model->get_kpi_data($frequency, $this->organ_id, $this->user_id, $prev_date, $next_date);
		$a = $kpis_left_join_data;
		$data = array();
		if($kpis_left_join_data)
		{
			$kpis = array();
			foreach($kpis_left_join_data as $kd){
				$kpis[$kd->kpi_id]["data"] = $kd;
				$kpis[$kd->kpi_id]["kpi_id"] = $kd->kpi_id;
				if($kd->date != null){
					$kpis[$kd->kpi_id]["no_kpi_data"] = false;
					$kpis[$kd->kpi_id]["kpi_data"][$kd->date] = $kd;
				}else{
					$kpis[$kd->kpi_id]["no_kpi_data"] = true;
					$kpis[$kd->kpi_id]["kpi_data"] = array();	
				}
			}
			foreach($kpis as $kpi)
			{
				$default_target = ($kpi['data']->default_target == null) ? "" : $this->Kpidata_model->prepare_format($kpi['data']->kpi_format_id, $kpi['data']->default_target);
					
				$data[$kpi['kpi_id']]['kpi_id'] = $kpi['kpi_id'];
				$data[$kpi['kpi_id']]['name'] = $kpi['data']->name;
				$data[$kpi['kpi_id']]['default_target'] = $default_target;
				$data[$kpi['kpi_id']]['show_target'] = ($kpi['data']->default_target != null) ? true : false;
					
				
				if($kpi['no_kpi_data'] == false)
				{
					foreach($actual_dates as $date)
					{
						$kpi_data_id = null;
						$actual = "";
						$target = $default_target;
						$target_null = 0;
						
						if(isset($kpi['kpi_data'][$date]))
						{
							$kd = $kpi['kpi_data'][$date];
							$kpi_data_id = $kd->kpi_data_id;
							$actual = ($kd->actual == null) ? "" : $this->Kpidata_model->prepare_format($kpi['data']->kpi_format_id, $kd->actual);
							
							if($kpi['data']->default_target != null)
							{
								if($kd->target != null){
									$target = $this->Kpidata_model->prepare_format($kpi['data']->kpi_format_id, $kd->target);	
								}else{
									$target = ($kd->target_null == 1) ? "" : $default_target;
								}
								$target_null = $kd->target_null;
							}
						}
											
						
						$kpi_data_on_date = array(
							"kpi_data_id" => $kpi_data_id,
							"actual" => $actual,
							"target" => $target,
							"target_null" => $target_null,
						);	
						$data[$kpi['kpi_id']]['dates'][$date] = $kpi_data_on_date;
					}
				}else{
					foreach($actual_dates as $date){
						$kpi_data_on_date = array(
							"kpi_data_id" => null,
							"actual" => "",
							"target" => $default_target,
							"target_null" => 0
						);	
						$data[$kpi['kpi_id']]['dates'][$date] = $kpi_data_on_date;
					}
				}		
			}
		}
		
		$array = array(
			"entries" => $data,
			"kpis_left_join_data" => $a,
			"count" => count($kpis_left_join_data)
		);
		
		return $array;
	}
	
	/************************************************************************************************
	*
	SAVE KPI DATA 
	*
	************************************************************************************************/
	
	public function ajax_save_kpi_data(){
		$array = array("result"=>"error", "message"=>"Failed to process data.");
		
		if(isset($_POST['action']) && $_POST['action'] == "save_kpi_data")
		{
			$kpis = array();
			$data = @$_POST['data'];
			$updated_data_count = 0;
			$error_update_count = 0;
			$islocked_kpis = array();
			$frequency = $_POST['frequency'];
			
			if(!in_array($frequency, $this->frequencies) || empty($data))
			{
				die(json_encode($array));
			}
			
			
			foreach($data as $column_id=>$desc)
			{
				$column = explode('_', $column_id);
				$kpi_id = (int)$column[0];
				$date = $column[1];
				
				$actual =  null; 
				$islocked = false;
				$array_data = array( 'kpi_id' => $kpi_id, "date" => $date, "actual_null_value" => false, "target_null_value" => false );
				
				if(!isset($kpis[$kpi_id])){
					$kpis[$kpi_id] = $this->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);		
				}
				
				/* ERROR TRAP HERE */
				if(
					$kpis[$kpi_id] == false 
					|| 
						(
							(!isset($desc['actual']) || !isset($desc['actual']['new_val'])) && 
							(!isset($desc['target']) || !isset($desc['target']['new_val']))
						)
					||
					(isset($desc['actual']['new_val']) && trim($desc['actual']['new_val']) != ""  && !is_numeric($desc['actual']['new_val'])) 
					|| 
					(isset($desc['target']['new_val']) && trim($desc['target']['new_val']) != "" && !is_numeric($desc['target']['new_val']))
					||
					$this->is_valid_date($date) == false
				){
					continue;
				}
				
				$default_target = (is_null($kpis[$kpi_id]->target)) ? null : $kpis[$kpi_id]->target;
				$target =  $default_target;
				
				if(isset($desc['actual']['new_val'])){
					if(trim($desc['actual']['new_val']) != ""){
						$actual = $desc['actual']['new_val'];
					}else{
						$array_data['actual_null_value'] = true;
					}
				}
				
				$array_data['target_null'] = 0;
				if(isset($desc['target']['new_val']) && $default_target != null){
					if(trim($desc['target']['new_val']) != ""){
						$target = $desc['target']['new_val'];
						$array_data['target_null'] = 0;
					}else{
						$target = null;
						$array_data['target_null_value'] = true;
						$array_data['target_null'] = 1;
					}
				}	
				
				$kpi_data = $this->Kpidata_model->get_specific_kpi_data($this->organ_id, $this->user_id, $kpi_id, $frequency, $date);
				
				if($kpi_data == false){
					$array_data['target']  = $this->Kpidata_model->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
					$array_data['actual'] = $this->Kpidata_model->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
					$process_data = $this->Kpidata_model->insert_kpi_data($array_data);
				}else{
					$array_data['actual'] =  $this->Kpidata_model->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
					$array_data['target'] =  $this->Kpidata_model->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
					$array_data['kpi_data_id'] = $kpi_data->kpi_data_id;
					
					$process_data =  $this->Kpidata_model->update_kpi_data($array_data);
				}
				
				if($kpis[$kpi_id]->islocked == 1){
					$islocked = true;	
				}
				
				if($process_data){
					if($kpis[$kpi_id]->islocked == 0){
						$update_data = array('islocked' => 1);
						$islocked = $this->Kpi_model->update($kpi_id, $update_data, TRUE);
					}
					
					$updated_data_count++;
				}else{
					$error_update_count++;
				}
				
				$islocked_kpis[$kpi_id] = array("kpi_id"=>$kpi_id, "islocked"=> $islocked);	
			}
			
			if($updated_data_count > 0)
			{
				$array = array(
					"result"=> "success", 
					"message" => "Saved successfully.", 
					"islocked_kpis" => $islocked_kpis,
					"islocked_count" => count($islocked_kpis)
				);
			}
		}
		
		die(json_encode($array));
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

