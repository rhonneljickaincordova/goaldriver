<?php

class Task_users_model extends CI_Model{

	public function task_users_add($organ_id, $task_id, $user_id, $users)
	{
		$organ_id = (int)$organ_id;
		$users_add = array('added'=> array(), "failed_to_add" => array());
		
		if(!empty($users))
		{
			foreach($users as $user){
				$user = (int)$user;
				
				$this->db->query("CALL task_user_add($task_id, $user, $user_id, @task_user_id)");	
				$query = $this->db->query("CALL task_user_specific_load($task_id, $user_id)");	
				$query->next_result();
				$add = ($query->num_rows() > 0) ? $query->row() : false;	
				
				if($add){
					 $users_add['added'][] =  $user_id; 
				}else{
					 $users_add['failed_to_add'][] =  $user_id;
				}
			}
			return $users_add;
		}
		
		return true;
	}
	
	public function task_users_edit($organ_id, $task_id, $user_id, $users)
	{
		$organ_id = (int)$organ_id;
		$task_id = (int)$task_id;
		$user_id = (int)$user_id;
		$users_add = array('added'=> array(), "failed_to_add" => array());
		
		$check_if_has_users = $this->get_task_users($task_id);
		if($check_if_has_users){
			$delete_users = $this->task_users_delete($task_id);
		}
		
		if(!empty($users))
		{
			foreach($users as $user){
				$user = (int)$user;
				
				$this->db->query("CALL task_user_add($task_id, $user, $user_id, @task_user_id)");	
				$query = $this->db->query("CALL task_user_specific_load($task_id, $user_id)");	
				$query->next_result();
				$add = ($query->num_rows() > 0) ? $query->row() : false;	
				
				if($add){
					 $users_add['added'][] =  $user_id; 
				}else{
					 $users_add['failed_to_add'][] =  $user_id;
				}
			}
			return $users_add;
		}
		
		return true;
	}
	
	
	public function task_users_delete($task_id)
	{
		$task_id = (int)$task_id;
		$delete = $this->db->query("CALL task_users_delete($task_id)");	
		return ($this->db->affected_rows() > 0) ?  true : false;
	}

	public function get_task_users($task_id, $user_id = 0){
		$task_id = (int)$task_id;
		$user_id = (int)$user_id;
		if($user_id == 0){
			$query = $this->db->query("CALL task_users_load($task_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->result() : false;
		}else{
			$query = $this->db->query("CALL task_user_specific_load($task_id, $user_id)");
			$query->next_result();
			return ($query->num_rows() > 0) ?  $query->row() : false;
		}
	}
	
}
?>