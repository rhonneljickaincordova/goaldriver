<?php

class Team_model extends MY_Model{

	public $_table = 'team';
	public $primary_key = 'team_id';
	public $before_create = array('created_at', 'updated_at');
	public $before_update = array('updated_at');

	//protected $soft_delete = TRUE;

	public $validate = array(
		array( 'field' => 'team',
	         'label' => 'Team Name',
	         'rules' => 'required' ),
	);

	public function created_at($users)
    {
        $users['entered'] = date('Y-m-d H:i:s');
        return $users;
    }

    public function updated_at($users){
    	$users['updated'] = date('Y-m-d H:i:s');
    	return $users;
    }

    function get_teams($user_id, $organ_id){
    	$query = $this->db->query("CALL organisation_teams_load($user_id, $organ_id)");
        $query->next_result();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    function add_new_team($data){
        return ($this->db->insert('team', $data)) ? $this->db->insert_id() : false;
    }

    function update_member_count($team_id, $direction){

        $query = $this->db->query("SELECT member_count FROM team WHERE team_id = {$team_id} AND entered_by_user_id = {$this->session->userdata('user_id')}");
        $row = $query->row();
  
        $count = 'member_count';
        
        if($direction == 'up'){
            $count = 'member_count+1';
        }
        elseif($direction == 'down'){
            if($row->member_count > 0){
                $count = 'member_count-1';    
            }
        }

        $this->db->where('team_id', $team_id);
        $this->db->set('member_count', $count, FALSE);
        $this->db->update('team');
    }
}