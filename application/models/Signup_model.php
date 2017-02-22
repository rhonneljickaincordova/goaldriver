<?php

class Signup_model extends MY_Model{

	public $_table = 'users';
	public $primary_key = 'user_id';
	public $before_create = array( 'timestamps' );

    protected function timestamps($users)
    {
        $users['entered'] = $users['updated'] = date('Y-m-d H:i:s');
        return $users;
    }

	public $validate = array(
		array( 'field' => 'firstname',
               'label' => 'firstname',
               'rules' => 'required' ),

		array( 'field' => 'lastname',
               'label' => 'lastname',
               'rules' => 'required' ),

		array( 'field' => 'company',
               'label' => 'company',
               'rules' => 'required' ),

		array( 'field' => 'job',
               'label' => 'job title ',
               'rules' => 'required' ),		

		array( 'field' => 'postcode',
               'label' => 'postcode',
               'rules' => 'required' ),		

		array( 'field' => 'terms',
               'label' => 'Terms and Conditions',
               'rules' => 'required' ),		

		array( 'field' => 'tel',
               'label' => 'telephone',
               'rules' => 'required' ),		

        array( 'field' => 'email', 
               'label' => 'email',
               'rules' => 'required|valid_email|is_unique[users.email]' ),

        array( 'field' => 'username', 
               'label' => 'username',
               'rules' => 'required|is_unique[users.username]' ),

        array( 'field' => 'password',
               'label' => 'password',
               'rules' => 'required' ),

        array( 'field' => 'password2',
               'label' => 'confirm password',
               'rules' => 'required|matches[password]' ),
    );


}