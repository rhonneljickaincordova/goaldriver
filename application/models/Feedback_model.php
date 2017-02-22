<?php

class Feedback_model extends MY_Model{


	public function save_feedback($data)
	{
		if(!empty($data))
		{
			$response = $this->db->insert('feedback', $data);
			$latest_id = $this->db->insert_id();
			
		}
		return $latest_id;
	}

	public function get_status($url=''){
		
		if($this->session->userdata('user_id') != "")
		{
			$query = $this->db->query("SELECT status_id FROM feedback WHERE  user_id = ". $this->session->userdata('user_id') ." ORDER BY feedback_id DESC LIMIT 1");

	    	return $query->result();   
		}
	}	
}