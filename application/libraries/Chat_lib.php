<?php if(!defined('BASEPATH')) exit('No direct script access allowed.');
/* 
Highcharts_gauge is used for creating guages based from graph table and thier corresponding graph users, kpi and kpi data.
It also uses Kpi_data_gauge.php library located in Libraries folder. This file is used for getting kpi data. 
 */
class Chat_lib {
	private $ci;
	
	function __construct()
	{
		$this->ci = &get_instance();	
		$this->user_id = $this->ci->session->userdata('user_id');
		$this->organ_id = $this->ci->session->userdata('organ_id');
		$this->plan_id = $this->ci->session->userdata('plan_id');
	}
	
	/* 1: get first users from organization except current user for users_list */
	/* 2: */
	/* load first  */
}