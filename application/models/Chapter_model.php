<?php

class Chapter_model extends MY_Model{
	
	public $_table = 'chapter';
	public $primary_key = 'chapter_id';
	public $before_create = array( 'timestamps' );

	protected function timestamps($plan)
    {
        $plan['entered'] = $plan['updated'] = date('Y-m-d H:i:s');
        return $plan;
    }

    function get_ordered_chapters($plan_id){
        $this->db->order_by("position", "asc"); 
    	$query = $this->db->get_where('chapter', array('plan_id' => $plan_id), null, null);
    	return $query->result();
    }
 	
 	function get_last_position(){
 		$query = $this->db->query("SELECT max(position) as pos FROM chapter");
 		$row = $query->row();
 		return $row->pos+1;
 	}

    function get_first_position($plan_id){
        $query = $this->db->query("SELECT chapter_id FROM chapter WHERE position = 0 AND plan_id = {$plan_id}");
        return ($query->num_rows() > 0) ? $query->row() : NULL;
    }

    function nav_button($dir, $position, $plan_id){

        $where = '';
        $btn_text = '';
        $icon = '';
        if($dir == 'next'){
            $where = "position > {$position} AND plan_id = {$plan_id} ORDER BY position LIMIT 1";
            $btn_text = 'Next chapter';
            $icon = '<i class="fa fa-chevron-right"></i>';
        }
        elseif($dir == 'prev'){
            $where = "position < {$position} AND plan_id = {$plan_id} ORDER BY position DESC LIMIT 1";
            $btn_text = 'Previous chapter';
            $icon = '<i class="fa fa-chevron-left"></i>';
        }
        // die("SELECT chapter_id FROM chapter WHERE $where");

        $link = site_url('plan');
        $query = $this->db->query("SELECT chapter_id, position FROM chapter WHERE $where");
        if($query->num_rows() > 0){
            $row = $query->row();
            $link = site_url('plan/chapter/'.encrypt($row->chapter_id));

            return '<a class="btn btn-default btn-sm" href="'.$link.'">'.$icon.' '.$btn_text.'</a>';
        }
    }

    function get_plan_id_by_chapter($chapter_id)
    {
        $sql = "SELECT plan_id FROM chapter WHERE chapter_id = ?";
        $query = $this->db->query($sql, array($chapter_id));

        return ($query->num_rows() > 0) ? $query->row()->plan_id : NULL;
    }

}