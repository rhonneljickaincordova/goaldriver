<?php

class Subsection_comment_model extends MY_Model{
	public $_table = 'subsection_comment';
	public $primary_key = 'id';
	public $before_create = array( 'timestamps' );

	protected function timestamps($subsection)
    {
        $subsection['entered'] = date('Y-m-d H:i:s');
        return $subsection;
    }

    function get_all_comments($plan_id){
    	$query = $this->db->query("SELECT s.section_id, s.title as section_title, ss.subsection_id, ss.title as subsection_title, sc.*, u.first_name, u.last_name, c.chapter_id, c.title as chapter_title FROM section s, subsection ss, subsection_comment sc, users u, chapter c WHERE sc.subsection_id = ss.subsection_id AND ss.section_id = s.section_id AND sc.user_id = u.user_id AND c.chapter_id = s.chapter_id AND ss.plan_id = {$plan_id} ORDER BY sc.entered DESC");
    	return ($query->num_rows() > 0) ? $query->result_array() : array();
    }
}
