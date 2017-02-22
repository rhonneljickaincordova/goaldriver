<?php

class Users_model extends MY_Model{

    public $_table = 'users';
    public $primary_key = 'user_id';
    public $before_create = array('created_at', 'updated_at');
    public $before_update = array('updated_at');
    

    protected $soft_delete = TRUE;
    
    public function created_at($users)
    {
        $users['entered'] = date('Y-m-d H:i:s');
        return $users;
    }

    public function updated_at($users){
        $users['updated'] = date('Y-m-d H:i:s');
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

        array( 'field' => 'password',
             'label' => 'password',
             'rules' => 'required' ),

        array( 'field' => 'password2',
             'label' => 'confirm password',
             'rules' => 'required|matches[password]' ),
    );

      
    function is_exists($field, $value, $user_id){
        $query = $this->db->get_where('users', array($field => $value, 'user_id' => $user_id), null, null);
        if ($query->num_rows() > 0){
            return true;
        }
        return false;
    }

    function is_user_exists($field, $value){
        $query = $this->db->get_where('users', array($field => $value), null, null);
        if ($query->num_rows() > 0){
            return true;
        }
        return false;
    }

    function get_users_by_organization($organ_id){

            $this->db->select('*');
            $this->db->from('organisation_users');
            $this->db->join('users', ' users.user_id = organisation_users.user_id ','left');
            $this->db->where(array('organisation_users.organ_id' => $organ_id));
            
            $query = $this->db->get();

            return $query->result();   

      
    }

    function get_users(){
          $query = $this->db->get('users');
        return $query->result();
    }

    function get_user_filter_by_id($user_id){
        $query = $this->db->query("SELECT * FROM users WHERE user_id=" .$user_id);
        return $query->result();   
    }

    function remove_profile_pic($user_id){
        $this->db->where('user_id', $user_id);
        $this->db->update($this->_table, array('profile_pic' => ''));
        return true;
    }


    function save_new_password($data,$id)
    {
         if(!empty($data))
        {
            $this->db->trans_start();
            $this->db->where('user_id', $id);
            $this->db->update('users', $data);
            $this->db->trans_complete(); 

            if ($this->db->trans_status() === FALSE)
            {
                $response = 0;
            }
            else
            {
                $response = 1;
            }
        }

        return $response;
    }


    /**********************************************************
     * Auth
     **********************************************************/ 
    // get organ_id of a canvas
    function get_organ_id($table, $id)
    {
        $sql = "SELECT organ_id FROM {$table} WHERE user_id = ?";
        $query = $this->db->query($sql, array($id));

        if($query->num_rows() > 0)
            return $query->row()->organ_id;

        return false;
    }

    function get_organ_id_from_team($table, $id)
    {
        $sql = "SELECT organ_id FROM {$table} WHERE team_id = ?";
        $query = $this->db->query($sql, array($id));

        if($query->num_rows() > 0)
            return $query->row()->organ_id;

        return false;
    }



}