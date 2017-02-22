<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Learning extends CI_Controller{
	
	function __construct(){
		parent::__construct();
	}

	function index(){
		$data['title'] = 'Learning';
		$this->load->view('learning/index', $data);
	}
}