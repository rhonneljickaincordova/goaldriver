<?php if(!defined('BASEPATH')) exit('No direct script access allowed.');
class Highcharts {
	private $ci;
	private $highchart_id;
	private $user_id = 0;
	private $organ_id = 0;
	private $plan_id = 0;
	private $is_organisation_owner = false;
	private $integers = array(1, 3, 9, 10, 11, 12, 13, 14, 15, 16);
	private	$decimals = array(2, 4, 5, 6, 7, 8);
	private	$is_filter = false;
	private	$filter_data = array();
	
	function __construct()
	{
		$this->ci = &get_instance();	
		$this->user_id = $this->ci->session->userdata('user_id');
		$this->organ_id = $this->ci->session->userdata('organ_id');
		$this->plan_id = $this->ci->session->userdata('plan_id');
		$this->ci->load->helper('highchart_helper');
		$this->ci->load->model('Graph_model');
		$this->ci->load->model('Kpi_model');
		$this->ci->load->library('Kpi_calendar');
		$this->ci->load->model('Organisation_model');
		
		$this->is_organisation_owner = $this->ci->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
	}
	

	
	
	/************************************************************************************************
	*
	STEP 0 A: GENERATE HIGHCHART GRAPHS FROM GRAPHS WITH bShowOnDash
	*
	************************************************************************************************/
	public function ajax_generate_highchart($graphs)
	{
		foreach($graphs as $graph)
		{
			if($graph->graph_type == "pie" || ($graph->graph_type != "pie" && $graph->bShowBreakdown != 1))
			{
				$this->addHighchart($graph->graph_id, $graph, $graph->graph_type, $graph->graph_name, $graph->description)
				->setValue('width', "100%")
				->setValue('height', '300px')
				->setValue('class', "kpi_hicharts highchart_container_fixed")
				->load_default_options()
				->get_kpi_data()
				->generate_options()
				->get_highchart_html();
			}
			else
			{
				$this->addHighchart($graph->graph_id, $graph, $graph->graph_type, $graph->graph_name, $graph->description)
				->setValue('width', "100%")
				->setValue('height', '300px')
				->setValue('class', "kpi_hicharts highchart_container_fixed")
				->load_default_options()
				->get_graph_users()
				->get_kpi_data()
				->generate_options()
				->get_highchart_html();
			}
			
		}
		
		$highcharts = (array)$this->ci->load->get_var('highcharts');
		$array = array("result" => "success", "highchart_graph_count"=>count($highcharts));
		foreach($highcharts as $highchart)
		{
			$highchart_data = array(
						"id" => $highchart->highchart_id,  
						"options" => $highchart->options,
						"html" => $highchart->html,							
						"date_type" => $highchart->kpi->frequency,
						"type" => $highchart->type,
						"error" => $highchart->error,
						"error_type" => $highchart->error_type,
						"error_message" => $highchart->error_message
					);
			
			$array["highcharts"][] = $highchart_data;
		}
		return $array;
	}
	
	
	
	
	/************************************************************************************************
	*
	STEP 0 B: GENERATE HIGHCHART GRAPH for FILTERED GRAPH
	*
	************************************************************************************************/
	public function ajax_filter_graph($from, $to, $highchart_id, $users, $show_average, $show_break_down)
	{	
		$highchart_id = (int)$highchart_id;
		if($highchart_id == 0){return false;}
		
		$graph = $this->ci->Graph_model->get_graphs($this->organ_id, $this->user_id, $highchart_id);
		if($graph)
		{
			$this->is_filter = true;
			$this->filter_data = array(
							"from" => $from, 
							"to" => $to, 
							"highchart_id" => $highchart_id, 
							"users" => $users, 
							"show_break_down" => $show_break_down, 
							"show_average" => $show_average
						);
			
			$this->addHighchart($graph->graph_id, $graph, $graph->graph_type, $graph->graph_name, $graph->description)
					->get_filtered_graph_users()
					->get_kpi_data()
					->generate_options();
			
			$highcharts = (array)$this->ci->load->get_var('highcharts');
			$highchart = $highcharts[$highchart_id];
			$array = array(
				"result" => "success",
				"id" => $highchart->highchart_id,  
				"options" => $highchart->options,
				"type" => $highchart->type,
				"error" => $highchart->error,
				"error_type" => $highchart->error_type,
				"error_message" => $highchart->error_message
			);
			
			die(json_encode($array));		
		}
	}
	
	
	
	
	
	
	/************************************************************************************************
	*
	STEP 1
	*
	************************************************************************************************/
	public function addHighchart($highchart_id, $graph, $type, $title = null, $subtitle = null)
	{
		$error = false;
		$error_type = "";
		$error_message = "";
		$highcharts = (array)$this->ci->load->get_var('highcharts');		
		
		$kpi = $this->ci->Kpi_model->get_kpis($this->organ_id, $this->plan_id, $this->user_id, $graph->kpi_id);	
		
		if($kpi == false)
		{
			$kpi = array();
			$error = true;
			$error_type = "kpi";
			$error_message = "Graph has invalid KPI.";
		}	
		
		$valueDecimals = (in_array($kpi->kpi_format_id, $this->decimals)) ? 2 : 0;
		$show_break_down = ($graph->bShowBreakdown == 1) ? true : false;
		$show_target = (is_numeric($kpi->target)) ? 1 : 0;
		$highcharts[$highchart_id] = (object)array(
											'highchart_id' => $highchart_id, 
											'kpi' => $kpi, 
											'type' => $type, 
											'graph' => $graph, 
											'title' => $title,
											'show_target' => $show_target, 
											'valueDecimals' => $valueDecimals, 
											'bShowAverage' => $graph->bShowAverage, 
											'bShowBreakdown' => $graph->bShowBreakdown, 
											'show_average' => $graph->bShowAverage, 
											'show_break_down' => $show_break_down, 
											'show_gauge_on_dash' => $graph->bShowGaugeOnDash, 
											'options' => array( 'title' => array("text"=>$title) , 'subtitle' => array("text"=>$subtitle) ),
											'error' => $error, 
											'error_type' => $error_type, 
											'error_message' => $error_message
									);
		
		$this->highchart_id = $highchart_id;
		$this->ci->load->vars(array('highcharts' => $highcharts));
		return $this;
	}
	
	
	
	
	
