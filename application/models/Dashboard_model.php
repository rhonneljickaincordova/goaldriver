<?php

class Dashboard_model extends CI_Model{

	public function save_shared_task($data)
	{

		if(!empty($data))
		{
			$response = $this->db->insert('task_shared', $data);
			$latest_id = $this->db->insert_id();
			
		}
		return $latest_id;
		
	}

	public function get_shared_task($shared_from){
		

			$query = $this->db->query("SELECT * FROM task_shared WHERE shared_from = ' " . $shared_from . " ' ORDER BY task_shared_id DESC LIMIT 1");
	        return $query->result();


    
	}


}?>