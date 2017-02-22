<?php

class Canvases_model extends CI_Model{

	function get_cavanases()
	{
		$user_id = $this->session->userdata('user_id');
		$organ_id = $this->session->userdata('organ_id');

		// $sql = "(SELECT id,name,type,entered,updated,entered_by FROM canvas_business_model WHERE organ_id=".$organ_id.")
		// 		UNION
		// 		(SELECT id,name,type,entered,updated,entered_by FROM canvas_lean WHERE organ_id=".$organ_id.")
		// 		UNION
		// 		(SELECT id,name,type,entered,updated,entered_by FROM canvas_personal_goals WHERE organ_id=".$organ_id.")
		// 		UNION
		// 		(SELECT id,name,type,entered,updated,entered_by FROM canvas_kpi WHERE organ_id=".$organ_id.")
		// 		ORDER BY entered DESC";
		$query = $this->db->query("CALL canvases_load_list(".$user_id.", ".$organ_id.")");
		$query->next_result();
		return $query->result();
	}

	function get_canvas_users()
	{
		$user_id = $this->session->userdata('user_id');
		$organ_id = $this->session->userdata('organ_id');

		$query = $this->db->query("CALL organisation_users_load(".$user_id.", ".$organ_id.")");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
	}

	// ADD
	function add_business_model($data)
	{
		if($this->db->insert('canvas_business_model', $data))
		{
			return $this->db->insert_id();
		}
		return false;
	}

	function add_lean($data)
	{
		if($this->db->insert('canvas_lean', $data))
		{
			return $this->db->insert_id();
		}
	}

	function add_personal_goals($data)
	{
		if($this->db->insert('canvas_personal_goals', $data))
		{
			return $this->db->insert_id();
		}
	}

	function add_kpi($data)
	{
		if($this->db->insert('canvas_kpi', $data))
		{
			return $this->db->insert_id();
		}
	}

	// GET
	function get_business_model($id)
	{
		// $sql = "SELECT * FROM canvas_business_model WHERE id = ? AND user_id = ?";
		$query = $this->db->query("CALL canvas_bm_load(".$this->session->userdata('user_id').", ".$id.")");
		$query->next_result();
		return $query->result();
	}

	function get_lean($id)
	{
		// $sql = "SELECT * FROM canvas_lean WHERE id = ? AND user_id = ?";
		$query = $this->db->query("CALL canvas_lean_load(".$this->session->userdata('user_id').", ".$id.")");
		$query->next_result();
		return $query->result();
	}

	function get_personal_goals($id)
	{
		// $sql = "SELECT * FROM canvas_personal_goals WHERE id = ? AND user_id = ?";
		$query = $this->db->query("CALL canvas_pg_load(".$this->session->userdata('user_id').", ".$id.")");
		$query->next_result();
		return $query->result();
	}

	function get_kpi($id)
	{
		// $sql = "SELECT * FROM canvas_kpi WHERE id = ? AND user_id = ?";
		$query = $this->db->query("CALL canvas_kpi_load(".$this->session->userdata('user_id').", ".$id.")");
		$query->next_result();
		return $query->result();
	}

	//DELETE
	function delete_canvas($type, $id)
	{
		$errors = array();

		switch ($type) {
			case 'business':
				if(! $this->db->delete('canvas_business_model', array('id' => $id)))
					$errors[] = 1;
				break;
			
			case 'lean':
				if(! $this->db->delete('canvas_lean', array('id' => $id)))
					$errors[] = 2;
				break;

			case 'personal':
				if(! $this->db->delete('canvas_personal_goals', array('id' => $id)))
					$errors[] = 3;
				break;

			case 'kpi':
				if(! $this->db->delete('canvas_kpi', array('id' => $id)))
					$errors[] = 4;
				break;
		}

		return (count($errors) < 1) ? true : false;
	}


	function update_canvas($id, $table, $data)
	{
		$this->db->where('id', $id);
		$update = $this->db->update($table, $data);

		if($update) return true;

		return false;
	}

	function update_business_model_name($id, $name)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('canvas_business_model', array('name' => $name));

		if($update) return true;

		return false;
	}

	function update_lean_name($id, $name)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('canvas_lean', array('name' => $name));

		if($update) return true;

		return false;
	}
	

	function update_personal_goals_name($id, $name)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('canvas_personal_goals', array('name' => $name));

		if($update) return true;

		return false;
	}
	

	function update_kpi_name($id, $name)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('canvas_kpi', array('name' => $name));

		if($update) return true;

		return false;
	}

	function is_canvas_exists_in_canvas_users($table, $canvas_id)
	{
		$query = $this->db->get_where($table, array('canvas_id' => $canvas_id), NULL, NULL);

		if($query->num_rows() > 0) return true;
		return false;
	}

	function is_canvas_exists($table, $canvas_id)
	{
		$query = $this->db->get_where($table, array('id' => $canvas_id), NULL, NULL);

		if($query->num_rows() > 0) return true;
		return false;
	}

	function get_canvas_shared_users($table, $canvas_id)
	{
		$query = $this->db->get_where($table, array('canvas_id' => $canvas_id), NULL, NULL);
		return $query->result();
	}

	function update_canvas_users($table, $canvas_id, $users)
	{
		$this->db->where(array('canvas_id' => $canvas_id, 'user_id' => $user_id));
		$update = $this->db->update($table, array('user_id' => $users));

		if($update) return true;

		return false;
	}

	function save_canvas_users($table, $data)
	{
		$insert = $this->db->insert($table, $data);

		if($insert) return true;

		return false;
	}

	function delete_canvas_user($tabe, $user_id)
	{
		$this->db->where('user_id', $user_id);
		if($this->db->delete($tabe))
			return true;
		return false;
	}

	function is_exists_canvas_user($table, $user_id)
	{
		$query = $this->db->get_where($table, array('user_id' => $user_id), NULL, NULL);

		return ($query->num_rows() > 0) ? true : false;
	}

	function delete_all_canvas_users($table, $canvas_id)
	{
		$this->db->where('canvas_id', $canvas_id);
		if($this->db->delete($table))
			return true;
		return false;
	}

	/**********************************************************
	 * Auth
	 **********************************************************/ 
	// get organ_id of a canvas
	function get_organ_id($table, $canvas_id)
	{
		$sql = "SELECT organ_id FROM {$table} WHERE id = ?";
		$query = $this->db->query($sql, array($canvas_id));

		if($query->num_rows() > 0)
			return $query->row()->organ_id;

		return false;
	}

	function get_canvas_info($table, $field, $canvas_id)
	{
		$sql = "SELECT {$field} FROM {$table} WHERE id = ?";
		$query = $this->db->query($sql, array($canvas_id));

		if($query->num_rows() > 0)
			return $query->row()->$field;

		return false;
	}
	

}