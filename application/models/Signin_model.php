<?php

class Signin_model extends MY_Model{

	public $_table = 'users';
	public $primary_key = 'user_id';

	public $validate = array(
		array( 'field' => 'username',
               'label' => 'Username',
               'rules' => 'required' ),

		array( 'field' => 'password',
               'label' => 'Password',
               'rules' => 'required' ),
		);

}