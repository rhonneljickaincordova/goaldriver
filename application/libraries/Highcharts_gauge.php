<?php if(!defined('BASEPATH')) exit('No direct script access allowed.');
/* 
Highcharts_gauge is used for creating guages based from graph table and thier corresponding graph users, kpi and kpi data.
It also uses Kpi_data_gauge.php library located in Libraries folder. This file is used for getting kpi data. 
 */
class Highcharts_gauge {
	private $ci;
	private $gauge_id;
	private $actuals = array();
	private $user_id = 0;
	private $organ_id = 0;
	private $plan_id = 0;
	private $is_organisation_owner = false;
	private $integers = array(1, 3, 9, 10, 11, 12, 13, 14, 15, 16);
	private	$decimals = array(2, 4, 5, 6, 7, 8);
	
	function __construct()
	{
		$this->ci = &get_instance();	
		$this->user_id = $this->ci->session->userdata('user_id');
		$this->organ_id = $this->ci->session->userdata('organ_id');
		$this->plan_id = $this->ci->session->userdata('plan_id');
		$this->ci->load->model('Kpi_model');
		$this->ci->load->model('Graph_model');
		$this->ci->load->model('Organisation_model');
		$this->is_organsation_owner = $this->ci->Organisation_model->get_owner_permission($this->organ_id, $this->user_id	);
	}

	
	/* GENERATE GAUGES FROM GRAPHS WITH bShowGaugeOnDash*/
	public function generate_gauges($graphs)
	{
		$highcharts_gauges = array();
		foreach($graphs as $graph){
			$this->addGauge($graph->graph_id, $graph, $graph->graph_name, null)
				->setValue('width', "300px")
				->setValue('height', '300px')
				->setValue('class', "highchart_container_fixed")
				->compare_data_to_rag()
				->get_highchart_html(true, "highchart_container col-md-4 no_border");
		}
		
		$gauges = (array)$this->ci->load->get_var('gauges');
		$array = array("result" => "success", "highchart_gauge_count"=>count($gauges));
		
		foreach($gauges as $gauge)
		{
			$graph = $gauge->graph;
			$array['highcharts'][] = array(
				"max" => 100,
				"html" => $gauge->html,
				"id" => $gauge->gauge_id,  
				"options" => $gauge->options,
				"bg_color" => $gauge->bg_color,
				"percent" => $gauge->percent,
				"valuePrefix" => $gauge->valuePrefix,
				"valueSuffix" => $gauge->valueSuffix,
				"rf_from_date" => $graph->rf_from_date,
				"rf_to_date" => $graph->rf_to_date,
				"frequency" => $graph->frequency,
				"formula_string" => $gauge->formula_string
			);
		}
		
		return $array;
	}
	
	
	/* STEP 1 : INITIALIZE GAUGE */
	public function addGauge($gauge_id, $graph, $title = null, $subtitle = null)
	{
		$valueSuffix = mb_convert_encoding($graph->format_suffix, 'UTF-8', 'HTML-ENTITIES');
		$valuePrefix = mb_convert_encoding($graph->format_prefix, 'UTF-8', 'HTML-ENTITIES'); 
			
		$gauges = (array)$this->ci->load->get_var('gauges');		
		$gauges[$gauge_id] = (object)array(
							'gauge_id' => $gauge_id,
							'title' => $title,
							'type' => "gauge",
							'graph' => $graph,
							'no_users' => false,
							'valuePrefix' => $valuePrefix,
							'valueSuffix' => $valueSuffix,
							'options' => array(
								"chart" => array(
									"renderTo" => "highchart_gauge-".$gauge_id,
									"type" => 'solidgauge'
								),
								'title' => array(
									"text"=>$title
								) ,
								'subtitle' => array("text"=>$subtitle)
							)
						);
		
		$this->gauge_id = $gauge_id;
		$this->ci->load->vars(array('gauges' => $gauges));
		return $this;
	}
	
	
	/* STEP 2 : COMPARE DATA TO RAG */
	public function compare_data_to_rag()
	{
		$gauges = (array)$this->ci->load->get_var('gauges');
		$gauge = (object)$gauges[$this->gauge_id];
		$graph = (object)$gauge->graph;
		
		$bg_color = "grey";
		$total_actual = ($graph->total_actual == null) ? 0 : $graph->total_actual;
		$total_target = ($graph->total_target == null) ? 0 : $graph->total_target;
		
		/* return if no graph users or no kpi users */
		if($graph->gu_user_count == 0 || $graph->ku_user_count == 0 )
		{
			$gauge->options['title']['text'] = $gauge->options['title']['text'] . " (no user)";
			$gauges[$this->gauge_id] = $gauge; 
			$this->ci->load->vars(array('gauges' => $gauges));
			return $this;
		}
		
		/* get percent */
		$percent = $this->get_percent(
									$graph->best_direction, 
									$total_actual, 
									$total_target, 
									$graph->kpi_format_id
							);
		
		/* get gauge color */
		if(is_numeric($graph->rag_1) && is_numeric($graph->rag_2) && is_numeric($graph->rag_3) && is_numeric($graph->rag_4))
		{
			$bg_color = $this->get_gauge_color(
									$graph->total_date_count, 
									$graph->current_date_count, 
									$total_actual, 
									$graph->best_direction, 
									$graph->rag_1, 
									$graph->rag_2, 
									$graph->rag_3, 
									$graph->rag_4
						);
		}
		
		/* get formula details */
		$formula_string = $this->get_formula_string(
									$graph->total_date_count, 
									$graph->current_date_count, 
									$total_actual, 
									$total_target, 
									$graph->best_direction, 
									$graph->rag_1, 
									$graph->rag_2, 
									$graph->rag_3, 
									$graph->rag_4,
									$percent, 
									$bg_color
							);
							
		$gauge->bg_color = $bg_color;
		$gauge->sum_actuals = $total_actual;
		$gauge->sum_targets = $total_target;
		$gauge->percent = $percent;
		$gauge->formula_string = $formula_string;
		
		
		$gauges[$this->gauge_id] = $gauge; 
		$this->ci->load->vars(array('gauges' => $gauges));
		return $this;
	}
	
	
	/* STEP 2.A: Tims calculation : GENERATE PERCENT */
	public function get_percent($best_direction, $total_actual, $total_target, $kpi_format_id)
	{	
		If($best_direction == "down"){
			$value = $total_target / $total_actual; 
		}else{
			$value = $total_actual / $total_target; 
		} 
		 
		$percent = ($value == null) ? 0 : $value * 100;
		$final_percent = (in_array($kpi_format_id, $this->decimals)) ? round($percent, 2) : round($percent, 0);
		
		return $final_percent;
	}
	
	
	/* STEP 2.A: Tims calculation : GENERATE GAUGE COLOR */
	public function get_gauge_color($total_date_count, $current_date_count, $total_actual, $best_direction, $rag_1, $rag_2, $rag_3, $rag_4)
	{
		$bg_color = "grey";
		$time_elapsed = ($current_date_count / $total_date_count) * 100;
		$predicted_total = round(($total_actual / $time_elapsed ) * 100, 2);
		
		if($best_direction == "up"){
			if($predicted_total < $rag_2){
				$bg_color = "red";
			}else if($predicted_total >= $rag_2 && $predicted_total < $rag_3){
				$bg_color = "amber";
			}else if($predicted_total >= $rag_3){
				$bg_color = "green";
			}
		}else if ($best_direction == "down"){
			if($predicted_total > $rag_2){
				$bg_color = "red";
			}else if($predicted_total <= $rag_2 && $predicted_total > $rag_3){
				$bg_color = "amber";
			}else if($predicted_total <= $rag_3){
				$bg_color = "green";
			}
		}
		
		return $bg_color;
	}

	
	/* STEP 2.A: Tims calculation : GENERATE STRING OF STEPS OF CALCULATION */
	public function get_formula_string($t_date_count, $c_date_count, $total_actual, $total_target, $best_direction, $rag_1, $rag_2, $rag_3, $rag_4, $percent, $gauge_color)
	{
		
		$time_elapsed = (($c_date_count / $t_date_count) * 100);
		$predicted_total = round(($total_actual / $time_elapsed ) * 100, 2);
	
		$time_elapsed_string = round($time_elapsed, 2);
		
		$string = "";
		$string .= "<strong>RAG 1 :</strong> $rag_1<br/>";
		$string .= "<strong>RAG 2 :</strong> $rag_2<br/>";
		$string .= "<strong>RAG 3 :</strong> $rag_3<br/>";
		$string .= "<strong>RAG 4 :</strong> $rag_4<br/>";
		$string .= "<strong>Total Actual :</strong> $total_actual<br/>";
		$string .= "<strong>Expected Total Target :</strong> $total_target<br/>";
			
		if($best_direction == "up"){
			$string .= "<strong>Percent :</strong> $total_actual / $total_target = <strong>$percent% </strong><br/>";
		}else{
			$string .= "<strong>Percent :</strong><br/> $total_target / $total_actual = <strong>$percent%</strong> <br/>";
		}
		
		$string .= "<strong>Time Elapsed :</strong> (".$c_date_count." / ".$t_date_count.") * 100 = <strong> $time_elapsed_string</strong> <br/>";
		$string .= "<strong>Predicted Total :</strong> ($total_actual / $time_elapsed_string ) * 100 = <strong> $predicted_total</strong> <br/>";
		
		return $string;
	}
	
