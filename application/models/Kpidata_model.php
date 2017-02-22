<?php

class Kpidata_model extends MY_Model{

	public $_table = 'kpi_users';
	public $primary_key = 'kpi_user_id';
	
	
	/************************************************************************************************
	*
	This function is used by KPI : ENTER DATA and GETTING DATES FOR GRAPH HIGHCHARTS
	*
	************************************************************************************************/
	public function enterData_dates($frequency, $prev_num, $next_num, $default_date = '0000-00-00', $is_filter = 0, $limit = 0, $from_date = '0000-00-00', $to_date = '0000-00-00')
	{
		$default_dates = array();
		$prev_num = (int)$prev_num;
		$next_num = (int)$next_num;
		$limit = (int)$limit;
		$is_filter = (int)$is_filter;
		$valid_default_date = $this->is_valid_date($default_date);
		$valid_from_date = $this->is_valid_date($from_date);
		$valid_to_date = $this->is_valid_date($to_date);
		if(($default_date != "" && $valid_default_date == false)||($from_date != "" && $valid_from_date == false) || ($to_date != "" && $valid_to_date == false)){
			$default_dates['dates'] = false;
			return $default_dates;
		}
		switch($frequency){
			case "daily": 
				$query = $this->db->query("CALL enterData_dates_daily($prev_num, $next_num, '".$default_date."', $is_filter, $limit, '".$from_date."', '".$to_date."')");	
				break;
			case "weekly": 
				$query = $this->db->query("CALL enterData_dates_weekly($prev_num, $next_num, '".$default_date."', $is_filter, $limit, '".$from_date."', '".$to_date."')");	
				break;
			case "monthly": 
				$query = $this->db->query("CALL enterData_dates_monthly($prev_num, $next_num, '".$default_date."', $is_filter, $limit, '".$from_date."', '".$to_date."')");	
				break;
			case "quarterly": 
				$query = $this->db->query("CALL enterData_dates_quarterly($prev_num, $next_num, '".$default_date."', $is_filter, $limit, '".$from_date."', '".$to_date."')");	
				break;
			default : 
				$query = $this->db->query("CALL enterData_dates_yearly($prev_num, $next_num, '".$default_date."', $is_filter, $limit, '".$from_date."', '".$to_date."')");	
			break;
		}
		
		$query->next_result();
		$dates = ($query->num_rows() > 0) ? $query->result() : false;	
		
		
		if($dates){
			$count = count($dates);
			$x = 1;
			
			foreach($dates as $date){
				if($x == 1){
					$default_dates['start_date'] = $date->selected_date;	
					$default_dates['start_date_formatted'] = $date->formatted_date;	
				}
				if($x == $count && $x != 1){
					$default_dates['last_date'] = $date->selected_date;	
					$default_dates['last_date_formatted'] = $date->formatted_date;	
				}
				$x++;
			}
		}
		
		$default_dates['dates'] = $dates;
		
		return $default_dates;
	}	
	
	
	/************************************************************************************************
	*
	GEet changes KPI data USE for KPI -> ENTER DATA
	*
	************************************************************************************************/
	public function get_kpi_data_daily($organ_id, $user_id, $default_target, $from_date, $to_date, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $limit, $kpi_id)
	{
		$sun = (int)$sun;
		$mon = (int)$mon;
		$tue = (int)$tue;
		$wed = (int)$wed;
		$thu = (int)$thu;
		$fri = (int)$fri;
		$sat = (int)$sat;
		$limit = (int)$limit;
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$default_target = (int)$default_target;
		$valid_from_date = $this->is_valid_date($from_date);
		$valid_to_date = $this->is_valid_date($to_date);
		if($valid_from_date == false ||  $valid_to_date == false){
			return false;
		}
		$query = $this->db->query("CALL enterData_kpiData_Daily($organ_id, $user_id, $default_target, '".$from_date."', '".$to_date."', $sun, $mon, $tue, $wed, $thu, $fri, $sat, $limit, $kpi_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;	
	}
	
	public function get_kpi_data($frequency, $organ_id, $user_id, $from_date, $to_date)
	{
		$default_dates = array();
		$organ_id = (int)$organ_id;
		$user_id = (int)$user_id;
		$valid_from_date = $this->is_valid_date($from_date);
		$valid_to_date = $this->is_valid_date($to_date);
		if($valid_from_date == false ||  $valid_to_date == false){
			return false;
		}
		switch($frequency)
		{
			case "daily": 	
				$query = $this->db->query("CALL kpiData_daily_load($organ_id, $user_id, '".$from_date."', '".$to_date."')");	
				break;
			case "weekly": 
				$query = $this->db->query("CALL kpiData_weekly_load($organ_id, $user_id, '".$from_date."', '".$to_date."')");	
				break;
			case "monthly": 
				$query = $this->db->query("CALL kpiData_monthly_load($organ_id, $user_id, '".$from_date."', '".$to_date."')");	
				break;
			case "quarterly": 
				$query = $this->db->query("CALL kpiData_quarterly_load($organ_id, $user_id, '".$from_date."', '".$to_date."')");	
				break;
			default : 
				$query = $this->db->query("CALL kpiData_yearly_load($organ_id, $user_id, '".$from_date."', '".$to_date."')");	
			break;
		}
		
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;	
	}	
	
	public function get_specific_kpi_data($organ_id, $user_id, $kpi_id, $frequency, $date)
	{
		$organ_id = (int)$organ_id;
		$user_id = (int)$user_id;
		$kpi_id = (int)$kpi_id;
		$valid_date = $this->is_valid_date($date);
		if($date == false || !in_array($frequency, $this->frequencies)){
			return false;
		}
		$query = $this->db->query("CALL kpiData_specific_load($organ_id, $user_id, $kpi_id, '".$frequency."', '".$date."')");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->row() : false;		
	}
	
	
	/************************************************************************************************
	*
	ADD CHANGES KPI DATA / INSERT KPI DATA
	*
	************************************************************************************************/
	public function insert_kpi_data($data)
	{
		$user_data = (object)$this->session->userdata();
		$user_id = (int)$user_data->user_id;
		$organ_id = (int)$user_data->organ_id;
		$plan_id = (int)$user_data->plan_id;
		
		$permission = $this->Kpiusers_model->kpi_user_exists($data['kpi_id'], $user_id);
		
		if($permission)
		{
			$kpi_data = array(
				"kpi_id" => $data['kpi_id'],
				"organ_id" => $organ_id,
				"plan_id" => $plan_id,
				"user_id" => $user_id,
				"difference" => 0,
				"trend" => 0,
				"actual" => $data['actual'],
				"target" => $data['target'],
				"target_null" => $data['target_null'],
				"date" => $data['date']
			);
			
			return $this->db->insert('kpi_data', $kpi_data);
		}
		
		return false;
	}
	/************************************************************************************************
	*
	UPDATE CHANGES KPI DATA
	*
	************************************************************************************************/
	public function update_kpi_data($data)
	{
		$update_data = false;
		
		if($data['actual_null_value'] == true){
			$update_data = true;
			$this->db->set('actual', NULL);
		}else if(array_key_exists('actual', $data) && is_null($data['actual']) == false){
			$update_data = true;
			$this->db->set('actual', $data['actual']);
		}
		
		if($data['target_null_value'] == true){
			$update_data = true;
			$this->db->set('target', NULL);
		}else if(array_key_exists('target', $data) && is_null($data['target']) == false){
			$update_data = true;
			$this->db->set('target', $data['target']);
		}
		
		$this->db->set('target_null', $data['target_null']);
		
		if($update_data == true)
		{
			$this->db->set('updated', 'NOW()', FALSE);
			$this->db->where('kpi_data_id', $data['kpi_data_id']);
			return $this->db->update('kpi_data');
		}
		
		return false;
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
	
	
	private function is_valid_date($date){
		preg_match('/\d{4}-\d{2}-\d{2}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}
	
	
}