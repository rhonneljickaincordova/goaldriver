<?php if(!defined('BASEPATH')) exit('No direct script access allowed.');
class Kpi_calendar {
	private $ci;
	private $default_timezone;
	function __construct(){
		$this->ci = &get_instance();	
		$this->default_timezone = "Asia/Manila";
	}
	
	
	function get_quarter_num($month){
		$curQuarter = ceil($month/3);
		return $curQuarter;
	}
	
	/* ENTER DATA CALENDAR/ KPI DATA CALENDAR */
	public function get_new_enterdata_dates($frequency, $direction, $prev_date, $next_date, $interval = 1)
	{
		$new_prev_date = "0000-00-00";
		$new_next_date = "0000-00-00";
		$prev_date = new DateTime($prev_date);
		$next_date = new DateTime($next_date);
		
		switch($frequency)
		{
			case "weekly" :
				$interval = $interval * 7;
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P'.$interval.'D'));
					$next_date->sub(new DateInterval('P'.$interval.'D'));
				}else{
					$prev_date->add(new DateInterval('P'.$interval.'D'));
					$next_date->add(new DateInterval('P'.$interval.'D'));
				}
				$new_prev_date = $prev_date->format('Y-m-d');
				$new_next_date = $next_date->format('Y-m-d');
			break;
			case "monthly" :
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P'.$interval.'M'));
					$next_date->sub(new DateInterval('P'.$interval.'M'));
				}else{
					$prev_date->add(new DateInterval('P'.$interval.'M'));
					$next_date->add(new DateInterval('P'.$interval.'M'));
				}
				$new_prev_date = $prev_date->modify('first day of this month')->format('Y-m-d');
				$new_next_date = $next_date->modify('first day of this month')->format('Y-m-d');
			break;
			case "quarterly" :
				$interval = $interval * 3;
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P'.$interval.'M'));
					$next_date->sub(new DateInterval('P'.$interval.'M'));
				}else{
					$prev_date->add(new DateInterval('P'.$interval.'M'));
					$next_date->add(new DateInterval('P'.$interval.'M'));
				}
				$new_prev_date = $prev_date->modify('first day of this month')->format('Y-m-d');
				$new_next_date = $next_date->modify('first day of this month')->format('Y-m-d');
			break;
			case "yearly" :
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P'.$interval.'Y'));
					$next_date->sub(new DateInterval('P'.$interval.'Y'));
				}else{
					$prev_date->add(new DateInterval('P'.$interval.'Y'));
					$next_date->add(new DateInterval('P'.$interval.'Y'));
				}
				$new_prev_date = $prev_date->format('Y-01-01');
				$new_next_date = $next_date->format('Y-01-01');
			break;
			default: 
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P'.$interval.'D'));
					$next_date->sub(new DateInterval('P'.$interval.'D'));
				}else{
					$prev_date->add(new DateInterval('P'.$interval.'D'));
					$next_date->add(new DateInterval('P'.$interval.'D'));
				}
				$new_prev_date = $prev_date->format('Y-m-d');
				$new_next_date = $next_date->format('Y-m-d');
			break;
		}
		$array = array('new_prev_date' => $new_prev_date, 'new_next_date' => $new_next_date );
		
		return $array;
	}
	
	
	
	
	
	/* GAUGES CALENDAR */
	public function get_new_gauge_dates($frequency, $reset, $direction, $prev_date, $next_date)
	{
		switch($frequency){
			case "weekly" : 
				if($reset == "yearly"){
					$array = $this->get_new_gauge_dates_by_reset('yearly', $direction, $prev_date, $next_date);
				}else if($reset == "quarterly"){
					$array = $this->get_new_gauge_dates_by_reset('quarterly', $direction, $prev_date, $next_date);	
				}else if($reset == "monthly"){
					$array = $this->get_new_gauge_dates_by_reset('monthly', $direction, $prev_date, $next_date);
				}else{
					$array = $this->get_new_gauge_dates_by_reset('weekly', $direction, $prev_date, $next_date);
				}
			break;
			case "monthly" : 
				if($reset == "yearly"){
					$array = $this->get_new_gauge_dates_by_reset('yearly', $direction, $prev_date, $next_date);
				}else if($reset == "quarterly"){
					$array = $this->get_new_gauge_dates_by_reset('quarterly', $direction, $prev_date, $next_date);
				}else{
					$array = $this->get_new_gauge_dates_by_reset('monthly', $direction, $prev_date, $next_date);
				}
			break;
			case "quarterly" : 
				if($reset == "yearly"){
					$array = $this->get_new_gauge_dates_by_reset('yearly', $direction, $prev_date, $next_date);
				}else{
					$array = $this->get_new_gauge_dates_by_reset('quarterly', $direction, $prev_date, $next_date);
				}
			break;
			case "yearly" : 
				$array = $this->get_new_gauge_dates_by_reset('yearly', $direction, $prev_date, $next_date);
			break;
			default : 
				$array = $this->get_new_gauge_dates_by_reset($reset, $direction, $prev_date, $next_date);
			break;
			
		}
		
		return $array;
	}
	
	public function get_new_gauge_dates_by_reset($reset, $direction, $prev_date, $next_date){
		$new_prev_date = "0000-00-00";
		$new_next_date = "0000-00-00";
		$prev_date = new DateTime($prev_date);
		$next_date = new DateTime($next_date);
		
		switch($reset){
			case "weekly": 
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P7D'));
					$next_date->sub(new DateInterval('P7D'));
				}else{
					$prev_date->add(new DateInterval('P7D'));
					$next_date->add(new DateInterval('P7D'));
				}
				$new_prev_date = $prev_date->format('Y-m-d');
				$new_next_date = $next_date->format('Y-m-d');
			break;
			case "monthly": 
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P1M'));
				}else{
					$prev_date->add(new DateInterval('P1M'));
				}
				$new_prev_date = $prev_date->modify('first day of this month')->format('Y-m-d');
				$new_next_date = $prev_date->modify('last day of this month')->format('Y-m-d');
			break;
			case "quarterly": 
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P1M'));
					$new_next_date = $prev_date->modify('last day of this month')->format('Y-m-d');
					$prev_date->modify('first day of this month')->format('Y-m-d');
					$prev_date->sub(new DateInterval('P2M'));
					$new_prev_date = $prev_date->format('Y-m-d');
				}else{
					$prev_date->add(new DateInterval('P3M'));
					$new_prev_date = $prev_date->modify('first day of this month')->format('Y-m-d');
					$prev_date->add(new DateInterval('P2M'));
					$new_next_date = $prev_date->modify('last day of this month')->format('Y-m-d');
				}
			break;
			case "yearly" :
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P1Y'));
				}else{
					$prev_date->add(new DateInterval('P1Y'));
				}
				$new_prev_date = $prev_date->format('Y-01-01');
				$new_next_date = $prev_date->format('Y-12-31');
			break;
			default: 
				if($direction == "prev"){
					$prev_date->sub(new DateInterval('P1D'));
				}else{
					$prev_date->add(new DateInterval('P1D'));
				}
				$new_prev_date = $prev_date->format('Y-m-d');
				$new_next_date = $prev_date->format('Y-m-d');
			break;
		}
		
		$array = array('new_prev_date' => $new_prev_date, 'new_next_date' => $new_next_date );
		
		return $array;
	}
	
}