	/************************************************************************************************
	*
	STEP 2 : Load default highchart options
	*
	************************************************************************************************/
	public function load_default_options()
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');		
		$highchart = $highcharts[$this->highchart_id];
		$default_options = array();
		$graph_type = strtolower($highchart->type);
		
		if($graph_type == "line" || $graph_type == "bar"){
			$default_options = array(
				"chart" => array(
					"renderTo" => "highchart-".$this->highchart_id
				)
			);
		}else if($graph_type == "pie - single kpi" || $graph_type == "pie - multipe kpi"){
			$default_options = array(
				"chart" => array(
					"renderTo" => "highchart-".$this->highchart_id,
					"type" => 'pie',
				)
			);
		}else if($graph_type == "gauge"){
			$default_options = array(
				"chart" => array(
					"renderTo" => "highchart-".$this->highchart_id,
					"type" => 'solidgauge'
				)
			);
		}
		
		$highchart_options = (array)$highchart->options;
		$highchart->options = array_merge($highchart_options, $default_options);
		$highcharts[$this->highchart_id] = $highchart; 
		$this->ci->load->vars(array('highcharts' => $highcharts));
		return $this;		
	}
	
	
	
	
	/************************************************************************************************
	*
	STEP 3 MAIN
	*
	************************************************************************************************/
	private function get_kpi_data()
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');
		$highchart = $highcharts[$this->highchart_id];
		
		$kpi = (object)$highchart->kpi;
		$graph = (object)$highchart->graph;
		$filter_date = 0;
		$from_date = "0000-00-00";
		$to_date = "0000-00-00";
		if($this->is_filter == true){
			$highchart->show_average = $this->filter_data['show_average'];
			$highchart->show_break_down = $this->filter_data['show_break_down'];
			
			if(isset($this->filter_data['from']) && isset($this->filter_data['to'])){
				$valid_from = $this->is_valid_date($this->filter_data['from']);
				$valid_to = $this->is_valid_date($this->filter_data['to']);
				if($valid_from == true && $valid_to == true){
					$filter_date = 1;
					$from_date = $this->filter_data['from'];
					$to_date = $this->filter_data['to'];	
				}	
			}
		}
		
		if(($this->is_filter == false && $highchart->show_break_down == false) || ($this->is_filter == true && $this->filter_data['show_break_down'] == false && $highchart->users_from_database == true))
		{
			$data = $this->get_unbroken_data($graph, $kpi, $filter_date, $from_date, $to_date);	
			$highchart->list_dates = $data['list_dates'];
			$highchart->last_query = $data['last_query'];
		}
		else
		{
			$data = $this->get_broken_data($graph, $kpi, $highchart->final_users, $filter_date, $from_date, $to_date);
			
			$highchart->list_dates = $data['list_dates'];
			$highchart->last_query = $data['last_query'];
			$highchart->actual_dates = $data['actual_dates'];
			$highchart->users_data = $data['users_data'];
		}
		
		if($data['list_dates'] == false)
		{
			$highchart->error = true;
			$highchart->error_type = "date";
			$highchart->error_message = "Invalid Date";
		}
		
		$highcharts[$this->highchart_id] = $highchart; 
		$this->ci->load->vars(array('highcharts' => $highcharts));
		
		return $this;
	}
	
	
	
	
	
	
	/************************************************************************************************
	*
	STEP 3 A : This function is  when loading unfiltered data and breakdown = false
				Gets sequence of dates and checks if has kpi data
				It automatically gets sum_actuals and sum_targets and avg_actuals, avg_targets ready for aggregate sum/average
	*
	************************************************************************************************/
	private function get_unbroken_data($graph, $kpi, $filter_date, $from_date = "0000-00-00", $to_date = "0000-00-00")
	{
		$default_target = (is_numeric($kpi->target)) ? $kpi->target : 0;
		if($kpi->frequency == "daily"){
			$unselected_day = 0;
			if($kpi->in_sun != 1){$unselected_day++;}
			if($kpi->in_mon != 2){$unselected_day++;}
			if($kpi->in_tue != 3){$unselected_day++;}
			if($kpi->in_wed != 4){$unselected_day++;}
			if($kpi->in_thu != 5){$unselected_day++;}
			if($kpi->in_fri != 6){$unselected_day++;}
			if($kpi->in_sat != 7){$unselected_day++;}
			
			$list_dates = $this->ci->Graph_model->get_graphDates_withData(
							$kpi->frequency, $graph->graph_id, $kpi->kpi_id, $this->user_id, $default_target, 7, $from_date, $to_date, $graph->gu_count, 0, $filter_date,$unselected_day, $kpi->in_sun, $kpi->in_mon, $kpi->in_tue, $kpi->in_wed, $kpi->in_thu, $kpi->in_fri, $kpi->in_sat);	
		}else{
			$list_dates = $this->ci->Graph_model->get_graphDates_withData($kpi->frequency, $graph->graph_id, $kpi->kpi_id, $this->user_id, $default_target, 5, $from_date, $to_date, $graph->gu_count, 0, $filter_date);	
		}
		
		$data = array(
			"list_dates" => $list_dates, 
			"last_query" => $this->ci->db->last_query()
		);
		
		return $data;
	}
	
	
	
	
	
	/************************************************************************************************
	*
	STEP 4 MAIN
	*
	************************************************************************************************/
	public function generate_options()
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');
		$highchart = $highcharts[$this->highchart_id];
		if($highchart->error == true){return $this;}
		
		$options = array();
		$kpi = (object)$highchart->kpi;
		$graph = (object)$highchart->graph;
		
		$list_dates = $highchart->list_dates;
		$show_target = $highchart->show_target;
		$show_average = $highchart->show_average;
		$valueDecimals = $highchart->valueDecimals;
		$graph_type = strtolower($graph->graph_type);
		$highchart_options = (array)$highchart->options;
		
		if(($this->is_filter == false && $highchart->show_break_down == false) || ($this->is_filter == true && $this->filter_data['show_break_down'] == false && $highchart->users_from_database == true))
		{
			if($graph_type == "line" || $graph_type == "bar")
			{
				$data = $this->generate_unbroken_options($list_dates, $kpi->agg_type, $graph_type, $show_target, $show_average, $valueDecimals);	
			}
			else if($graph_type == "pie - single kpi" || $graph_type == "pie - multipe kpi")
			{
				$data = $this->generate_pie_options($highchart->title, $kpi->name, $list_dates, $kpi->agg_type, $graph_type, $show_target, $show_average, $valueDecimals, "unbroken");
				$options['title'] = $data['title'];
			}
		}
		else
		{
			$actual_dates = $highchart->actual_dates;
			$users_data = $highchart->users_data;
			
			if($graph_type == "line" || $graph_type == "bar")
			{
				$data = $this->generate_broken_options($list_dates, $kpi->agg_type, $graph_type, $show_target, $show_average, $valueDecimals, $actual_dates, $users_data);
			}
			else if($graph_type == "pie - single kpi" || $graph_type == "pie - multipe kpi")
			{
				$data = $this->generate_pie_options($highchart->title, $kpi->name, $list_dates, $kpi->agg_type, $graph_type, $show_target, $show_average, $valueDecimals, "broken", $actual_dates, $users_data);
				$options['title'] = $data['title'];
			}
		}	
		
		$series = $data['series'];
		$actual_dates = $data['actual_dates'];
		
		$options["xAxis"] = array("categories" => array_values($actual_dates));		
		$options['series'] = array_values( $series );
		$options['tooltip']['valueDecimals'] = $valueDecimals;
		$options['tooltip']['valueSuffix'] = mb_convert_encoding($kpi->format_suffix, 'UTF-8', 'HTML-ENTITIES');
		$options['tooltip']['valuePrefix'] = mb_convert_encoding($kpi->format_prefix, 'UTF-8', 'HTML-ENTITIES'); 
		
		$highchart->options = array_merge($highchart_options, $options);
		$highcharts[$this->highchart_id] = $highchart; 
		$this->ci->load->vars(array('highcharts' => $highcharts));
		
		return $this;
	}
	
	
	
	
	/************************************************************************************************
	*
	STEP 4 A : This function is  when loading unfiltered data and breakdown = false
				Gets sequence of dates and checks if has kpi data
				It automatically gets sum_actuals and sum_targets and avg_actuals, avg_targets ready for aggregate sum/average
	*
	************************************************************************************************/
	public function generate_unbroken_options($list_dates, $agg_type, $graph_type, $show_target, $show_average, $valueDecimals)
	{
		$series = array();
		$actuals = array();
		$targets = array();
		
		foreach($list_dates as $date)
		{
			if($agg_type == "average"){
				$actuals[] = floatval($date->avg_actuals);
				$actuals[] = floatval($date->avg_targets);
			}else{
				$actuals[] = floatval($date->sum_actuals);
				$target[] = floatval($date->sum_targets);
			}
			$actual_dates[] = $date->formatted_date;
		}
		
		$series[] = array(
			"name" => "Actuals",
			"data" => $actuals
		);
			
		if($show_target == 1){
			$type = ($graph_type == "bar") ? "column" : "line";
			$series[] = array(
				"name" => "Targets",
				"data" => $target,
				"type" => $type
			);	
		}
		
		if($show_average == 1){
			$average = $this->show_average($actuals, $valueDecimals);
			$series[] = array(
				"name" => "Average",
				"data" => $average
			);	
		}
		
		$data = array(
			"series" => $series,
			"actual_dates" => $actual_dates
		);
		
		return $data;
	}
	
	
	
	
	/************************************************************************************************
	*
	STEP 3 B: GET GRAPH USERS
	*
	************************************************************************************************/
	public function get_graph_users()
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');
		$highchart = $highcharts[$this->highchart_id];
		if($highchart->error == true){ return $this; }
		
		$graph = $highchart->graph;
		$final_users = $this->ci->Graph_model->get_graph_users($graph->graph_id);
		
		if($final_users == false || empty($final_users) ){
			$highchart->error = true;
			$highchart->error_type = "user";
			$highchart->error_message = "No users selected to pull kpi data.";	
		}
		
		$highchart->final_users = $final_users;	
		$highcharts[$this->highchart_id] = $highchart; 
		$this->ci->load->vars(array('highcharts' => $highcharts));
		return $this;
	}
	
	
	
	
	/************************************************************************************************
	*
	STEP 4 : gets KPI DATA by User we also generate dates on the queried data
	*
	************************************************************************************************/
	public function get_broken_data($graph, $kpi, $final_users, $filter_date, $from_date = "0000-00-00", $to_date = "0000-00-00")
	{
		$default_target = (is_numeric($kpi->target)) ? $kpi->target : 0;
		$list_dates = false;
		$actual_dates = array();
		$users_data = array();
		
		if($kpi->frequency == "daily")
		{
			$unselected_day = 0;
			if($kpi->in_sun != 1){$unselected_day++;}
			if($kpi->in_mon != 2){$unselected_day++;}
			if($kpi->in_tue != 3){$unselected_day++;}
			if($kpi->in_wed != 4){$unselected_day++;}
			if($kpi->in_thu != 5){$unselected_day++;}
			if($kpi->in_fri != 6){$unselected_day++;}
			if($kpi->in_sat != 7){$unselected_day++;}
			
			foreach($final_users as $user)
			{
				$user_id = $user->user_id;
				$user_data = $this->ci->Graph_model->get_graphDates_withData(
							$kpi->frequency, $graph->graph_id, $kpi->kpi_id, $user_id, $default_target, 7, $from_date, $to_date, $graph->gu_count, 1, $filter_date, $unselected_day, $kpi->in_sun, $kpi->in_mon, $kpi->in_tue, $kpi->in_wed, $kpi->in_thu, $kpi->in_fri, $kpi->in_sat);	
				
				$users_data[] = array(
					"user_id" => $user_id,
					"full_name" => $user->first_name . " ". $user->last_name,
					"data" => $user_data
				);
				if($user_data){
					$list_dates = true;
				}
				if(empty($actual_dates)){
					foreach($user_data as $date){
						$actual_dates[$date->selected_date] = $date->formatted_date;
					}
				}
				$last_query[] = $this->ci->db->last_query();
			}
		}
		else
		{
			foreach($final_users as $user)
			{
				$user_id = $user->user_id;
				$user_data = $this->ci->Graph_model->get_graphDates_withData($kpi->frequency, $graph->graph_id, $kpi->kpi_id, $user_id, $default_target, 5, $from_date, $to_date, $graph->gu_count, 1, $filter_date);	
				
				$users_data[] = array(
					"user_id" => $user_id,
					"full_name" => $user->first_name . " ". $user->last_name,
					"data" => $user_data
				);
				
				if($user_data){
					$list_dates = true;
				}
				if(empty($actual_dates)){
					foreach($user_data as $date){
						$actual_dates[$date->selected_date] = $date->formatted_date;
					}
				}
				$last_query[] = $this->ci->db->last_query();
			}
		}
		
		$data = array(
			"users_data" => $users_data,
			"list_dates" => $list_dates,
			"actual_dates" => $actual_dates,
			"last_query" => $last_query
		);
		
		return $data;
	}
	
	
	/************************************************************************************************
	*
	STEP 5 B : This function is  when loading unfiltered data and breakdown = true
				Gets sequence of dates and checks if has kpi data
	*
	************************************************************************************************/
	public function generate_broken_options($list_dates, $agg_type, $graph_type, $show_target, $show_average, $valueDecimals, $actual_dates, $users_data)
	{
		$series = array();
		$avg_actuals = array();
		$users_count = count($users_data);
		
		foreach($users_data as $user_data)
		{
			$x = 0;
			$actuals = array();
			$targets = array();
			if($user_data['data'] != false)
			{
				
				foreach($user_data['data'] as $data_by_date){
					$actuals[] = floatval($data_by_date->actual);			
					$targets[] = floatval($data_by_date->target);	
					$avg_actuals[$x] = (isset($avg_actuals[$x])) ? $avg_actuals[$x] + $data_by_date->actual : $data_by_date->actual;
					$x++;
				}
			}
			else
			{
				foreach($actual_dates as $actual_date){
					$actuals[] = floatval(0);			
					$targets[] = floatval(0);			
					$avg_actuals[$x] = (isset($avg_actuals[$x])) ? $avg_actuals[$x] : 0;
					$x++;
				}
			}
			
			$series[] = array(
				"name" => "Actuals(".$user_data['full_name'].")",
				"data" => $actuals
			);
			
			if($show_target == 1){
				$type = (strtolower($graph_type) == "bar") ? "column" : "line";
				$series[] = array(
					"name" => "Targets(".$user_data['full_name'].")",
					"data" => $targets,
					"type" => $type
				);	
			}
		}
			
		if($show_average == 1){
			$average = $this->show_average($avg_actuals, $valueDecimals);
			$series[] = array(
				"name" => "Average",
				"data" => $average
			);	
		}
		
		$data = array(
			"series" => $series,
			"actual_dates" => $actual_dates
		);
		
		return $data;
	}
	
	
	
	/************************************************************************************************
	*
	STEP (PIE CHART OPTIONS) :
	*
	************************************************************************************************/
	/* generate_unbroken_options($list_dates, $agg_type, $graph_type, $show_target, $show_average, $valueDecimals) */
	/* generate_broken_options($list_dates, $agg_type, $graph_type, $show_target, $show_average, $valueDecimals, $actual_dates, $users_data) */
	public function generate_pie_options($highchart_title, $kpi_name, $list_dates, $agg_type, $graph_type, $show_target, $show_average, $valueDecimals, $data_type = "unbroken", $actual_dates = array(), $users_data = array())
	{
		$series = array();
		
		if($data_type == "unbroken")
		{
			$actuals = array();
			
			foreach($list_dates as $date)
			{
				if($agg_type == "average"){
					$actuals[] = floatval($date->avg_actuals);
				}else{
					$actuals[] = floatval($date->sum_actuals);
				}
				$actual_dates[] = $date->formatted_date;
			}
			
			$total = floatval(array_sum($actuals));
			$series[] = array(
				"name" => $kpi_name,
				"colorByPoint" => true,
				"data" => array_values(
					array(
						array(
							"name" => $kpi_name,
							"y" => $total
						)
					)
				)
			);
			if(count($actual_dates) == 1){
				$title = array("text"=> $highchart_title . "(" .$actual_dates[0]. ")");
			}else{
				$end_date = $actual_dates[count($actual_dates) - 1];
					$title = array("text"=> $highchart_title . " (".$actual_dates[0]."-".$end_date.")");
			}
		}
		else
		{
			$date_count = count($actual_dates);
			foreach($users_data as $user_data)
			{
				$actuals = array();
				if($user_data['data'] != false)
				{
					foreach($user_data['data'] as $data_by_date)
					{
						$actuals[] = floatval($data_by_date->actual);			
					}
					$total = array_sum($actuals);
					$data[] = array(
						"name" => $user_data['full_name'],
						"y" => $total
					);
				}
				else
				{
					$data[] = array(
							"name" => $user_data['full_name'],
							"y" => 0
						);
				}
			}
			
			$series[] = array(
				"name" => $kpi_name,
				"colorByPoint" => true,
				"data" => array_values( $data)
			);
			
			$x = 1;
			foreach($actual_dates as $date=>$formatted_date)
			{
				if($x == 1){
					$start_date = $formatted_date;	
				}	
				
				if($x == $date_count){
					$end_date = $formatted_date;
				}
				$x++;
			}
			if(count($actual_dates) == 1){
				$title = array("text"=> $highchart_title . "(" .$start_date. ")");
			}else{
				$title = array("text"=> $highchart_title . " (".$start_date."-".$end_date.")");
			}
		}
		
		$data = array(
			"series" => $series,
			"title" => $title,
			"actual_dates" => $actual_dates
		);
		
		return $data;
	}
	
	
	/************************************************************************************************
	*
	STEP 3 C : 
	*
	************************************************************************************************/
	public function get_filtered_graph_users()
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');
		$highchart = $highcharts[$this->highchart_id];
		if($highchart->error == true){ return $this; }
		
		$graph = $highchart->graph;
		$final_users = array();
		$users_from_database = true;
		
		$graph_users = $this->ci->Graph_model->get_graph_users($graph->graph_id);
		
		if(is_array($this->filter_data['users']))
		{
			$users_from_database = false;
			$tmp_users = array();
			
			foreach($this->filter_data['users'] as $user)
			{
				$existence = $this->ci->Graph_model->get_graph_users($graph->graph_id, $user['id']);
				if($existence)
				{
					$tmp_users[] = $existence;
				}
			}
			$final_users = $tmp_users;
		}	
		
		if($final_users == false || empty($final_users) )
		{
			$highchart->error = true;
			$highchart->error_type = "user";
			$highchart->error_message = "No users selected to pull kpi data.";	
		}
		
		if(count($final_users) == count($graph_users)){
			$users_from_database = true;
		}
		
		$highchart->final_users = $final_users;	
		$highchart->users_from_database = $users_from_database;	
		
		$highcharts[$this->highchart_id] = $highchart; 
		$this->ci->load->vars(array('highcharts' => $highcharts));
		return $this;
	}
	
	
	
	
	/************************************************************************************************
	*
	FINAL STEP : Generate html
	*
	************************************************************************************************/
	public function get_highchart_html($is_kpi_highchart = true, $container_class = "highchart_container col-md-12")
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');
		$highchart = $highcharts[$this->highchart_id];
		
		$kpi = (object)$highchart->kpi;
		$graph = (object)$highchart->graph;
		$class = $highchart->class;
		$width = isset($highchart->width) ? "width:".$highchart->width .";" : "";
		$height = isset($highchart->height) ? "height:".$highchart->height .";" : "";
		
		$html = "<div class='$container_class'>";
			if($is_kpi_highchart == true)
			{
				$html_data = array( 
					"data-toggle='modal'", 
					"data-target='#filter_highchartModal'",
					"data-kpi_id='" . $graph->kpi_id . "'", 
					"data-show_average='" . $graph->bShowAverage . "'",
					"data-show_break_down='" . $graph->bShowBreakdown . "'",
					"data-highchart_id='" . $this->highchart_id . "'",
					"data-frequency='" . $kpi->frequency . "'"
				);
				$html_data_string = implode($html_data, " ");
				$html .= "<a href='#' title='Filter' ".$html_data_string." class='filter_highchart' >";	
				$html .= "<i class='fa fa-pencil-square-o' style='font-size:25px;float:right;padding-top:10px;'></i>";
				$html .= "</a>";
			}
		$html .= "<div id='highchart-".$this->highchart_id."' class='$class' style='$width $height'></div>";
		$html .= "</div>";
		
		$highchart->html = $html;
		$highcharts[$this->highchart_id] = $highchart; 
		$this->ci->load->vars(array('highcharts' => $highcharts));
		
		return $this;
	}

	
	/************************************************************************************************
	*
	EXTENDED FUNCTIONS
	*
	************************************************************************************************/
	public function show_average($actuals, $valueDecimals)
	{
		$average_actuals = array();
		$ave_actual = round(array_sum($actuals) /  count($actuals), $valueDecimals);
		$z = 0;
		
		while($z < count($actuals))
		{
			$average_actuals[] = $ave_actual;	
			$z++;
		}
			
		return array_values($average_actuals);
	}
	
	
	private function is_valid_date($date)
	{
		preg_match('/\d{4}-\d{2}-\d{2}/', $date, $match);
		return (!empty($match)) ? true : false;
	}
	
	public function setValue($name, $value)
	{
		$highcharts = (array)$this->ci->load->get_var('highcharts');	
		$highcharts[$this->highchart_id]->$name = $value;
		$this->ci->load->vars(array('highcharts' => $highcharts));
		return $this;
	}	
	

	
}
?>