	/* STEP 3 : GENERATE HTML FOR GAUGE */
	public function get_highchart_html($is_kpi_highchart = true, $container_class = "highchart_container col-md-12"){
		$gauges = (array)$this->ci->load->get_var('gauges');
		$gauge = $gauges[$this->gauge_id];
		
		$graph = (object)$gauge->graph;
		$class = $gauge->class;
		$width = isset($gauge->width) ? "width:".$gauge->width .";" : "";
		$height = isset($gauge->height) ? "height:".$gauge->height .";" : "";
		
		$html = "<div class='$container_class'>";
			if($is_kpi_highchart == true){
				/* $html_data = array( 
					"data-toggle='modal'", 
					"data-target='#filter_gaugeModal'",
					"data-kpi_id='" . $graph->kpi_id . "'", 
					"data-gauge_id='" . $this->gauge_id . "'",
					"data-date_type='" . $graph->frequency . "'"
				);
				$html_data_string = implode($html_data, " ");
				$html .= "<a href='#' title='Filter' ".$html_data_string." class='filter_highchart' >";	
				$html .= "<i class='fa fa-pencil-square-o' style='font-size:25px;float:right;padding-top:10px;'></i>";
				$html .= "</a>"; */
				$html .= "<div class='gauge_filter_container col-md-12' style='height:30px;'>";
				/* if($graph->frequency == "daily"){ */
						$start_date = date_create($graph->rf_from_date);
						$end_date = date_create($graph->rf_to_date);
						if($graph->reset_frequency_type == "daily"){
							$date = date_format($start_date,"F d, Y");
						}else{
							$date = date_format($start_date,"M d, Y"). " - ". date_format($end_date,"M d, Y");
						}
						
						$html .= "<input type='hidden' id='gauge_date_prev-".$graph->graph_id."' value='".$graph->rf_from_date."'>";
						$html .= "<input type='hidden' id='gauge_date_next-".$graph->graph_id."' value='".$graph->rf_to_date."'>";
						$html .= "<input type='hidden' id='gauge_date_frequency-".$graph->graph_id."' value='".$graph->frequency."'>";
						$html .= "<div class='gauge_filter_PrevNext'>";	
							$html .= "<button type='button' class='hg_gauge_prev_btn' data-gauge_id='".$this->gauge_id."'><span class='glyphicon glyphicon-chevron-left'></span></button>";
							$html .= "<div class='gauge_details'><small id='gauge_date_string-".$graph->graph_id."'>".$date."</small></div>";
							$html .= "<button type='button' class='hg_gauge_next_btn' data-gauge_id='".$this->gauge_id."'><span class='glyphicon glyphicon-chevron-right'></span></button>";	
						$html .= "</div>";
					
				/* }				 */
				$html .= "</div>";
				
			}
			$html .= "<div id='highchart_gauge-".$this->gauge_id."' class='$class' style='$width $height'></div>";
			/* $html .= "<div class='col-md-12' id='tims_calculation-".$this->gauge_id."'>";
				$html .= $gauge->formula_string;
			$html .= "</div>"; */
		$html .= "</div>";
		
		$gauge->html = $html;
		$gauges[$this->gauge_id] = $gauge; 
		$this->ci->load->vars(array('gauges' => $gauges));
		
		return $this;
	}	
	
	
	/* This is to pass temporary values for gauges  */
	public function setValue($name, $value)
	{
		$gauges = (array)$this->ci->load->get_var('gauges');	
		$gauges[$this->gauge_id]->$name = $value;
		$this->ci->load->vars(array('gauges' => $gauges));
		return $this;
	}


