<?php

class Task_model extends MY_Model{

	public $_table = 'tasks';
	public $primary_key = 'id';

	public function get_task($task_id, $organ_id)
	{
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		
		$query = $this->db->query("CALL task_specific_load($task_id, $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->row() : false;
	}

	
	public function get_all_task($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		
		$query = $this->db->query("CALL tasks_organ_load($user_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}

	public function get_monthly_tasks($user_id, $organ_id, $month, $year)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$month = (int)$month;
		$year = (int)$year;
		
		$query = $this->db->query("CALL tasks_organ_monthly_load($organ_id, $month, $year)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}
	
	public function get_all_task_by_user($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$query = $this->db->query("CALL tasks_organ_load_filtered_by_user($user_id, $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function task_add($u_id, $org_id, $participants, $owner_id, $name, $desc, $due_date, $start_date, $status, $priority, $plan_id, $m_id, $ntd_id)
	{
		$user_id = (int)$u_id;
		$organ_id = (int)$org_id;
		$plan_id = (int)$plan_id;
		$owner_id = (int)$owner_id;
		$ntd_id = (int)$ntd_id;
		$m_id = (int)$m_id;
		$status = (int)$status;
		$priority = (int)$priority;
		$showDash = (int)$showDash;
		$entered_by = (int)$entered_by;
		$name = $this->db->escape_str($name);
		$description = $this->db->escape_str($desc);
		
		$params = "$user_id, $organ_id, '$participants', $owner_id, '$name', '$desc', '$due_date', '$start_date', $status, $priority, $plan_id, $m_id, $ntd_id, @out_task_id";
		$add_string = "CALL task_add(".$params.")";
		$this->db->query($add_string);	
		$query = $this->db->query("CALL task_specific_load(@out_task_id, $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;		
	}
	
	
	public function task_update($user_id, $task_id, $organ_id, $participants, $owner_id, $name, $description, $due_date, $start_date, $status, $priority, $m_id, $updated_by)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		$owner_id = (int)$owner_id;
		$status = (int)$status;
		$name = $this->db->escape_str($name);
		$description = $this->db->escape_str($description);
		$m_id = (int)$m_id;
		$updated_by = (int)$updated_by;
		
		$params = "$user_id, $task_id, $organ_id, '$participants', $owner_id, '$name', '$description', '$due_date', '$start_date', $status, $priority, $m_id, $updated_by";
		$update_string = "CALL task_update(".$params.")";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	public function task_delete($user_id, $task_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		
		$this->db->query("CALL task_delete($user_id, $task_id, $organ_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	public function task_owner_update($user_id, $task_id, $owner_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		$owner_id = (int)$owner_id;
		
		$update_string = "CALL task_owner_update($user_id, $task_id, $owner_id, $organ_id)";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	public function task_status_update($user_id, $task_id, $status, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		$status = (int)$status;
		
		$update_string = "CALL task_status_update($user_id, $task_id, $status, $organ_id)";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	public function task_priority_update($user_id, $task_id, $priority, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		$priority = (int)$priority;
		
		$update_string = "CALL task_priority_update($user_id, $task_id, $priority, $organ_id)";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	public function task_startdate_update($user_id, $task_id, $start_date, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		
		
		$update_string = "CALL task_startdate_update($user_id, $task_id, '$start_date', $organ_id)";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	public function task_duedate_update($user_id, $task_id, $due_date, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		
		$update_string = "CALL task_duedate_update($user_id, $task_id, '$due_date', $organ_id)";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}

	/* Tasks Comments */
	public function get_all_task_comment($user_id, $task_id, $organ_id)
	{
		$task_id = (int)$task_id;
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		
		$query = $this->db->query("CALL task_comments_load($user_id, $task_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}
	
	public function task_comment_delete($user_id, $comment_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		
		$this->db->query("CALL task_comment_delete($user_id, $comment_id, $organ_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	public function task_comment_add($comment, $user_id, $task_id, $organ_id, $plan_id)
	{
		$comment = $this->db->escape_str($comment);
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		
		$add_string = "CALL task_comment_add('$comment', $user_id, $task_id, $organ_id, $plan_id, @task_progress_id)";
		$this->db->query($add_string);	
		$query = $this->db->query("CALL task_comment_specific_load(@task_progress_id, $organ_id)");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;		
	}
	public function task_comment_update($comment, $user_id, $comment_id, $organ_id)
	{
		$comment = $this->db->escape_str($comment);
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		
		$this->db->query("CALL task_comment_update('$comment', $user_id, $comment_id, $organ_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	/* to check and delete */
	public function get_all_comments($organ_id){
		$query = $this->db->query("SELECT * FROM tasks_progress WHERE  organ_id = " . $organ_id);
        return $query->result();   
	}
	public function get_task_comment($task_id){

			$this->db->select('*');
			$this->db->from('tasks_progress');
			$this->db->join('users', ' users.user_id = tasks_progress.user_id ');
			$this->db->where('tasks_progress.task_id', $task_id);
			$this->db->order_by('date_post','DESC');
			$query = $this->db->get();

	        return $query->result();   
    
	}
	public function update_task_ntdid($task_id, $data=array())
	{
		$this->db->where('task_id', $task_id);
		$this->db->update('tasks', $data);
	}

	public function task_status_update_meeting($user_id, $task_id, $status, $organ_id)
	{
		$user_id = (int)$user_id;
		$task_id = (int)$task_id;
		$organ_id = (int)$organ_id;
		$status = (int)$status;
		
		$update_string = "CALL task_status_update_meeting($user_id, $task_id, $status, $organ_id)";
		$query = $this->db->query($update_string); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
}