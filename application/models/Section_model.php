<?php

class Section_model extends MY_Model{
	
	public $_table = 'section';
	public $primary_key = 'section_id';
	public $before_create = array( 'timestamps' );

	protected function timestamps($plan)
    {
        $plan['entered'] = $plan['updated'] = date('Y-m-d H:i:s');
        return $plan;
    }

    function get_ordered_section($chapter_id){
        $this->db->order_by("position", "asc"); 
    	$query = $this->db->get_where('section', array('chapter_id' => $chapter_id, 'plan_id' => $this->session->userdata('plan_id')), null, null);
    	return $query->result();
    }

    function get_last_position(){
 		$query = $this->db->query("SELECT max(position) as pos FROM section");
 		$row = $query->row();
 		return $row->pos+1;
 	}

    function nav_button($dir, $position, $chapter_id){
        $where = '';
        $btn_text = '';
        $icon = '';
        if($dir == 'next'){
            $where = "position > {$position} AND chapter_id = {$chapter_id} ORDER BY position LIMIT 1";
            $btn_text = 'Next section';
            $icon = '<i class="fa fa-chevron-right"></i>';
        }
        elseif($dir == 'prev'){
            $where = "position < {$position} AND chapter_id = {$chapter_id} ORDER BY position DESC LIMIT 1";
            $btn_text = 'Previous section';
            $icon = '<i class="fa fa-chevron-left"></i>';
        }

        $link = site_url('plan/chapter/'.encrypt($chapter_id));
        $query = $this->db->query("SELECT section_id FROM section WHERE $where");
        if($query->num_rows() > 0){
            $row = $query->row();    
            $link = site_url('plan/chapter/'.encrypt($chapter_id).'/'.encrypt($row->section_id));

            return '<a class="btn btn-default btn-sm" href="'.$link.'">'.$icon.' '.$btn_text.'</a>';
        }
        
        
    }

    
    
}