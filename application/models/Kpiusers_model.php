<?php

class Kpiusers_model extends MY_Model{

	public $_table = 'kpi_users';
	public $primary_key = 'kpi_user_id';
	

	public function get_kpi_users($organ_id, $kpi_id)
	{
		$organ_id = (int)$organ_id;
		$kpi_id = (int)$kpi_id;
		$query = $this->db->query("CALL kpi_users_load($organ_id, $kpi_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function get_kpi_members_only($organ_id, $kpi_id, $owner_id)
	{
		$organ_id = (int)$organ_id;
		$kpi_id = (int)$kpi_id;
		$owner_id = (int)$owner_id;
		$query = $this->db->query("CALL kpi_members_load($organ_id, $kpi_id, $owner_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : array();
		
	}
	
	public function kpi_user_add($kpi_id, $user_id)
	{
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$query_string = "CALL kpi_user_add($kpi_id, $user_id, @kpi_user_id)";
		
		$this->db->query($query_string);	
		
		$query = $this->db->query("Select * From ".$this->_table." WHERE kpi_user_id = @kpi_user_id");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;	
	}
	
	public function kpi_user_delete($kpi_id, $user_id)
	{
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$this->db->query("CALL kpi_user_delete($kpi_id, $user_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	public function kpi_user_exists($kpi_id, $user_id, $data = false)
	{
		$kpi_id = (int)$kpi_id;
		$user_id = (int)$user_id;
		$query = $this->db->query("CALL kpi_user_exists($user_id, $kpi_id)");	
		$query->next_result();
		$row = $query->row();
		if($data){
			return ($row->count > 0) ? $row : false;
		}else{
			return ($row->count > 0) ? true : false;
		}
	}
	
	
	public function update_kpi_users($organ_id, $owner_id, $kpi_id, $users)
	{
		$organ_id = (int)$organ_id;
		$owner_id = (int)$owner_id;
		$kpi_id = (int)$kpi_id;
		$kpi_users = $this->get_kpi_users($organ_id, $kpi_id, $owner_id);
		if($kpi_users){
			if(!empty($users)){
				$add_users = array();
				$kpi_user_ids = array();
				
				foreach($kpi_users as $kpi_user){
					$kpi_user_id = $kpi_user->user_id;
					if(!in_array($kpi_user_id, $users)){
						$this->kpi_user_delete($kpi_id, $kpi_user_id);	
					}else{
						$kpi_user_ids[] = $kpi_user_id;
					}
				}
				
				foreach($users as $user_id){
					if(!in_array($user_id, $kpi_user_ids)){
						$add_user = $this->kpi_user_add($kpi_id, $user_id);	
					}
				}
			}else{
				foreach($kpi_users as $kpi_user){
					$user_id = $kpi_user->user_id;
					$this->kpi_user_delete($kpi_id, $user_id);	
				}
			}
		}else{
			if(!empty($users)){
				foreach($users as $user_id){
					$user_id = (int)$user_id;
					$add_user = $this->kpi_user_add($kpi_id, $user_id);
				}
			}
		}
		
	}
	
}