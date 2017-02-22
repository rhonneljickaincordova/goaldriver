<?php

class Organisationusers_model extends MY_Model{

	public $_table = 'organisation_users';
	public $primary_key = 'organ_user_id';
	
	public $before_create = array( 'timestamps' );
	public $before_update = array( 'timestamps_udpate' );

    protected function timestamps($org_user)
    {
        $org_user['entered'] = $org_user['updated'] = date('Y-m-d H:i:s');
        return $org_user;
    }

	
	
    protected function timestamps_udpate($org_user)
    {
        $org_user['updated'] = date('Y-m-d H:i:s');
        return $org_user;
    }

	
	
	public function get_organ_id($user_id)
	{
		$user_id = (int)$user_id;
		$query = $this->db->query("SELECT organ_id 							
		 						   FROM organisation_users
		 						   WHERE user_id = ". $user_id );
								   
        return $query->row()->organ_id;   
	}	
	
	
	
	public function get_organisation_users($organ_id, $user_id = 0)
	{
		$user_id = (int)$user_id;
		$query = $this->db->query("CALL organisation_users_load($user_id, $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function organisation_member_exists($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$query = $this->db->query("CALL organisation_member_exists($user_id, $organ_id)");	
		$query->next_result();
		$row = $query->row();
		
		return ($row->count > 0) ? true : false;
	}
	
	public function organisation_users_add($user_id, $new_user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$new_user_id = (int)$new_user_id;
		$organ_id = (int)$organ_id;
		$this->db->query("CALL organisation_user_add($user_id, $new_user_id, $organ_id, @organ_user_id)");	
		$query = $this->db->query("select * from ".$this->_table." where organ_user_id = @organ_user_id");	
		return ($query->num_rows() > 0) ? $query->row()->organ_user_id : false;	
	}
	
	
	
	public function organisation_user_exist($user_id, $email, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$query = $this->db->query("CALL organisation_user_exists($user_id, '$email', $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	
	
	public function organisation_user_exists_any($user_id, $email, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$query = $this->db->query("CALL organisation_user_exists_any($user_id, '$email', $organ_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
	
	
	public function username_is_unique($username)
	{
		$query = $this->db->get_where('users', array('username' => $username));
		return ($query->num_rows() > 0) ? false : true;
	}
	
	public function organisation_user_member_add($master_account_id, $first_name, $last_name, $username, $email, $hash, $organ_id)
	{
		$hash = $this->db->escape_str($hash);	
		$username = $this->db->escape_str($username);	
		$first_name = $this->db->escape_str($first_name);	
		$last_name = $this->db->escape_str($last_name);	
		
		$data = array();
		$this->db->query("CALL organisation_user_member_add($master_account_id, '$first_name', '$last_name', '$username', '$email', '$hash', $organ_id, @user_id, @organ_user_id)");	
		$query = $this->db->query("select * from ".$this->_table." where user_id = @user_id and organ_user_id = @organ_user_id");	
		
		if($query->num_rows() > 0)
		{
			$data['organ_user_id'] = $query->row()->organ_user_id;	
			$data['user_id'] = $query->row()->user_id;	
			
			return $data;
		}
		else
		{
			return false;
		}
	}

	function delete_organisation_user($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		if($this->db->delete($this->_table, array('user_id' => $user_id, 'organ_id' => $organ_id))){
			return true;
		}
		return false;
	}

	function is_organisation_admin($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;

		$this->db->select('*');
		$this->db->from('organisation');
		$this->db->where('organ_id', $organ_id);
		$this->db->where('owner_id', $user_id);

		$query = $this->db->get()->result_array();

		return $query;
	}


}