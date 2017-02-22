<?php

class Passwordtoken_model extends MY_Model{

	public function send_token($data)
	{
		if(!empty($data))
		{
			$response = $this->db->insert('password_token', $data);
			$latest_id = $this->db->insert_id();
			
		}
		return $latest_id;
	}

	public function get_value($token, $field = 'password_token_id', $is_used = 0)
	{
	   $escape_token = $this->db->escape_str($token);
	   $query = $this->db->query("SELECT ".$field." FROM password_token WHERE token_key = '".$escape_token."' AND is_used = ".$is_used);
       if($field == 'password_token_id'){
			return ($query->num_rows() > 0) ?  $query->row()->password_token_id : false;		
	   }else{
		   return ($query->num_rows() > 0) ?  $query->row()->user_id : false;	
		}
	   
		
	}

	public function get_user_id($password_token_id)
	{
		$this->db->select('user_id');
		$query = $this->db->get_where('password_token', array('password_token_id' => $password_token_id));

		return $query->row()->user_id;


	}

	public function update_token($data,$id){

		if(!empty($data))
		{
			$this->db->trans_start();
			$this->db->where('password_token_id', $id);
			$this->db->update('password_token', $data);
			$this->db->trans_complete(); 

			if ($this->db->trans_status() === FALSE)
			{
				$response = 'false';
			}
			else
			{
				$response = 'true';
			}
		}

		return $response;

	}
	


}