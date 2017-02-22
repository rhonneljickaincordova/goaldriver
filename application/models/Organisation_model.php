<?php

class Organisation_model extends MY_Model{

	public $_table = 'organisation';
	public $primary_key = 'organ_id';
	

	public function get_organ_id($user_id){
		$query = $this->db->query("SELECT organ_id 							
		 						   FROM organisation
		 						   WHERE owner_id = ". $user_id ."");
        return ($query->num_rows() > 0) ? $query->row()->organ_id : null;

	}

	
	public function get_organisations_by_user($user_id = 0){
		$user_id = (int)$user_id;
	
		$query = $this->db->query("CALL organisations_load($user_id)");
		$query->next_result();
		if($query->num_rows() > 0){
			$tmp_organisations = $query->result();
			$organisations = array();
			foreach($tmp_organisations as $tmp_organisation){
				$tmp_organisation->entered = gd_date($tmp_organisation->entered, "Y-m-d" );
				$tmp_organisation->updated = gd_date($tmp_organisation->updated, "Y-m-d" );
				$organisations[] = $tmp_organisation;
			}
			return $organisations;
		}else{
			return false;
		}
	}

	public function get_organisations_by_user_per_account_id($user_id = 0){
		$user_id = (int)$user_id;
		$account_id = $this->session->userdata('account_id');
	
		$query = $this->db->query("SELECT 
			organisation.organ_id, 
			organisation.owner_id, 
			organisation.name, 
			organisation.updated, 
			organisation.updated_by, 
			organisation.account_id,
			users.first_name, 
			users.last_name 
			FROM organisation, organisation_users, users 
			WHERE 
			organisation_users.user_id = users.user_id 
			AND organisation.account_id = '".$account_id."'
			AND organisation.organ_id = organisation_users.organ_id
			AND organisation_users.user_id =".$user_id." ORDER BY organisation.name");

		return ($query->num_rows() > 0) ?  $query->result() : false;
	}
	
	
	
	public function get_organisations_by_user_simple($user_id = 0, $organ_id = 0){
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$ext_where = ($organ_id == 0) ? "" : " AND organisation_users.organ_id != $organ_id ";
		
		$query = $this->db->query("SELECT 
			organisation.organ_id, 
			organisation.name
			FROM organisation, organisation_users
			WHERE 
			organisation.organ_id = organisation_users.organ_id
			AND organisation_users.user_id =".$user_id." 
			$ext_where
			ORDER BY organisation.name");

		return ($query->num_rows() > 0) ?  $query->result() : false;		
	}
	
	
	
	public function organisation_add($user_id, $organisation_name){
		$this->load->model('Subsection_model');
		
		$organisation_name = $this->db->escape_str($organisation_name);	
		$this->db->query("CALL organisation_add($user_id, '$organisation_name', @organ_id, @plan_id, 0)");	

		$this->Subsection_model->copy_cruncher_plans("@plan_id");

		$query = $this->db->query("select * from ".$this->_table." where organ_id = @organ_id");	
		return ($query->num_rows() > 0) ? $query->row()->organ_id : false;			
	}

	public function organisation_copy_existing($user_id, $organisation_name, $organ_id){
		$this->load->model('Subsection_model');
		$this->load->model('Milestone_model');
		$this->load->model('Meeting_model');
		$user_id = $this->session->userdata('user_id');

		
		// get plan_id of the organ
		$plan_idQuery = $this->db->query("SELECT b.plan_id FROM organisation a, plan b WHERE a.account_id = b.account_id AND b.organ_id = {$organ_id} AND a.owner_id = {$user_id} LIMIT 1");
		$plan_id = $plan_idQuery->row()->plan_id;

		$organisation_name = $this->db->escape_str($organisation_name);	
		
		$query = $this->db->query("CALL organisation_add($user_id, '$organisation_name', @organ_id, @plan_id, $plan_id);");
		$query_plan_id = $this->db->query("SELECT @plan_id AS new_plan_id");
		$query_organ_id = $this->db->query("SELECT @organ_id AS new_organ_id");

		$new_plan_id = $query_plan_id->row()->new_plan_id;
		$new_organ_id = $query_organ_id->row()->new_organ_id;
		

		// -- copy plans
		$this->Subsection_model->copy_cruncher_plans($new_plan_id, $plan_id);
		
		// -- copy strategy
		$this->db->query("CALL strategy_copy($user_id, $plan_id, @plan_id)");

		// -- copy milestone & tasks
		$this->Milestone_model->copy_milestone($plan_id, $new_organ_id, $organ_id, $new_plan_id);

		// -- copy meeting templates
		$this->Meeting_model->copy_meeting_template($plan_id, $new_organ_id, $organ_id, $new_plan_id);

		$query = $this->db->query("select * from ".$this->_table." where organ_id = @organ_id");	
		return ($query->num_rows() > 0) ? $query->row()->organ_id : false;	
	}
	
	public function organisation_login($user_id, $organ_id){
		$query = $this->db->query("CALL organisation_login($user_id, $organ_id)");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;		
	}
	
	
	
	public function organisation_users($user_id, $organ_id){
		$query = $this->db->query("CALL organisation_users_load($user_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	
	public function organisation_members($user_id, $organ_id){
		$query = $this->db->query("CALL organisation_members_load($user_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	
	public function organisation_delete($user_id, $organ_id){
		$this->db->query("CALL organisation_delete($user_id, $organ_id)");	
		
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	
	public function get_owner_permission($organ_id, $user_id){
		$this->db->where(array( "organ_id" => $organ_id, "owner_id"=>$user_id ));
		$query = $this->db->get($this->_table);
		return ($query->num_rows() > 0) ? $query->row() : false;
	}	
	
	public function is_owner($user_id = 0){
		$owner = $this->Organisation_model->get_organ_id($user_id);
		return ($owner != null) ? true : false;
	}
	
	public function get_my_organisation($user_id)
	{
		$query = $this->db->query("SELECT 
										a.organ_id, 
										a.user_id, 
										b.name 
									FROM 
										organisation_users a, 
										organisation b 
									WHERE 
										a.organ_id = b.organ_id 
									AND 
										a.user_id = {$user_id}");
		return ($query->num_rows() > 0) ?  $query->result() : false;	
	}
}