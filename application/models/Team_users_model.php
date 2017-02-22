<?php

class Team_users_model extends MY_Model{

	public $_table = 'team_users';
	public $primary_key = 'team_user_id';
	public $before_create = array('created_at', 'updated_at');
	public $before_update = array('updated_at');


	public function created_at($users)
    {
        $users['entered'] = date('Y-m-d H:i:s');
        return $users;
    }

    public function updated_at($users){
    	$users['updated'] = date('Y-m-d H:i:s');
    	return $users;
    }

	function get_team_managers($user_id, $organ_id){
        $query = $this->db->query("CALL team_manager_list_load($user_id, $organ_id)");
        $query->next_result();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

	function get_team_members($user_id, $team_id){
        $query = $this->db->query("CALL team_members_load($user_id, $team_id)");
        $query->next_result();
        return ($query->num_rows() > 0) ? $query->result() : array();   
    }

    function get_non_team_members($user_id, $team_id, $organ_id){
        $query = $this->db->query("CALL team_non_members_load($user_id, $team_id, $organ_id)");
        $query->next_result();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    function delete_user_from_team($user_id)
    {
        $response = false;

        if(!empty($user_id))
        {
            $this->db->where('user_id', $user_id);
            $response = $this->db->delete('team_users');
        }

        return $response;
    }
	
	

}