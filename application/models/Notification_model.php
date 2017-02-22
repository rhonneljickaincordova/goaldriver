<?php
class Notification_model extends CI_Model{

	public function success($data){
		if ($this->db->insert('notifications', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}


	public function get_all_Notification($userId)
	{
		$userId = (int)$userId;
		$query = $this->db->query("SELECT *	FROM notifications WHERE user_id = ". $userId );

		return ($query->num_rows() > 0) ? $query->result() : array();   
	}

	public function get_all_Notification_by_organ($userId,$org_id)
	{
		$userId = (int)$userId;
		$org_id = (int)$org_id;
		$query = $this->db->query("SELECT *	FROM notifications WHERE user_id = ". $userId ."  AND  organ_id = ". $org_id );

		return ($query->num_rows() > 0) ? $query->result() : array();   
	}
	
	public function get_all_unseen_notification($userId){
		$userId = (int)$userId;
		$query = $this->db->query("SELECT * FROM notifications WHERE user_id = ". $userId ." AND  status = 0 ");
        
		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	
	public function count_Notification($userId){
		$userId = (int)$userId;
		$query = $this->db->query("SELECT count(notification_id) as count FROM notifications WHERE user_id = ". $userId ." AND  status = 0 ");
        
		return $query->result();   
	}
	
	
	public function update_Notification_Status($id, $user_id){
		$id = (int)$id;
		$user_id = (int)$user_id;
		$query = $this->db->query("SELECT status FROM notifications WHERE user_id = ".$user_id." and notification_id =" .$id);

        if($query->num_rows() > 0 && $query->row()->status == 0 ){
			$update = $this->db->query("UPDATE notifications SET status = 1 WHERE notification_id = ". $id);
	        
			return $udate;   
		}
		
		return false;
	}

	
	public function update_all_notification_status($id, $user_id){
		$id = (int)$id;
		$user_id = (int)$user_id;
		$this->db->query("UPDATE notifications SET status = 1 WHERE user_id = ".$user_id." and notification_id = ". $id);
	}
	
	
	public function delete_notification($tmp_ids, $user_id){
		$response = false; 
		$user_id = (int)$user_id;
		if(!empty($tmp_ids))
		{
			$ids = array();
			foreach($tmp_ids as $id){
				$ids[] = (int)$id;	
			}
			
			$this->db->where('user_id', $user_id);
			$this->db->where_in('notification_id', $ids);
			$response =  $this->db->delete('notifications');

		}
		return $response; 
	}

	
	public function delete_all_notification($user_id){
		$user_id = (int)$user_id;
		$query = $this->db->query("DELETE FROM notifications where user_id = $user_id");
	    return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}

	
	public function delete_all_notification_by_organ($organ_id, $user_id){
		$organ_id = (int)$organ_id;
		$user_id = (int)$user_id;
		$query = $this->db->query("DELETE FROM notifications where user_id = $user_id AND organ_id = $organ_id");
	    return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
}
?>