	/* GENERATE FILTERED GAUGE */
	public function ajax_filter_gauge($graph)
	{
		if($graph)
		{
			$this->addGauge($graph->graph_id, $graph, $graph->graph_name, $graph->description)
				->compare_data_to_rag();
				
			$gauges = (array)$this->ci->load->get_var('gauges');
			
			$array = array("result" => "success");
			$percent = ($graph->frequency == 'daily') ? round($graph->reset_percent, 2)  : (($graph->percent == null) ?  0 : $graph->percent);
			foreach($gauges as $gauge)
			{
				$start_date = date_create($graph->rf_from_date);
				$end_date = date_create($graph->rf_to_date);
				if($graph->reset_frequency_type == "daily"){
					$date_string = date_format($start_date,"F d, Y");
				}else{
					$date_string = date_format($start_date,"M d, Y"). " - ". date_format($end_date,"M d, Y");
				}
				
				$array['highcharts'][] = array(
					"max" => 100,
					"html" => $gauge->html,
					"id" => $gauge->gauge_id,  
					"options" => $gauge->options,
					"bg_color" => $gauge->bg_color,
					"percent" => $gauge->percent,
					"valuePrefix" => $gauge->valuePrefix,
					"valueSuffix" => $gauge->valueSuffix,
					"rf_from_date" => $graph->rf_from_date,
					"rf_to_date" => $graph->rf_to_date,
					"frequency" => $graph->frequency,
					"formula_string" => $gauge->formula_string,
					"date_string" => $date_string
				);
			}
			
			return $array;	
		}else{
			$array = array("result" => "error");
		}
	}	
}