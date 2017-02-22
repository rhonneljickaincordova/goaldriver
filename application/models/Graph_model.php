<?php

class Graph_model extends MY_Model{
	public $_table = 'graph';
	public $primary_key = 'graph_id';
	public $frequencies = array("daily", "weekly", "monthly", "quarterly", "yearly");
	
	/************************************************************************************************
	*
	HIGHCHARTS GAUGES
	*
	************************************************************************************************/
	public function get_kpiDash_gauges_v3($gauge_permission, $organ_id, $user_id )
	{
		$gauges = array();
		$organ_id = (int)$organ_id;
		$user_id = (int)$user_id;
		
		if($gauge_permission == "kpi_dash"){
			$query = $this->db->query("CALL kpiDash_gauges_load_v3($organ_id, $user_id)");	
		}else if($gauge_permission == "kpi_dash_shared"){
			$query = $this->db->query("CALL kpiDash_sharedGauges_load_v3($organ_id, $user_id)");
		}else{
			$query = $this->db->query("CALL mainDash_gauges_load_v3($organ_id, $user_id)");
		}
		
		$query->next_result();
		if($query->num_rows() > 0){
			$user_gauges = $query->result();
			foreach($user_gauges as $row){
				$total_actual = 0;
				$total_target = 0;
				$gu_user_count = 0;
				$ku_user_count = 0;
				$default_target = ($row->target != null ) ? $row->target : 0;
				$ext = "";
				$query_parameters = array(
					$row->graph_id,
					$row->kpi_id,
					$user_id,
					$default_target,
					date('Y-m-d'),	
					date('Y-m-d'),
					0
				);
				switch($row->frequency){
					case "daily" : 
						$query_parameters = array(
							$row->graph_id,
							$row->kpi_id,
							$user_id,
							$default_target,
							$row->in_sun,
							$row->in_mon,
							$row->in_tue,
							$row->in_wed,
							$row->in_thu,
							$row->in_fri,
							$row->in_sat
						);
						$ext = "_".$row->reset_frequency_type;
					break;
					case "weekly":
						if($row->reset_frequency_type == "yearly"){
							$ext = "_yearly";
						}else if($row->reset_frequency_type == "quarterly"){
							$ext = "_quarterly";		
						}else if($row->reset_frequency_type == "monthly"){
							$ext = "_monthly";	
						}else{
							$ext = "_weekly";	
						}
					break;
					case "monthly":
						if($row->reset_frequency_type == "yearly"){
							$ext = "_yearly";
						}else if($row->reset_frequency_type == "quarterly"){
							$ext = "_quarterly";	
						}else{
							$ext = "_monthly";	
						}
					break;
					case "quarterly":
						if($row->reset_frequency_type == "yearly"){
							$ext = "_yearly";
						}else{
							$ext = "_quarterly";	
						}
					break;
					case "yearly":
						$ext = "_yearly";
					break;
				} 
				
				
				$sp_query = "CALL kpiDash_gaugeData_".$row->frequency.$ext."(".implode(",", $query_parameters).")";
				$query_data = $this->db->query($sp_query);
				/* $query_data = $this->db->query("CALL kpiDash_gaugeData_daily_yearly(70,55,57,8.00,0,2,3,4,5,6,0)"); */
				$query_data->next_result();
				if($query_data->num_rows() > 0){
					$kpi_data = $query_data->row();
					
					$total_actual = $kpi_data->sum_actuals;
					$total_target = $kpi_data->sum_targets;
					$gu_user_count = $kpi_data->gu_user_count; 
					$ku_user_count = $kpi_data->ku_user_count; 
					
					$row->rf_to_date = $kpi_data->rf_to_date; 
					$row->rf_from_date = $kpi_data->rf_from_date; 
					$row->total_date_count = $kpi_data->total_date_count; 
					$row->current_date_count = $kpi_data->current_date_count; 
					
					$row->rag_1 = ($row->rag_1 == null) ? null : ($kpi_data->total_date_count * $row->rag_1) * $gu_user_count; 
					$row->rag_2 = ($row->rag_2 == null) ? null : ($kpi_data->total_date_count * $row->rag_2) * $gu_user_count; 
					$row->rag_3 = ($row->rag_3 == null) ? null : ($kpi_data->total_date_count * $row->rag_3) * $gu_user_count; 
					$row->rag_4 = ($row->rag_4 == null) ? null : ($kpi_data->total_date_count * $row->rag_4) * $gu_user_count; 
				}
				
				$row->total_actual = $total_actual; 
				$row->total_target = $total_target; 
				$row->gu_user_count = $gu_user_count; 
				$row->ku_user_count = $ku_user_count; 
				
				$gauges[] = $row;
			}
		 }
		 
		 return $gauges;
	}
	
