<?php

class Mail_notification_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}

	function get_recipient_email($user_id)
	{
		$sql = "SELECT email FROM users WHERE user_id = ?";
		$query = $this->db->query($sql, array($user_id));

		if($query->num_rows() > 0)
		{
			return $query->row()->email;
		}

		return false;
	}

	function creator($user_id)
	{
		$sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
		$query = $this->db->query($sql, array($user_id));

		if($query->num_rows() > 0)
		{
			return $query->result();
		}

		return false;
	}

	


}