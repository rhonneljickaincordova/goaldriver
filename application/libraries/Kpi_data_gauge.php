<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Kpi_data_gauge{
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
		
		$data = array();
		$array = array();
		$kpi_id = (int)$kpi_id;
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
		
		if(!empty($data)){
			$array['entries'] = $data;
		}
		
		return $array;
	}
	
	
	public function prepare_dates($dates){
		$new_dates = array();
		
		foreach($dates as $date){
			$new_dates[] = "'".$date."'";
		}
		
		$final_dates = implode(",", $new_dates);
		return $final_dates; 
	}
	
}