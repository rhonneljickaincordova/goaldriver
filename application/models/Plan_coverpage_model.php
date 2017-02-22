<?php

class Plan_coverpage_model extends MY_model{
	public $_table = 'plan_coverpage';
	public $primary_key = 'id';

	public $before_create = array( 'timestamps' );

	protected function timestamps($plan)
    {
        $plan['entered'] = $plan['updated'] = date('Y-m-d H:i:s');
        return $plan;
    }

    function is_exists($plan_id){
    	$query = $this->db->get_where($this->_table, array('plan_id' => $plan_id), NULL, NULL);
    	return $query->num_rows();
    }

    function get_cover_page_info($plan_id){
        $query = $this->db->query("SELECT pc.*, p.is_coverpage FROM plan_coverpage pc INNER JOIN plan p WHERE pc.plan_id = p.plan_id AND pc.plan_id = $plan_id");
        return ($query->num_rows() > 0) ? $query->result() : NULL;
    }
}