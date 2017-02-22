<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */


	var $organ_id = null;
	var $user_id = null;
	var $plan_id = null;
	
	public function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('logged_in')) 
		{
			redirect('account/sign_in');
		}

		
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id  = $this->session->userdata('organ_id');
		$this->plan_id  = $this->session->userdata('plan_id');

		
		if($this->organ_id == null)
		{
			$session_data = array(
				'error_message'	=> "Please select an organisation first."
			);	
			$this->session->set_userdata($session_data);	
			redirect("user-settings/organisations"); 
		}
	}

	public function index()
	{
		if(! $this->session->userdata('logged_in'))
		{
			redirect('account/sign_in');
		} 
			
		redirect('dashboard','refresh');
		
	}
}
