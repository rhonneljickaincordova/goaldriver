<?php

class Subsection_model extends MY_Model{
	
	public $_table = 'subsection';
	public $primary_key = 'subsection_id';
	public $before_create = array( 'timestamps' );

	protected function timestamps($subsection)
    {
        $subsection['entered'] = $subsection['updated'] = date('Y-m-d H:i:s');
        return $subsection;
    }

    function copy_cruncher_plans($new_plan_id, $base_plan=null){
    	$base_plan_id = 2; // crunchers plan

        if(! is_null($base_plan))
            $base_plan_id = $base_plan;
    	
    	$copy_chapter = $this->db->query("SELECT * FROM chapter WHERE plan_id = {$base_plan_id}");
		if($copy_chapter->num_rows() > 0)
    	{
    		foreach ($copy_chapter->result() as $chapter) {
    			// copy chapter
	    		$this->db->query("INSERT INTO chapter (plan_id, title, description, position, entered, updated)
					VALUES ( {$new_plan_id}, '{$chapter->title}' , '{$chapter->description}' ,'{$chapter->position}', NOW() , NOW())");
	    		$new_chapter_id = $this->db->insert_id();

	    		$this->copy_sections($new_plan_id, $chapter->chapter_id, $new_chapter_id);
			}
		}
    } 


    function copy_sections($new_plan_id, $old_chapter_id, $new_chapter_id){
    	$copy_section = $this->db->query("SELECT * FROM section WHERE chapter_id = {$old_chapter_id}");
		if($copy_section->num_rows() > 0)
		{
			foreach ($copy_section->result() as $section) {
				// copy section
    			$this->db->query("INSERT INTO section (plan_id, chapter_id, title, position, instructions, example, entered, updated)
								VALUES( {$new_plan_id}, {$new_chapter_id}, '{$section->title}', {$section->position}, '{$section->instructions}', '{$section->example}', NOW(), NOW() )");
    			$new_section_id = $this->db->insert_id();

    			$this->copy_subsections($new_plan_id, $section->section_id, $new_section_id);
			}
		}

    	
    }

    function copy_subsections($new_plan_id, $old_section_id, $new_section_id){
    	$copy_subsection = $this->db->query("SELECT * FROM subsection WHERE section_id = {$old_section_id}");
		if($copy_subsection->num_rows() > 0)
		{
			foreach ($copy_subsection->result() as $subsection) {
				// copy subsection
				$this->db->query("INSERT INTO subsection (plan_id, section_id, title, type, instructions, example, chart_type, entered, updated)
					VALUES( {$new_plan_id}, {$new_section_id}, '{$subsection->title}', '{$subsection->type}', '{$subsection->instructions}', '{$subsection->example}', {$subsection->chart_type}, NOW(), NOW() )");
			}
		}
		
    }

    function get_subsections($section_id)
    {
    	$query = $this->db->query("SELECT * FROM subsection WHERE section_id = {$section_id} ORDER BY position");
    	return $query->result();
    }

    function next_button($chapter_id, $section_id, $sub_section_id)
    {
    	$query = $this->db->query("SELECT * FROM ".$this->_table." WHERE section_id = {$section_id} AND subsection_id = (SELECT MIN(subsection_id) FROM ".$this->_table." WHERE subsection_id > {$sub_section_id})");
    	$btn_text = 'Next Sub section';
        $icon = '<i class="fa fa-chevron-right"></i>';
    	if($query->num_rows() > 0){
            $row = $query->row();    
            $link = site_url('plan/chapter/'.encrypt($chapter_id).'/'.encrypt($row->section_id).'/'.encrypt($row->subsection_id));

            return '<a class="btn btn-default btn-sm" href="'.$link.'">'.$icon.' '.$btn_text.'</a>';
        }
    }

    function prev_button($chapter_id, $section_id, $sub_section_id)
    {
    	$query = $this->db->query("SELECT * FROM ".$this->_table." WHERE section_id = {$section_id} AND subsection_id = (SELECT MAX(subsection_id) FROM ".$this->_table." WHERE subsection_id < {$sub_section_id})");
    	$btn_text = 'Previous Sub section';
        $icon = '<i class="fa fa-chevron-left"></i>';
    	if($query->num_rows() > 0){
            $row = $query->row();    
            $link = site_url('plan/chapter/'.encrypt($chapter_id).'/'.encrypt($row->section_id).'/'.encrypt($row->subsection_id));

            return '<a class="btn btn-default btn-sm" href="'.$link.'">'.$icon.' '.$btn_text.'</a>';
        }
    }

    function subsection_move($subsection_id, $old_position, $direction)
    {   
        $query = $this->db->query("CALL subsection_move($subsection_id, $old_position, '$direction')");  
        $query->next_result();

        return ($query->num_rows() > 0) ?  $query->row() : false;
    }

    function section_move($section_id, $old_position, $direction)
    {   
        $query = $this->db->query("CALL section_move($section_id, $old_position, '$direction')");  
        $query->next_result();

        return ($query->num_rows() > 0) ?  $query->row() : false;
    }

    function subsection_reorder($section_id)
    {
        // query to get all id's, ordered by priority ASC.
        $sections = $this->db->query("SELECT subsection_id FROM subsection WHERE section_id = {$section_id} ORDER BY position ASC");
        // iterate thru all items, and give each a new priority

        if($sections->num_rows() > 0)
        {
            $position = 0;
            foreach ($sections->result() as $item) {
                $this->db->query("UPDATE subsection SET position = ".$position." WHERE subsection_id = ".$item->subsection_id);
                $position++;
            }

            return $sections->result();
        }
        
    }

    // get all subsections order by position
    function get_subsections_by_position($section_id=null){
        if($section_id != ''){
            $query = $this->db->query("SELECT * FROM subsection WHERE section_id = {$section_id} ORDER BY position ASC");
            return $query->result();    
        }
        
    }

}
	