	public function get_kpiDash_gauge_filter_v3($gauge_permission, $organ_id, $user_id, $graph_id )
	{
		$gauges = array();
		$organ_id = (int)$organ_id;
		$user_id = (int)$user_id;
		$graph_id = (int)$graph_id;
		
		if($gauge_permission == "kpi_dash"){
			$query = $this->db->query("CALL kpiDash_gauges_filter_load_v3($organ_id, $user_id, $graph_id)");
		}else if($gauge_permission == "kpi_dash_shared"){
			$query = $this->db->query("CALL kpiDashShared_gauges_filter_load_v3($organ_id, $user_id, $graph_id)");
		}else{
			$query = $this->db->query("CALL mainDash_gauges_filter_load_v3($organ_id, $user_id, $graph_id)");
		}
		$query->next_result();
		return ($query->num_rows() > 0) ? $graph = $query->row() : false;
	}
	
	public function get_kpiDash_gaugeData_filter_v3($graph, $user_id, $in_from_date, $in_to_date)
	{
		$user_id = (int)$user_id;
		$total_actual = 0;
		$total_target = 0;
		$gu_user_count = 0;
		$ku_user_count = 0;
		$default_target = ($graph->target != null ) ? $graph->target : 0;
		
		switch($graph->frequency){
			case "weekly":
				if($graph->reset_frequency_type == "yearly"){
					$ext = "_yearly";
				}else if($graph->reset_frequency_type == "quarterly"){
					$ext = "_quarterly";		
				}else if($graph->reset_frequency_type == "monthly"){
					$ext = "_monthly";	
				}else{
					$ext = "_weekly";	
				}
			break;
			case "monthly":
				if($graph->reset_frequency_type == "yearly"){
					$ext = "_yearly";
				}else if($graph->reset_frequency_type == "quarterly"){
					$ext = "_quarterly";	
				}else{
					$ext = "_monthly";	
				}
			break;
			case "quarterly":
				if($graph->reset_frequency_type == "yearly"){
					$ext = "_yearly";
				}else{
					$ext = "_quarterly";	
				}
			break;
			case "yearly":
				$ext = "_yearly";
			break;
		} 
		
		if($graph->frequency == "daily"){
			$query_parameters = array(
				$graph->graph_id,
				$graph->kpi_id,
				$user_id,
				$default_target,
				$graph->in_sun,
				$graph->in_mon,
				$graph->in_tue,
				$graph->in_wed,
				$graph->in_thu,
				$graph->in_fri,
				$graph->in_sat,
				"'".$in_from_date."'", 
				"'".$in_to_date."'",
				"'".$graph->reset_frequency_type."'"
			);
			$query_sp = "CALL kpiDash_gaugeData_daily_filter(".implode(",", $query_parameters).")";
		}else{
			$query_parameters = array(
				$graph->graph_id,
				$graph->kpi_id,
				$user_id,
				$default_target,
				"'".$in_from_date."'", 
				"'".$in_to_date."'",
				1
			);
			$query_sp = "CALL kpiDash_gaugeData_".$graph->frequency.$ext."(".implode(",", $query_parameters).")";
		}
		/* die(json_encode(($query_sp))) ; */
		$query = $this->db->query($query_sp);
		$query->next_result();
		if($query->num_rows() > 0){
			$kpi_data = $query->row();
			
			$total_actual = $kpi_data->sum_actuals;
			$total_target = $kpi_data->sum_targets;
			$gu_user_count = $kpi_data->gu_user_count; 
			$ku_user_count = $kpi_data->ku_user_count; 
			
			
			$graph->total_date_count = $kpi_data->total_date_count; 
			$graph->current_date_count = $kpi_data->current_date_count; 
			
			$graph->rag_1 = ($graph->rag_1 == null) ? null : ($kpi_data->total_date_count * $graph->rag_1) * $gu_user_count; 
			$graph->rag_2 = ($graph->rag_2 == null) ? null : ($kpi_data->total_date_count * $graph->rag_2) * $gu_user_count; 
			$graph->rag_3 = ($graph->rag_3 == null) ? null : ($kpi_data->total_date_count * $graph->rag_3) * $gu_user_count; 
			$graph->rag_4 = ($graph->rag_4 == null) ? null : ($kpi_data->total_date_count * $graph->rag_4) * $gu_user_count; 
		}
		
		$graph->rf_from_date = $in_from_date; 
		$graph->rf_to_date = $in_to_date; 
		
		$graph->total_actual = $total_actual; 
		$graph->total_target = $total_target; 
		$graph->gu_user_count = $gu_user_count; 
		$graph->ku_user_count = $ku_user_count; 	
		
		return $graph;
	}
	
	
	public function get_kpiDash_sharedGauges($organ_id, $user_id, $graph_id = 0, $in_from_date = "", $in_to_date = "" ){
		$organ_id = (int)$organ_id;
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		
		if($graph_id == 0)
		{
			$query = $this->db->query("CALL kpiDash_sharedGauges_load($organ_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;	
		}else{
			$query = $this->db->query("CALL kpiDash_sharedSpecGauge_load($organ_id, $user_id, $graph_id, '$in_from_date', '$in_to_date')");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;	
		}
		
		return false;
	}
	
	
	public function get_mainDash_gauges_load($organ_id, $user_id, $graph_id = 0, $in_from_date = "", $in_to_date = "" ){
		$organ_id = (int)$organ_id;
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		
 		if($graph_id == 0)
		{
			$query = $this->db->query("CALL mainDash_gauges_load($organ_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;	
		}
		else
		{
			$query = $this->db->query("CALL mainDash_specGauge_filter_load($organ_id, $user_id, $graph_id, '$in_from_date', '$in_to_date')");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;	
		}
	}
	
	
	public function get_mainDash_graphs_load($organ_id, $user_id, $graph_id = 0){
		$organ_id = (int)$organ_id;
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		
 		if($graph_id == 0)
		{
			$query = $this->db->query("CALL mainDash_graphs_load($organ_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;	
		}
		else
		{
			$query = $this->db->query("CALL mainDash_specGraph_filter_load($organ_id, $user_id, $graph_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;	
		}
	}
	
	/************************************************************************************************
	*
	GRAPHS
	*
	************************************************************************************************/
	public function get_shared_graphs($organ_id, $user_id, $graph_id = 0){
		$organ_id = (int)$organ_id;
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		
		if($graph_id == 0)
		{
			$query = $this->db->query("CALL shared_graphs_load($organ_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;	
		}
		else
		{
			/* $query = $this->db->query("CALL shared_graph_specific_load($organ_id, $user_id, $graph_id, $bShowOnDash)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;	 */
		}
	}
	
	
	public function get_graphs($organ_id, $user_id, $graph_id = 0)
	{
		$organ_id = (int)$organ_id;
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		if($graph_id == 0)
		{
			$query = $this->db->query("CALL graphs_load($organ_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;	
		}
		else
		{
			$query = $this->db->query("CALL graph_specific_load($organ_id, $user_id, $graph_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;	
		}
	}
	
	
	public function graph_add($graph_name, $description, $graph_type_id, $kpi_id, $user_id, $organ_id, $show_on_dash, $show_average, $show_break_down, $show_gauge_on_dash, $reset_frequency_type )
	{
		$organ_id = (int)$organ_id;
		$user_id = (int)$user_id;
		$kpi_id = (int)$kpi_id;
		$graph_type_id = (int)$graph_type_id;
		$graph_name = $this->db->escape_str($graph_name);
		$description = $this->db->escape_str($description);
		$this->db->query("CALL graph_add('$graph_name', '$description', $graph_type_id, $kpi_id, $user_id, NOW(), $show_on_dash, $show_average, $show_break_down, $show_gauge_on_dash, '$reset_frequency_type', @graph_id )");	
		$query = $this->db->query("CALL graph_specific_load($organ_id, $user_id, @graph_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;	
	}
	
	
	public function graph_update($graph_name, $description, $graph_type_id, $kpi_id, $user_id, $organ_id, $graph_id, $show_on_dash, $show_average, $show_break_down, $show_gauge_on_dash, $kpi_frequency, $reset_rf_type )
	{
		if(!in_array($reset_rf_type, $this->frequencies)){
			return false;
		}
		$kpi_id = (int)$kpi_id;
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$graph_type_id = (int)$graph_type_id;
		$show_on_dash = (int)$show_on_dash;
		$show_average = (int)$show_average;
		$show_break_down = (int)$show_break_down;
		$show_gauge_on_dash = (int)$show_gauge_on_dash;
		
		$graph_name = $this->db->escape_str($graph_name);
		$description = $this->db->escape_str($description);
		$update_data = array(
				"graph_name" => $graph_name,
				"description" => $description,
				"graph_type_id" => $graph_type_id,
				"bShowOnDash" => $show_on_dash,
				"bShowAverage" => $show_average,
				"bShowBreakdown" => $show_break_down,
				"bShowGaugeOnDash" => $show_gauge_on_dash,
				"reset_frequency_type" => $reset_rf_type
			);
		$this->db->where('graph_id', $graph_id);
		
		$update = $this->db->update($this->_table, $update_data); 
		$kpi_update = $this->graph_kpi_update($graph_id, $kpi_id);
		if($update == true && $kpi_update == true){
			$query = $this->db->query("CALL graph_specific_load($organ_id, $user_id, $graph_id)");	
			$query->next_result();
			return ($query->num_rows() > 0) ? $query->row() : false;				
		}else{
			return false;
		}
		
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
	
	
	public function graph_delete($graph_id)
	{
		$graph_id = (int)$graph_id;
		$this->db->query("CALL graph_delete($graph_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	public function get_graph_types($graph_type_id = 0)
	{
		$graph_type_id = (int)$graph_type_id;
		if($graph_type_id == 0){
			$this->db->where("disabled", 0);
			$query = $this->db->get("graph_types");
	
			return ($query->num_rows() > 0) ? $query->result() : false;
 		}else{
			$this->db->where("graph_type_id", $graph_type_id);
			$query = $this->db->get("graph_types");
			
			return ($query->num_rows() > 0) ? $query->row() : false;
		}
	}
	
	
	/************************************************************************************************
	*
	GRAPH KPI
	*
	************************************************************************************************/
	public function graph_kpi_add($graph_id, $kpi_id)
	{
		$kpi_id = (int)$kpi_id;
		$graph_id = (int)$graph_id;
		$this->db->query("CALL graph_add($graph_id, $kpi_id, @graph_kpi_id )");	
		$query = $this->db->query("select * from graph_kpi where graph_kpi_id = @graph_kpi_id");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;	
	}
	
	
	public function graph_kpi_update($graph_id, $kpi_id, $graph_kpi_id = 0)
	{
		$kpi_id = (int)$kpi_id;
		$graph_id = (int)$graph_id;
		$graph_kpi_id = (int)$graph_kpi_id;
		$update_data = array(
				"kpi_id" => $kpi_id
			);
		if($graph_kpi_id != 0){
			$this->db->where('graph_id', $graph_id);
		}
			
		$this->db->where('graph_id', $graph_id);
		$update = $this->db->update("graph_kpi", $update_data); 
		if($update){
			return true;
		}else{
			return false;
		}
	}
	
	/************************************************************************************************
	*
	GRAPH USERS
	*
	************************************************************************************************/
	public function get_graph_users($graph_id, $user_id = 0){
		$graph_id = (int)$graph_id;
		$user_id = (int)$user_id;
		if($user_id == 0){
			$query = $this->db->query("CALL graph_users_load($graph_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;
		}else{
			$query = $this->db->query("CALL graph_user_specific_load($graph_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;
		}
	}
	
	
	public function graph_users_add($organ_id, $plan_id, $graph_id, $kpi_id, $users)
	{
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$graph_id = (int)$graph_id;
		$kpi_id = (int)$kpi_id;
		$delete_users = $this->graph_users_delete($graph_id); 
		
		if($delete_users){
			$users_add = array('added'=> array(), "failed_to_add" => array());
			foreach($users as $user){
				$user_id = (int)$user['id'];
				$is_kpi_user = $this->Kpi_model->get_kpis_as_member($organ_id, $plan_id, $user_id, $kpi_id);
				if($is_kpi_user){
					$this->db->query("CALL graph_user_add($graph_id, $user_id, @graph_user_id)");	
					$query = $this->db->query("CALL graph_user_specific_load($graph_id, $user_id)");	
					$query->next_result();
					$add = ($query->num_rows() > 0) ? $query->row() : false;	
				}else{
					$add = false;
				}
				
				if($add){
					 $users_add['added'][] =  $user_id; 
				}else{
					 $users_add['failed_to_add'][] =  $user_id;
				}
			}
			return $users_add;
		}else{
			return false;
		}
	}
	
	
	public function graph_users_delete($graph_id)
	{
		$graph_id = (int)$graph_id;
		$current_users = $this->get_graph_users($graph_id);
		
		if($current_users != false){
			$delete = $this->db->query("CALL graph_users_delete($graph_id)");	
			if($this->db->affected_rows() > 0){
				return true; 
			}else{
				return false;
			}
		}else{
			return true; 
		}
	}
	
	/************************************************************************************************
	*
	GRAPH SETTINGS : 
	*review to delete if not use
	************************************************************************************************/
	public function get_graph_settings($graph_id)
	{
		$graph_id = (int)$graph_id;
		$query = $this->db->query("CALL graph_settings_load($graph_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->row() : false;
	}
	
	
	public function graph_settings_add($graph_id, $kpi_id)
	{
		$graph_id = (int)$graph_id;
		$kpi_id = (int)$kpi_id;
		$this->db->query("CALL graph_settings_add($graph_id, $kpi_id, @graph_setting_id)");	
		return $this->get_graph_settings($graph_id);	
	}
	
	
	/************************************************************************************************
	*
	GRAPH PERMISSION
	*
	************************************************************************************************/
	public function get_owner_permission($graph_id, $user_id)
	{
		$user_id = (int)$user_id;
		$graph_id = (int)$graph_id;
		$this->db->where(array( "graph_id" => $graph_id, "entered_by"=>$user_id ));
		$query = $this->db->get($this->_table);
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
	
	/************************************************************************************************
	*
	GRAPH SHARED USERS
	*
	************************************************************************************************/
	public function get_shared_users($organ_id, $graph_id)
	{
		$organ_id = (int)$organ_id;
		$graph_id = (int)$graph_id;
		$query = $this->db->query("CALL graph_shared_users_load($organ_id, $graph_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	
	public function shared_users_delete($graph_id)
	{
		$graph_id = (int)$graph_id;
		$this->db->query("CALL graph_shared_users_delete($graph_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	public function shared_user_add($graph_id, $user_id)
	{
		$user_id = (int)$user_id;
		$graph_id = (int)$graph_id;
		
		$this->db->query("CALL graph_shared_user_add($graph_id, $user_id, @graph_shared_user_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;		
	}
	
	
	public function shared_users_add($graph_id, $shared_users, $organ_id)
	{
		$graph_id = (int)$graph_id;
		$organ_id = (int)$organ_id;
		$this->Graph_model->shared_users_delete($graph_id);
		if(!empty($shared_users)){
			foreach($shared_users as $shared_user){
				$user_id = (int)$shared_user['id'];
				$is_organisation_member = $this->Organisationusers_model->organisation_member_exists($user_id, $organ_id);
		
				if($is_organisation_member){
					$this->shared_user_add($graph_id, $user_id);
				}
			}
		}
	}
	
	
	public function graph_kpi_users_checker($kpi_id, $users, $user_id)
	{
		$final_users = array();
		if(!empty($users)){
			foreach($users as $user_id){
				$final_users[] = (int)$user_id;	
			}
		}
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$string_users = implode(',', $final_users);
		
		$query = $this->db->query("CALL graph_kpi_users_checker($kpi_id, '$string_users', $user_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
	
	
	
	/************************************************************************************************
	*
	HIGHCHARTS GRAPH DATA
	*is_byUser = 0  :  This functions is queries series of dates and checks if has kpi data from all users 
	*is_byUser = 1  :  This functions is queries series of dates and checks if has kpi data from specific user 
	*
	************************************************************************************************/
	public function get_graphDates_withData($frequency, $graph_id, $kpi_id, $user_id, $default_target, $interval, $from_date, $to_date, $user_count, $is_byUser = 0, $is_filter = 0, $unselected_count = 0, $in_sun = 0, $in_mon = 0, $in_tue = 0, $in_wed = 0, $in_thu = 0, $in_fri = 0, $in_sat = 0)
	{
		$default_dates = array();
		$in_sun = (int)$in_sun;
		$in_mon = (int)$in_mon;
		$in_tue = (int)$in_tue; 
		$in_wed = (int)$in_wed; 
		$in_thu = (int)$in_thu; 
		$in_fri = (int)$in_fri;
		$in_sat = (int)$in_sat;
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$graph_id = (int)$graph_id;
		$interval = (int)$interval;
		$is_filter = (int)$is_filter;
		$user_count = (int)$user_count;
		$default_target = (int)$default_target;
		$is_byUser = (int)$is_byUser;
		$unselected_count = (int)$unselected_count;
		$valid_from_date = $this->is_valid_date($from_date);
		$valid_to_date = $this->is_valid_date($to_date);
		if(($from_date != "" && $valid_from_date == false) || ($to_date != "" && $valid_to_date == false)){
			return false;
		}
		switch($frequency)
		{
			case "daily": 	
				$query = $this->db->query("CALL graphData_daily($graph_id, $kpi_id, $user_id, $default_target, $in_sun, $in_mon, $in_tue, $in_wed, $in_thu, $in_fri, $in_sat, $is_filter, $interval, '".$from_date."', '".$to_date."', $user_count, $unselected_count, $is_byUser)");	
				break;
			case "weekly": 
				$query = $this->db->query("CALL graphData_weekly($graph_id, $kpi_id, $user_id, $default_target, $is_filter, $interval, '".$from_date."', '".$to_date."', $user_count, $is_byUser)");	
				break;
			case "monthly": 
				$query = $this->db->query("CALL graphData_monthly($graph_id, $kpi_id, $user_id, $default_target, $is_filter, $interval, '".$from_date."', '".$to_date."', $user_count, $is_byUser)");	
				break;
			case "quarterly": 
				$query = $this->db->query("CALL graphData_quarterly($graph_id, $kpi_id, $user_id, $default_target, $is_filter, $interval, '".$from_date."', '".$to_date."', $user_count, $is_byUser)");	
				break;
			default : 
				$query = $this->db->query("CALL graphData_yearly($graph_id, $kpi_id, $user_id, $default_target, $is_filter, $interval, '".$from_date."', '".$to_date."', $user_count, $is_byUser)");	
			break;
		}
		
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;	
	}	
	
	
	/************************************************************************************************
	*
	HIGHCHARTS GRAPH  DATES
	This function is used by GETTING DATES FOR GRAPH HIGHCHARTS DAILY
	*
	************************************************************************************************/
	public function graph_dates_daily($interval = 1, $unselected_count = 0, $in_sun, $in_mon, $in_tue, $in_wed, $in_thu, $in_fri, $in_sat, $is_filter = 0, $from_date = '0000-00-00', $to_date = '0000-00-00')
	{
		$in_sun = (int)$in_sun;
		$in_mon = (int)$in_mon;
		$in_tue = (int)$in_tue; 
		$in_wed = (int)$in_wed; 
		$in_thu = (int)$in_thu; 
		$in_fri = (int)$in_fri;
		$in_sat = (int)$in_sat;
		$is_filter = (int)$is_filter;
		$interval = (int)$interval;
		$unselected_count = (int)$unselected_count;
		$default_dates = array();
		$prev_num = (int)$prev_num;
		$next_num = (int)$next_num;
		$valid_from_date = $this->is_valid_date($from_date);
		$valid_to_date = $this->is_valid_date($to_date);
		if(($from_date != "" && $valid_from_date == false) || ($to_date != "" && $valid_to_date == false)){
			return false;
		}
		
		$query = $this->db->query("CALL graph_dates_daily($in_sun, $in_mon, $in_tue, $in_wed, $in_thu, $in_fri, $in_sat,  $is_filter, $interval, '".$from_date."', '".$to_date."', $unselected_count)");	
				
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
				$default_dates['actual_dates'][] = $date;
			}
		}
		
		$default_dates['dates'] = $dates;
		
		return $default_dates;
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