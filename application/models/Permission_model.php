<?php

Class Permission_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get($table, $column="", $where="")
	{
		$this->db->select('*');
		$this->db->from($table);

		if(!empty($where))
		{
			$this->db->where($column, $where);
		}

		$this->db->order_by("tab_order", "ASC");

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function insert($table, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert_batch($table, $data);
		}

		return $response;
	}

	public function has_existing_entry($user_id, $org_id, $tab_id)
	{

        $query = null; //emptying in case 

        $query = $this->db->get_where('permissions', array(//making selection
            'user_id'   => $user_id,
            'organ_id'  => $org_id,
            'tab_id'	=> $tab_id
        ));

        $count = $query->num_rows(); //counting result from query

        if($count == 0) 
        {
            return TRUE;
        }
        else
        {
        	return FALSE;
        }
	}

	public function delete_old_entry($user_id, $org_id, $tab_id)
	{
		$response = false;

		if(!empty($user_id))
		{
			$query = "DELETE FROM permissions WHERE (organ_id=$org_id AND user_id=$user_id AND tab_id=$tab_id) OR (organ_id=$org_id AND user_id=$user_id AND tab_id=$tab_id)";
			// $this->db->where('user_id', $user_id);
			// $this->db->where('organ_id', $org_id);
			// $this->db->where('tab_id', $tab_id);
			$query = $this->db->query($query);

			$response = $query;
		}

		return $response;
	}
}