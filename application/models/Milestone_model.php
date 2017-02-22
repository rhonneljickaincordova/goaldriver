<?php

class Milestone_model extends MY_Model{

	public $_table = 'milestones';
	public $primary_key = 'id';

	
	public function get_all_milestone($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		
		$query = $this->db->query("CALL milestones_for_organ_load($user_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}
	
	public function get_monthly_milestones($user_id, $organ_id, $month, $year)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$month = (int)$month;
		$year = (int)$year;
		
		$query = $this->db->query("CALL milestones_organ_monthly_load($organ_id, $month, $year)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}
	
	
	public function get_all_milestone_tasks($user_id, $milestone_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$milestone_id = (int)$milestone_id;
		$organ_id = (int)$organ_id;
		
		$query = $this->db->query("CALL milestone_tasks_load($user_id, $milestone_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}
	
	
	public function get_milestone($milestone_id, $organ_id)
	{
		$milestone_id = (int)$milestone_id;
		$organ_id = (int)$organ_id;
		
		$query = $this->db->query("CALL milestone_specific_load($milestone_id, $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->row() : false;
	}
	

	public function milestone_add($organ_id, $plan_id, $owner_id, $status, $name, $description, $due_date, $start_date, $showDash, $entered_by)
	{
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$owner_id = (int)$owner_id;
		$status = (int)$status;
		$showDash = (int)$showDash;
		$entered_by = (int)$entered_by;
		$name = $this->db->escape_str($name);
		$description = $this->db->escape_str($description);
		
		$add = "CALL milestone_add($organ_id, $plan_id, $owner_id, $status, '$name', '$description', '$due_date', '$start_date', $showDash, $entered_by, @id)";
		$this->db->query($add);	
		$query = $this->db->query("CALL milestone_specific_load(@id, $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;		
	}
	
	
	public function milestone_update($milestone_id, $organ_id, $owner_id, $status, $name, $description, $due_date, $start_date, $updated_by)
	{
		$organ_id = (int)$organ_id;
		$plan_id = (int)$plan_id;
		$owner_id = (int)$owner_id;
		$status = (int)$status;
		$showDash = (int)$showDash;
		$entered_by = (int)$entered_by;
		$name = $this->db->escape_str($name);
		$description = $this->db->escape_str($description);
		
		$update = "CALL milestone_update($milestone_id, $organ_id, $owner_id, $status, '$name', '$description', '$due_date', '$start_date', $updated_by)";
		$query = $this->db->query($update); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	public function milestone_delete($user_id, $milestone_id, $organ_id, $delete_tasks)
	{
		$user_id = (int)$user_id;
		$milestone_id = (int)$milestone_id;
		$organ_id = (int)$organ_id;
		$delete_tasks = ((int)$delete_tasks == 1) ? 1 : 0;
		
		$this->db->query("CALL milestone_delete($user_id, $milestone_id, $organ_id, $delete_tasks)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}

	
	public function milestone_showondash_update($user_id, $organ_id, $milestone_id, $in_bShowOnDash)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$milestone_id = (int)$milestone_id;
		$in_bShowOnDash = ((int)$in_bShowOnDash == 1) ? 1 : 0;
		
		$update = "CALL milestone_showondash_update($user_id, $organ_id, $milestone_id, $in_bShowOnDash)";
		$query = $this->db->query($update); 
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
		
	public function milestones_load_for_dash($user_id, $plan_id){
		$query = $this->db->query("CALL milestones_load_for_dash($user_id, $plan_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function get_upcoming_milestones($organ_id){
		$query = $this->db->query("SELECT m.owner_id, m.id, m.name, m.dueDate, m.status, u.first_name FROM milestones m INNER JOIN users u ON u.user_id = m.owner_id WHERE m.dueDate >= NOW() AND m.organ_id = {$organ_id}");
		return ($query->num_rows() > 0) ? $query->result() : array();
	}

	public function get_late_milestones($organ_id){
		$query = $this->db->query("SELECT m.id, m.name, m.dueDate, u.first_name FROM milestones m INNER JOIN users u ON u.user_id = m.owner_id WHERE m.dueDate <= NOW() AND m.organ_id = {$organ_id}");
		return ($query->num_rows() > 0) ? $query->result() : array();
	}

	public function copy_milestone($plan_id ,$new_ogran_id, $old_organ_id=null, $new_plan_id=null)
	{	
		$owner_id = $this->session->userdata('user_id');

		$copy_milestone = $this->db->query("SELECT * FROM milestones WHERE plan_id = {$plan_id} AND organ_id = {$old_organ_id}");

		//print_r($copy_milestone->result());

		if($copy_milestone->num_rows() > 0)
    	{
    		$this->db->query("CALL plan_get_start_date($plan_id, $old_organ_id, @out_startdate)");
			$base_date_q = $this->db->query("SELECT @out_startdate AS out_startdate");

			$newStartDate = NULL;
   			$newDueDate = NULL;
    		$newOrganStartDate = date('Y-m-d');
    		
    		if($base_date_q->num_rows() > 0)
    		{
    			// base_date validate
    			if($base_date_q->row()->out_startdate == NULL || $base_date_q->row()->out_startdate == '0000-00-00')
    			{
    				$base_date = NULL;
    			}
    			else{
    				$base_date = new DateTime($base_date_q->row()->out_startdate);	
    			}

    			foreach ($copy_milestone->result() as $milestone)
    			{
    				// reset variables
					$startDate = NULL;
					$newStartDate = NULL;
					$dueDate = NULL;
					$newDueDate = NULL;



					// if($base_date != NULL)
					// {
					// 	if($milestone->startDate != NULL || $milestone->startDate != '0000-00-00')
	    // 				{
	    // 					$startDate = new DateTime($milestone->startDate);

	    // 					//var_dump($startDate); die();

	    // 					$startDateDiff = $base_date->diff($startDate);	
	    // 					$startNumDays = $startDateDiff->format('%R%a days');
	    // 					$newStartDate = date('Y-m-d', strtotime($newOrganStartDate . $startNumDays));
	    // 				}
	    				
	    				
	    // 				if($milestone->dueDate != NULL || $milestone->dueDate != '0000-00-00')
	    // 				{
	    // 					$dueDate = new DateTime($milestone->dueDate);
					// 		$dueDateDiff = $base_date->diff($dueDate);
					// 		$dueNumDays = $dueDateDiff->format('%R%a days');
					// 		$newDueDate = date('Y-m-d', strtotime($newOrganStartDate . $dueNumDays));
	    // 				}
					// }

    				//copy milestones
		    		$this->db->query("INSERT INTO milestones (organ_id,plan_id,owner_id,status,name,description,dueDate,startDate,bShowOnDash,entered_on,entered_by,updated_on,updated_by)
						SELECT {$new_ogran_id},{$new_plan_id},{$owner_id},status,name,description,'{$newDueDate}','{$newStartDate}',bShowOnDash,NOW(),{$owner_id},NULL,NULL FROM milestones WHERE id = {$milestone->id}");
		    		
		    		$new_milestone_id = $this->db->insert_id();
		    		
		    		$this->copy_milestone_tasks($plan_id, $milestone->id, $new_milestone_id, $new_ogran_id, $old_organ_id, $new_plan_id, $base_date);
    			} 

    		} // end base_date num_rows
		}
	}

	public function copy_milestone_tasks($plan_id, $milestone_id, $new_milestone_id, $new_ogran_id, $old_organ_id=null, $new_plan_id=null, $base_date=null){
		$owner_id = $this->session->userdata('user_id');

		$copy_task = $this->db->query("SELECT * FROM tasks WHERE milestone_id = {$milestone_id} AND organ_id = {$old_organ_id}");
		
		if($copy_task->num_rows() > 0)
		{
			// $newStartDate = NULL;
			// $newDueDate = NULL;
			$newOrganStartDate = date('Y-m-d');

			foreach ($copy_task->result() as $task) {
				
				$startDate = NULL;
				$newStartDate = NULL;
				$dueDate = NULL;
				$newDueDate = NULL;

				// if($base_date != NULL)
				// {
				// 	if($task->task_startDate != NULL || $task->task_startDate != '0000-00-00')
				// 	{
				// 		$startDate = new DateTime($task->task_startDate);
		  //   			$startDateDiff = $base_date->diff($startDate);
				// 		$startNumDays = $startDateDiff->format('%R%a days');
				// 		$newStartDate = date('Y-m-d', strtotime($newOrganStartDate . $startNumDays));
				// 	}

				// 	if($task->task_dueDate != NULL || $task->task_dueDate != '0000-00-00')
				// 	{
				// 		$dueDate = new DateTime($task->task_dueDate);
				// 		$dueDateDiff = $base_date->diff($dueDate);
				// 		$dueNumDays = $dueDateDiff->format('%R%a days');
				// 		$newDueDate = date('Y-m-d', strtotime($newOrganStartDate . $dueNumDays));
				// 	}
    // 			}

    			// copy tasks
				$this->db->query("INSERT INTO tasks (organ_id,participant_id,owner_id,task_name,task_description,task_dueDate,task_startDate,entered_on,entered_by,updated_on,updated_by,status,priority,plan_id,milestone_id,user_id,date_completed,ntd_id)
					SELECT {$new_ogran_id},participant_id,{$owner_id},task_name,task_description,'{$newDueDate}','{$newStartDate}',NOW(),{$owner_id},NULL,NULL,status,priority,{$new_plan_id},{$new_milestone_id},{$owner_id},date_completed,ntd_id FROM tasks WHERE task_id = {$task->task_id}");
    		}
		}

	}
	
	
	
}