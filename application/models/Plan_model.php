<?php

class Plan_model extends MY_Model{
	
	public $_table = 'plan';
	public $primary_key = 'plan_id';
	public $before_create = array( 'timestamps' );

	protected function timestamps($plan)
    {
        $plan['entered'] = $plan['updated'] = date('Y-m-d H:i:s');
        return $plan;
    }


    public function get_new_plan($owner_id){
        $query = $this->db->query("SELECT plan_id FROM plan WHERE owner_id = " .$owner_id);
        return $query->row()->plan_id; 
    }

    public function get_chart_types(){
        $query = $this->db->query("SELECT * FROM chart_type");

        return $query->result();
    }

    public function set_chart_type($chart_id, $section_id)
    {
        $this->db->where('subsection_id', $section_id);
        if($this->db->update('subsection', array('chart_type' => $chart_id)))
        {
            return true;
        }
        return false;
    }

    function validate_chapter_access($chapter_id, $plan_id)
    {
        $query = $this->db->query("SELECT COUNT(*) FROM chapter WHERE plan_id = {$plan_id} AND chapter_id = {$chapter_id} ");
        return $query->num_rows();
    }

    function get_organ_id_by_plan($plan_id)
    {
        $sql = "SELECT organ_id FROM ".$this->_table." WHERE plan_id = ?";
        $query = $this->db->query($sql, array($plan_id));

        return ($query->num_rows() > 0) ? $query->row()->organ_id : NULL;
    }
    
    
}