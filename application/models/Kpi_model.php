<?php

class Kpi_model extends MY_Model{

	public $_table = 'kpi';
	public $primary_key = 'kpi_id';
	public $best_directions = array("up", "down");
	public $agg_types = array("sum_total", "average");
	public $frequencies = array("daily", "weekly", "monthly", "quarterly", "yearly");

	public function get_kpis($organ_id, $plan_id, $owner_id, $kpi_id = 0)
	{
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$owner_id = (int)$owner_id;
		$kpi_id = (int)$kpi_id;
		
		if($kpi_id == 0)
		{
			$query = $this->db->query("CALL kpis_load_as_admin($organ_id, $plan_id, $owner_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;
		}
		else
		{
			$query = $this->db->query("CALL kpi_specific_load($organ_id, $plan_id, $owner_id, $kpi_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;		
		}
		
	}
	
	public function get_kpis_as_member($organ_id, $plan_id, $user_id, $kpi_id = 0){
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$user_id = (int)$user_id;
		$kpi_id = (int)$kpi_id;
		
		if($kpi_id == 0)
		{
			$query = $this->db->query("CALL kpis_load_as_member($organ_id, $plan_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;
		}
		else
		{
			$query = $this->db->query("CALL kpi_specific_load_as_member($organ_id, $plan_id, $user_id, $kpi_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;		
		}
	}
	
	public function get_kpis_count($user_id, $organ_id, $plan_id)
	{
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$user_id = (int)$user_id;
		$array = array(	"daily" => 0, "weekly" => 0, "monthly" => 0, "quarterly" => 0, "yearly" => 0 );
		
		$query = $this->db->query("CALL kpis_count($user_id, $organ_id, $plan_id)");
		$query->next_result();
		
		if($query->num_rows() > 0)
		{	
			$rows = $query->result();
			foreach($rows as $row)
			{
				$array[$row->frequency] = $row->count;	
			}
		}
		
		return $array;	
	}
	
	
	public function kpi_add($organ_id, $plan_id, $owner_id, $name, $icon, $description, $frequency, $format, $best_direction, $target, $agg_type, $rag_1, $rag_2, $rag_3, $rag_4, $current_trend, $rollup_to_parent, $parent_kpi_id )
	{
		$format = (int)$format;
		$owner_id = (int)$owner_id;
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		
		if(!in_array($agg_type, $this->agg_types)){return false;}
		if(!in_array($frequency, $this->frequencies)){return false;}
		if(!in_array($best_direction, $this->best_directions)){return false;}
		
		$target_string = ($target == null || !is_numeric($target)) ? "null" : $target;
		$rag1_string = ($rag_1 == null) ? "null" : $rag_1;
		$rag2_string = ($rag_2 == null) ? "null" : $rag_2;
		$rag3_string = ($rag_3 == null) ? "null" : $rag_3;
		$rag4_string = ($rag_4 == null) ? "null" : $rag_4;
		$name = $this->db->escape_str($name);
		$description = $this->db->escape_str($description);
		
		$query_string = "CALL kpi_add($organ_id, $plan_id, $owner_id, '$name', '$icon', '$description', '$frequency', $format, '$best_direction', $target_string, $rag1_string, $rag2_string, $rag3_string, $rag4_string, '$agg_type', '$current_trend', $rollup_to_parent, $parent_kpi_id, @kpi_id)";
		
		$this->db->query($query_string);	
		
		$query = $this->db->query("Select 
			kpi.*,  kpi_formats.name as format 
			From 
				".$this->_table.", kpi_formats 
			WHERE 
				kpi_id = @kpi_id AND kpi.kpi_format_id = kpi_formats.kpi_format_id");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;			
	}
	
	
	
	public function kpi_delete($user_id, $kpi_id, $organ_id, $plan_id)
	{
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$this->db->query("CALL kpi_delete($user_id, $kpi_id, $organ_id, $plan_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	
	public function get_kpi_formats($kpi_format_id = 0)
	{
		$kpi_format_id = (int)$kpi_format_id;
		
		if($kpi_format_id == 0)
		{
			$this->db->order_by("kpi_format_id", "asc");
			$query = $this->db->get("kpi_formats");
		
			return ($query->num_rows() > 0) ? $query->result() : false;
			
		}
		else
		{
			$query = $this->db->get_where("kpi_formats", array("kpi_format_id"=>$kpi_format_id));
			return ($query->num_rows() > 0) ? $query->row() : false;
		}
	}
	
	
	/*permission*/
	public function get_owner_permission($kpi_id, $organ_id, $user_id)
	{
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$query = $this->db->query("CALL kpi_owner($organ_id, $kpi_id, $user_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}	
	
	
	private function get_day_num($day){
		$array = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		
		$x = 0;
		foreach($array as $day_name){
			if($day_name == $day){
				return $x+1;
			}
			$x++;
		}
	}
	
	
	public function get_assigned_kpis($organ_id, $user_id, $frequency)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		if(!in_array($frequency, $this->frequencies)){return false;}
		
		$query = $this->db->query("CALL kpi_assigned_byFrequency_load($organ_id, $user_id, '".$frequency."')");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;	
	}
	
	
}

