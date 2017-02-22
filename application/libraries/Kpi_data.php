<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Kpi_data{
	private $ci = 0;
	private $user_id = 0;
	private $organ_id = 0;
	private $plan_id = 0;
	
	public function __construct(){
		$this->ci = &get_instance();	
		$this->user_id = $this->ci->session->userdata('user_id');
		$this->organ_id = $this->ci->session->userdata('organ_id');
		$this->plan_id = $this->ci->session->userdata('plan_id');
		
		$this->ci->load->model('Kpi_model');
		
	}
	
	
	public function get_kpi_data($type, $dates, $kpi_id = 0, $user_id = 0){
		$kpi_id = (int)$kpi_id;
		$data = array();
		$user_id = ($user_id == 0) ? $this->user_id : $user_id;
		switch($type){
			case "daily" : 
				$data = $this->ci->Kpi_model->get_kpi_data('daily', $dates, $user_id, $this->organ_id, $this->plan_id, $kpi_id);	
				break;
			case "weekly" : 
				$data = $this->ci->Kpi_model->get_kpi_data('weekly', $dates, $user_id, $this->organ_id, $this->plan_id, $kpi_id);
				break;
			case "monthly" : 
				$data = $this->ci->Kpi_model->get_kpi_data('monthly', $dates, $user_id, $this->organ_id, $this->plan_id, $kpi_id);
				break;
			case "quarterly" : 
				$data = $this->ci->Kpi_model->get_kpi_data('quarterly', $dates, $user_id, $this->organ_id, $this->plan_id, $kpi_id);
				break;
			case "yearly" : 
				$data = $this->ci->Kpi_model->get_kpi_data('yearly', $dates, $user_id, $this->organ_id, $this->plan_id, $kpi_id);	
				break;
		}
		
		return $data;
	}
	
	
	public function prepare_dates($dates){
		$new_dates = array();
		
		foreach($dates as $date){
			$new_dates[] = "'".$date."'";
		}
		
		$final_dates = implode(",", $new_dates);
		return $final_dates; 
	}
	
	
	public function prepare_data($data, $dates){
		$rows = $data;
		$kpi_data =  array();
		$kpi_ids = array();
		
		if($data == false){
			return array();
		}
		
		foreach($rows as $row){
			
			$row->actual = ($row->actual == null) ? "" : $this->prepare_format($row->kpi_format_id, $row->actual);
			$row->default_target = ($row->default_target == null) ? "" : $this->prepare_format($row->kpi_format_id, $row->default_target);
				
			$kpi_ids[] = $row->kpi_id;
			$kpi_data[$row->kpi_id]['data'][$row->kpi_date] = $row;
			$kpi_data[$row->kpi_id]['name'] = $row->name;
			$kpi_data[$row->kpi_id]['default_target'] = $row->default_target;
			$kpi_data[$row->kpi_id]['user_id'] = $row->user_id;
			$kpi_data[$row->kpi_id]['first_name'] = $row->first_name;
			$kpi_data[$row->kpi_id]['last_name'] = $row->last_name;
		}
		
		$final_data = $this->final_data_manipulation($kpi_data, $dates);
		return $final_data;
	}
	
	
	public function final_data_manipulation($kpi_data, $dates){
		$kpi_data = (array)$kpi_data;
		$final_data = array();
		
		foreach($kpi_data as $kpi_id=>$desc){
			$name = $desc['name'];
			$data = (array)$desc['data'];
			$default_target = $desc['default_target'];
			$actuals = array();
			$targets = array();
			$show_target = false;
			$actual_dates = array();
			$user_id = $desc['user_id'];
			$first_name = $desc['first_name'];
			$last_name = $desc['last_name'];
			
			foreach($dates as $date){
				if(isset($data[$date])){
					$entry = (array)$data[$date];
					$actuals[] = $entry['actual'];
					$kpi_format_id = $entry['kpi_format_id'];
					$actual_dates[] = $date;
					
					if(trim($entry['data_target']) != "" && $entry['data_target'] != null){
						$targets[] = $this->prepare_format($kpi_format_id, $entry['data_target']); 
					}else{
						$targets[] = null;
					}
					if(trim($default_target) != ""){
						$show_target = true;
					}	
				}else{
					$actuals[] = null;
					$actual_dates[] = $date;
					
					if(trim($default_target) != ""){
						$targets[] = $default_target;
						$show_target = true;
					}else{
						$targets[] = null;
					}
				}
			}
			
			$final_data['entries'][] = array(
								"kpi_id" => $kpi_id, 
								"name" => $name,
								"actuals" => $actuals,
								"targets" => $targets,
								"show_target" => $show_target,
								"default_target" => $default_target,
								"user_id" => $user_id,
								"first_name" => $first_name,
								 "last_name" => $last_name
  							);
		}
		
		$final_data['actual_dates'] = $actual_dates;
		return $final_data;
	}
	
	
	public function get_kpi_data_daily($kpi_dates, $process_type, $kpi_id = 0, $user_id = 0){
		$dates = $this->prepare_dates($kpi_dates['days_list']);
		$data = $this->get_kpi_data('daily', $dates, $kpi_id, $user_id);
		$array = array(
			"result"=>"success",
			"entries" => array(),
			"actual_dates" => array()
		);
		
		if(!empty($data)){
			$new_data = $this->prepare_data($data, $kpi_dates['days_list']);	
			
			$array['entries'] = $new_data['entries'];
			$array['actual_dates'] = $new_data['actual_dates'];
		}
		
		if($process_type == "prev"){
			$array['kpi_dates']['start_day'] = $kpi_dates['start_day'];
		}else if($process_type == "next"){
			$array['kpi_dates']['last_day'] = $kpi_dates['last_day'];
		}else if($process_type == "current"){
			$array['kpi_dates']['days'] = $kpi_dates['days'];
		}	
		
		return $array;	
	}
	
	
	public function get_kpi_data_weekly($kpi_dates, $process_type, $kpi_id = 0, $user_id = 0){
		$data = $this->get_kpi_data('weekly', $kpi_dates['weeks_list'], $kpi_id, $user_id);
		$array = array(
			"result"=>"success",
			"entries" => array(),
			"actual_dates" => array(),
			"data" =>$data
		);
		
		if(!empty($data)){
			$new_data = $this->prepare_data($data, $kpi_dates['weeks_list']);	
			
			$array['entries'] = $new_data['entries'];
			$array['actual_dates'] = $new_data['actual_dates'];
		}
		
		switch($process_type){
			case "prev" :
				$array['kpi_dates']['start_week'] = $kpi_dates['start_week'];
				break;
			case "next" :
				$array['kpi_dates']['last_week'] = $kpi_dates['last_week'];
				break;
			case "current" :
				$array['kpi_dates']['start_week'] = $kpi_dates['start_week'];
				$array['kpi_dates']['weeks'] = $kpi_dates['weeks'];
				$array['kpi_dates']['last_week'] = $kpi_dates['last_week'];
				break;
		}
		
		$array['weeks_list'] = $kpi_dates['weeks_list'];
		return $array;
	}
	
	
	public function get_kpi_data_monthly($kpi_dates, $process_type, $kpi_id = 0, $user_id = 0){
		$dates = $this->prepare_dates($kpi_dates['months_list']);
		$data = $this->get_kpi_data('monthly', $dates, $kpi_id, $user_id);
		$array = array(
			"result"=>"success",
			"entries" => array(),
			"actual_dates" => array()
		);
		
		if(!empty($data)){
			$new_data = $this->prepare_data($data, $kpi_dates['months_list']);	
			
			$array['entries'] = $new_data['entries'];
			$array['actual_dates'] = $new_data['actual_dates'];
		}
		
		if($process_type == "prev"){
			$array['kpi_dates']['start_month'] = $kpi_dates['start_month'];
		}else if($process_type == "next"){
			$array['kpi_dates']['last_month'] = $kpi_dates['last_month'];
		}else if($process_type == "current"){
			$array['kpi_dates']['start_month'] = $kpi_dates['start_month'];
			$array['kpi_dates']['months'] = $kpi_dates['months'];
			$array['kpi_dates']['last_month'] = $kpi_dates['last_month'];
		}
		
		$array['months_list'] = $kpi_dates['months_list'];
		return $array;
	}
	
	
	public function get_kpi_data_quarterly($kpi_dates, $process_type, $kpi_id = 0, $user_id = 0){
		$data = $this->get_kpi_data('quarterly', $kpi_dates['quarters_list'], $kpi_id, $user_id);
		$array = array(
			"result"=>"success",
			"entries" => array(),
			"actual_dates" => array()
		);
		
		if(!empty($data)){
			$new_data = $this->prepare_data($data, $kpi_dates['quarters_list']);	
			
			$array['entries'] = $new_data['entries'];
			$array['actual_dates'] = $new_data['actual_dates'];
		}
		
		if($process_type == "prev"){
			$array['kpi_dates']['start_quarter'] = $kpi_dates['start_quarter'];
		}else if($process_type == "next"){
			$array['kpi_dates']['last_quarter'] = $kpi_dates['last_quarter'];
		}else if($process_type == "current"){
			$array['kpi_dates']['start_quarter'] = $kpi_dates['start_quarter'];
			$array['kpi_dates']['quarters'] = $kpi_dates['quarters'];
			$array['kpi_dates']['last_quarter'] = $kpi_dates['last_quarter'];
		}
		
		$array['quarters_list'] = $kpi_dates['quarters_list'];
		return $array;
	}
	
	
	public function get_kpi_data_yearly($kpi_dates, $process_type, $kpi_id = 0, $user_id = 0){
		$dates = $this->prepare_dates($kpi_dates['years_list']);
		$data = $this->get_kpi_data('yearly', $dates, $kpi_id, $user_id);
		$array = array(
			"result"=>"success",
			"entries" => array(),
			"actual_dates" => array()
		);
		
		if(!empty($data)){
			$new_data = $this->prepare_data($data, $kpi_dates['years_list']);	
			
			$array['entries'] = $new_data['entries'];
			$array['actual_dates'] = $new_data['actual_dates'];
		}
		
		if($process_type == "prev"){
			$array['kpi_dates']['start_year'] = $kpi_dates['start_year'];
		}else if($process_type == "next"){
			$array['kpi_dates']['last_year'] = $kpi_dates['last_year'];
		}else if($process_type == "current"){
			$array['kpi_dates']['start_year'] = $kpi_dates['start_year'];
			$array['kpi_dates']['years'] = $kpi_dates['years'];
			$array['kpi_dates']['last_year'] = $kpi_dates['last_year'];
		}
		
		$array['years_list'] = $kpi_dates['years_list'];
		return $array;
	}
	
	
	public function prepare_format($kpi_format_id, $value, $return = "string", $type = 'in'){
		$kpi_format_id = (int)$kpi_format_id;
		$integers = array(1, 3, 9, 10, 11, 12, 13, 14, 15, 16);
		$decimals = array(2, 4, 5, 6, 7, 8);
		
		$new_value = ($return == "string") ? "" : NULL;
		if($type == 'in'){
			if(is_null($value) == false){
				if(in_array($kpi_format_id, $integers )){
					$new_value = (int)$value;	
				}else if(in_array($kpi_format_id, $decimals )){
					$new_value = number_format($value, 2, '.', '');
				}
			}			
		}else{
			if(is_null($value) == false){
				if(in_array($kpi_format_id, $integers )){
					$new_value = number_format($value, 0, '', ',');
				}else if(in_array($kpi_format_id, $decimals )){
					$new_value = number_format($value, 2, '.', ',');
				}	
			}
		}
			
		return $new_value;
	}
	
	public function save_kpi_data_day($data, $frequency, $date_type ){
		$kpis = array();
		$response = array("response_update" => false);
		$islocked_kpis = array();
		if(empty($data)){
			return $response;
		}	
		
		foreach($data as $column_id=>$desc){
			$column = explode('_', $column_id);
			$kpi_id = $column[0];
			$date = $column[1];
			
			$actual =  null; 
			$islocked = false;
			$array_data = array( 'kpi_id' => $kpi_id, "date" => $date, "actual_null_value" => false, "target_null_value" => false );
			
			
			if(!isset($kpis[$kpi_id])){
				$kpis[$kpi_id] = $this->ci->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);		
			}
			
			if($kpis[$kpi_id] == false || 
				(
					(!isset($desc['actual']) || !isset($desc['actual']['new_val'])) && 
					(!isset($desc['target']) || !isset($desc['target']['new_val']))
				)
			){
				continue;
			}
			
			$default_target = (is_null($kpis[$kpi_id]->target)) ? null : $kpis[$kpi_id]->target;
			$target =  $default_target;
			
			
			if(
				(isset($desc['actual']['new_val']) && trim($desc['actual']['new_val']) != ""  && !is_numeric($desc['actual']['new_val'])) || 
				(isset($desc['target']['new_val']) && trim($desc['target']['new_val']) != "" && !is_numeric($desc['target']['new_val'])) 
			){
					$array = array("result"=>"error", "message"=>"Failed to process data. Invalid value.");
					die(json_encode($array));
			}
			
			if(isset($desc['actual']['new_val'])){
				if(trim($desc['actual']['new_val']) != ""){
					$actual = $desc['actual']['new_val'];
				}else{
					$array_data['actual_null_value'] = true;
				}
			}
			
			if(isset($desc['target']['new_val']) && $default_target != null){
				if(trim($desc['target']['new_val']) != ""){
					$target = $desc['target']['new_val'];
				}else{
					$target = null;
					$array_data['target_null_value'] = true;
				}
			}	
			
			$kpi_data = $this->ci->Kpi_model->get_specific_kpi_data($kpi_id, $frequency, $date, $this->user_id);
			
			if($kpi_data == false){
				$array_data['target']  = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
				$array_data['actual'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
				$process_data = $this->ci->Kpi_model->insert_kpi_data($array_data);
			}else{
				$array_data['actual'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
				$array_data['target'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
				$array_data['kpi_data_id'] = $kpi_data->kpi_data_id;
				
				$process_data =  $this->ci->Kpi_model->update_kpi_data($array_data);
			}
			
			if($kpis[$kpi_id]->islocked == 1){
				$islocked = true;	
			}
			
			if($process_data){
				if($kpis[$kpi_id]->islocked == 0){
					$update_data = array('islocked' => 1);
					$islocked = $this->ci->Kpi_model->update($kpi_id, $update_data, TRUE);
				}
				$response["response_update"] = true;
			}else{
				$response["response_update"] = false;
			}
			
			$islocked_kpis[$kpi_id] = array("kpi_id"=>$kpi_id, "islocked"=> $islocked);	
		}
		
		$response['islocked_kpis'] = $islocked_kpis;
		
		return $response;
	}
	
	
	public function save_kpi_data_week($data, $frequency, $date_type ){
		$kpis = array();
		$response = array("response_update" => false);
		$islocked_kpis = array();
		if(empty($data)){
			return $response;
		}	
		foreach($data as $column_id=>$desc){
			$column = explode('_', $column_id);
			$kpi_id = $column[0];
			$date = $column[1];
			
			$column_date = explode('-', $date);
			$year = $column_date[0];
			$week_num = $column_date[1];
			
			$actual =  null; 
			$islocked = false;
			$array_data = array( 'kpi_id' => $kpi_id, "actual_null_value" => false, "target_null_value" => false );
			
			if(!isset($kpis[$kpi_id])){
				$kpis[$kpi_id] = $this->ci->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);		
			}
			
			if($kpis[$kpi_id] == false || 
				(
					(!isset($desc['actual']) || !isset($desc['actual']['new_val'])) && 
					(!isset($desc['target']) || !isset($desc['target']['new_val']))
				)
			){
				continue;
			}
			
			$default_target = ($kpis[$kpi_id]->target == null) ? null : $kpis[$kpi_id]->target;
			$target =  $default_target;
			
			
			if(
				(isset($desc['actual']['new_val']) && trim($desc['actual']['new_val']) != ""  && !is_numeric($desc['actual']['new_val'])) || 
				(isset($desc['target']['new_val']) && trim($desc['target']['new_val']) != "" && !is_numeric($desc['target']['new_val'])) 
			){
					$array = array("result"=>"error", "message"=>"Failed to process data. Invalid value.");
					die(json_encode($array));
			}
			
			
			if(isset($desc['actual']['new_val'])){
				if(trim($desc['actual']['new_val']) != ""){
					$actual = $desc['actual']['new_val'];
				}else{
					$array_data['actual_null_value'] = true;
				}
			}
			
			if(isset($desc['target']['new_val']) && $default_target != null){
				if(trim($desc['target']['new_val']) != ""){
					$target = $desc['target']['new_val'];
				}else{
					$target = null;
					$array_data['target_null_value'] = true;
				}
			}	
			
			$kpi_data = $this->ci->Kpi_model->get_specific_kpi_data($kpi_id, $frequency, $date, $this->user_id);
			$datetime = new DateTime();
			$week_date = $datetime->setISODate($year, $week_num, 4)->format('Y-m-d');
			$array_data['date'] = $week_date;
			
			if($kpi_data == false){
				$array_data['target'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null");  
				$array_data['actual'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
				$process_data = $this->ci->Kpi_model->insert_kpi_data($array_data);
			}else{
				$array_data['actual'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
				$array_data['target'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
				$array_data['kpi_data_id'] = $kpi_data->kpi_data_id;
				$process_data =  $this->ci->Kpi_model->update_kpi_data($array_data);
			}
			
			if($kpis[$kpi_id]->islocked == 1){
				$islocked = true;	
			}
			
			if($process_data){
				if($kpis[$kpi_id]->islocked == 0){
					$update_data = array('islocked' => 1);
					$islocked = $this->ci->Kpi_model->update($kpi_id, $update_data, TRUE);
				}
				$response["response_update"] = true;
			}else{
				$response["response_update"] = false;
			}
			
			$islocked_kpis[$kpi_id] = array("kpi_id"=>$kpi_id, "islocked"=> $islocked);	
			
		}
		
		$response['islocked_kpis'] = $islocked_kpis;
		
		return $response;
	}
	
	public function save_kpi_data_month($data, $frequency, $date_type ){
		$kpis = array();
		$response = array("response_update" => false);
		$islocked_kpis = array();
		if(empty($data)){
			return $response;
		}
		foreach($data as $column_id=>$desc){
			$column = explode('_', $column_id);
			$kpi_id = $column[0];
			$date = $column[1];
			
			$column_date = explode('-', $date);
			$year = $column_date[0];
			$week_num = $column_date[1];
			
			$actual =  null; 
			$islocked = false;
			$array_data = array( 'kpi_id' => $kpi_id, "actual_null_value" => false, "target_null_value" => false );
			
			if(!isset($kpis[$kpi_id])){
				$kpis[$kpi_id] = $this->ci->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);		
			}
			
			if($kpis[$kpi_id] == false || 
				(
					(!isset($desc['actual']) || !isset($desc['actual']['new_val'])) && 
					(!isset($desc['target']) || !isset($desc['target']['new_val']))
				)
			){
				continue;
			}
			
			$default_target = ($kpis[$kpi_id]->target == null) ? null : $kpis[$kpi_id]->target;
			$target =  $default_target;
			
			
			if(
				(isset($desc['actual']['new_val']) && trim($desc['actual']['new_val']) != ""  && !is_numeric($desc['actual']['new_val'])) || 
				(isset($desc['target']['new_val']) && trim($desc['target']['new_val']) != "" && !is_numeric($desc['target']['new_val'])) 
			){
					$array = array("result"=>"error", "message"=>"Failed to process data. Invalid value.");
					die(json_encode($array));
			}
			
			if(isset($desc['actual']['new_val'])){
				if(trim($desc['actual']['new_val']) != ""){
					$actual = $desc['actual']['new_val'];
				}else{
					$array_data['actual_null_value'] = true;
				}
			}
			
			if(isset($desc['target']['new_val']) && $default_target != null){
				if(trim($desc['target']['new_val']) != ""){
					$target = $desc['target']['new_val'];
				}else{
					$target = null;
					$array_data['target_null_value'] = true;
				}
			}	
			
			
			$kpi_data = $this->ci->Kpi_model->get_specific_kpi_data($kpi_id, $frequency, $date, $this->user_id);
			$date = $date ."-05";
			$array_data['date'] = $date;
			if($kpi_data == false){
				$array_data['target'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
				$array_data['actual'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null");  
				$process_data = $this->ci->Kpi_model->insert_kpi_data($array_data);
			}else{
				$array_data['actual'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
				$array_data['target'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
				$array_data['kpi_data_id'] = $kpi_data->kpi_data_id;
				$process_data =  $this->ci->Kpi_model->update_kpi_data($array_data);
			}
			
			if($kpis[$kpi_id]->islocked == 1){
				$islocked = true;	
			}
			
			if($process_data){
				if($kpis[$kpi_id]->islocked == 0){
					$update_data = array('islocked' => 1);
					$islocked = $this->ci->Kpi_model->update($kpi_id, $update_data, TRUE);
				}
				$response["response_update"] = true;
			}else{
				$response["response_update"] = false;
			}
			
			$islocked_kpis[$kpi_id] = array("kpi_id"=>$kpi_id, "islocked"=> $islocked);		
		}
					
		$response['islocked_kpis'] = $islocked_kpis;
		
		return $response; 
	}
	
	
	public function save_kpi_data_quarter($data, $frequency, $date_type ){
		$kpis = array();
		$response = array("response_update" => false);
		$islocked_kpis = array();
		if(empty($data)){
			return $response;
		}
		foreach($data as $column_id=>$desc){
			$column = explode('_', $column_id);
			$kpi_id = $column[0];
			$date = $column[1];
			
			$column_date = explode('-', $date);
			$year = $column_date[0];
			$quarter_num = $column_date[1];
			
			$actual =  null; 
			$islocked = false;
			$array_data = array( 'kpi_id' => $kpi_id, "actual_null_value" => false, "target_null_value" => false );
			
			if(!isset($kpis[$kpi_id])){
				$kpis[$kpi_id] = $this->ci->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);		
			}
			
			if($kpis[$kpi_id] == false || 
				(
					(!isset($desc['actual']) || !isset($desc['actual']['new_val'])) && 
					(!isset($desc['target']) || !isset($desc['target']['new_val']))
				)
			){
				continue;
			}
			
			$default_target = ($kpis[$kpi_id]->target == null) ? null : $kpis[$kpi_id]->target;
			$target =  $default_target;
			
			
			if(
				(isset($desc['actual']['new_val']) && trim($desc['actual']['new_val']) != ""  && !is_numeric($desc['actual']['new_val'])) || 
				(isset($desc['target']['new_val']) && trim($desc['target']['new_val']) != "" && !is_numeric($desc['target']['new_val'])) 
			){
					$array = array("result"=>"error", "message"=>"Failed to process data. Invalid value.");
					die(json_encode($array));
			}
			
			if(isset($desc['actual']['new_val'])){
				if(trim($desc['actual']['new_val']) != ""){
					$actual = $desc['actual']['new_val'];
				}else{
					$array_data['actual_null_value'] = true;
				}
			}
			
			if(isset($desc['target']['new_val']) && $default_target != null){
				if(trim($desc['target']['new_val']) != ""){
					$target = $desc['target']['new_val'];
				}else{
					$target = null;
					$array_data['target_null_value'] = true;
				}
			}	
			
			$kpi_data = $this->ci->Kpi_model->get_specific_kpi_data($kpi_id, $frequency, $date, $this->user_id);
			$quarter_date = $this->ci->kpi_calendar->get_quartert_day($quarter_num);
			
			if($quarter_date != ""){
				$final_date = $year ."-". $quarter_date;
				$array_data['date'] = $final_date; 
				if($kpi_data == false){
					$array_data['target'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null");   
					$array_data['actual'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null");  
					$process_data = $this->ci->Kpi_model->insert_kpi_data($array_data);
				}else{
					$array_data['actual'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
					$array_data['target'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
					$array_data['kpi_data_id'] = $kpi_data->kpi_data_id;
					$process_data =  $this->ci->Kpi_model->update_kpi_data($array_data);
				}
			}
			
			if($kpis[$kpi_id]->islocked == 1){
				$islocked = true;	
			}
			
			if($process_data){
				if($kpis[$kpi_id]->islocked == 0){
					$update_data = array('islocked' => 1);
					$islocked = $this->ci->Kpi_model->update($kpi_id, $update_data, TRUE);
				}
				$response["response_update"] = true;
			}else{
				$response["response_update"] = false;
			}
			
			$islocked_kpis[$kpi_id] = array("kpi_id"=>$kpi_id, "islocked"=> $islocked);		
		}
					
		$response['islocked_kpis'] = $islocked_kpis;
		
		return $response;
	}
	
	public function save_kpi_data_year($data, $frequency, $date_type ){
		$kpis = array();
		$response = array("response_update" => false);
		$islocked_kpis = array();
		if(empty($data)){
			return $response;
		}
		foreach($data as $column_id=>$desc){
			$column = explode('_', $column_id);
			$kpi_id = $column[0];
			$date = $column[1];
			
			$actual =  null; 
			$islocked = false;
			$array_data = array( 'kpi_id' => $kpi_id, "actual_null_value" => false, "target_null_value" => false );
			
			if(!isset($kpis[$kpi_id])){
				$kpis[$kpi_id] = $this->ci->Kpi_model->get_kpis_as_member($this->organ_id, $this->plan_id, $this->user_id, $kpi_id);		
			}
			
			if($kpis[$kpi_id] == false || 
				(
					(!isset($desc['actual']) || !isset($desc['actual']['new_val'])) && 
					(!isset($desc['target']) || !isset($desc['target']['new_val']))
				)
			){
				continue;
			}
			
			$default_target = ($kpis[$kpi_id]->target == null) ? null : $kpis[$kpi_id]->target;
			$target =  $default_target;
			
			
			if(
				(isset($desc['actual']['new_val']) && trim($desc['actual']['new_val']) != ""  && !is_numeric($desc['actual']['new_val'])) || 
				(isset($desc['target']['new_val']) && trim($desc['target']['new_val']) != "" && !is_numeric($desc['target']['new_val'])) 
			){
					$array = array("result"=>"error", "message"=>"Failed to process data. Invalid value.");
					die(json_encode($array));
			}
			
			if(isset($desc['actual']['new_val'])){
				if(trim($desc['actual']['new_val']) != ""){
					$actual = $desc['actual']['new_val'];
				}else{
					$array_data['actual_null_value'] = true;
				}
			}
			
			if(isset($desc['target']['new_val']) && $default_target != null){
				if(trim($desc['target']['new_val']) != ""){
					$target = $desc['target']['new_val'];
				}else{
					$target = null;
					$array_data['target_null_value'] = true;
				}
			}	
			
			$kpi_data = $this->ci->Kpi_model->get_specific_kpi_data($kpi_id, $frequency, $date, $this->user_id);
			$date = $date."-02-05";
			$array_data['date'] = $date;	
			
			if($kpi_data == false){
				$array_data['target'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null");   
				$array_data['actual'] = $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null");  
				$process_data = $this->ci->Kpi_model->insert_kpi_data($array_data);
			}else{
				$array_data['actual'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $actual, "null"); 
				$array_data['target'] =  $this->prepare_format($kpis[$kpi_id]->kpi_format_id, $target, "null"); 
				$array_data['kpi_data_id'] = $kpi_data->kpi_data_id;
				$process_data =  $this->ci->Kpi_model->update_kpi_data($array_data);
			}
			
			if($kpis[$kpi_id]->islocked == 1){
				$islocked = true;	
			}
			
			if($process_data){
				if($kpis[$kpi_id]->islocked == 0){
					$update_data = array('islocked' => 1);
					$islocked = $this->ci->Kpi_model->update($kpi_id, $update_data, TRUE);
				}
				$response["response_update"] = true;
			}else{
				$response["response_update"] = false;
			}
			
			$islocked_kpis[$kpi_id] = array("kpi_id"=>$kpi_id, "islocked"=> $islocked);	
		}
		
		$response['islocked_kpis'] = $islocked_kpis;
		
		return $response;
	}
	
	
}