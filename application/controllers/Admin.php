<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	var $user_type = '';

	function __construct(){
		parent::__construct();

		$this->user_type = $this->session->userdata('user_type');

		if($this->user_type != 'superadmin')
		{
			redirect('dashboard');
		}

		$this->load->model('Stats_model', 'stats');
	}

	function index(){
		$data = array();

		$data['stats'] = $this->stats->get_all();
		$this->load->view('admin/index', $data);
	}

}