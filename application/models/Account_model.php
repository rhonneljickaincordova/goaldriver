<?php

class Account_model extends MY_Model{

	public $_table = 'account';
	public $primary_key = 'account_id';


	function create_user_account($user_id){
		$account = array(
			'entered' => date('Y-m-d H:i:s'),
			'account_owner_id' => $user_id
		);
		$account_created = parent::insert($account);
		
		return $account_created;
	}

	


}