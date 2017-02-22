<?php
// created by: James Erie
defined('BASEPATH') OR exit('No direct script access allowed');

/*
- check if session organ_id is the same to requested organ_id
- if not, check if that user belong to that organ_id 
- if yes it belong, then switch to that requested organ_id
- if not belong, show 404
*/

class Auth
{
	var $user_id = 0;
	var $organ_id = 0;

	function __construct()
	{
		$this->ci = &get_instance();

		$this->user_id = $this->ci->session->userdata('user_id');
		$this->organ_id = $this->ci->session->userdata('organ_id');
		
		// Load models
		$this->ci->load->model('Canvases_model', 'canvas');
		$this->ci->load->model('Chapter_model', 'chapter');
		$this->ci->load->model('Plan_model', 'plan');
		$this->ci->load->model('Users_model', 'users');
		$this->ci->load->model('Organisationusers_model', 'organ_user');
		$this->ci->load->library('user_agent');
	}

	function canvas_access($type, $canvas_id)
	{
		$table = '';

		switch ($type) 
		{
			case 'business':
				$table_canvas = 'canvas_business_model';
				$table_canvas_user = 'canvas_bm_users';
				break;
			
			case 'personal':
				$table_canvas = 'canvas_personal_goals';
				$table_canvas_user = 'canvas_pg_users';
				break;

			case 'lean':
				$table_canvas = 'canvas_lean';
				$table_canvas_user = 'canvas_lean_users';
				break;

			case 'kpi':
				$table_canvas = 'canvas_kpi';
				$table_canvas_user = 'canvas_kpi_users';
				break;
		}

		$canvas_organ_id = $this->ci->canvas->get_organ_id($table_canvas, $canvas_id);
		$result = $this->ci->canvas->get_canvas_shared_users($table_canvas_user, $canvas_id);

		$is_owner = $this->ci->canvas->get_canvas_info($table_canvas, 'user_id', $canvas_id);



		if(($is_owner == $this->user_id) || count($result))
		{
			$switch_organ = array('organ_id' => $canvas_organ_id);
			$this->ci->session->set_userdata($switch_organ);
			//redirect('canvases/edit_canvas/'.$type.'/'.encrypt($canvas_id));
			return true;
		}
		else{
			return false;
			//show_404_page("404_page");

		}

		// organ_id of requested canvas
		//$canvas_organ_id = $this->ci->canvas->get_organ_id($table, $canvas_id);
		
		//echo $this->user_id.'-'.$canvas_organ_id;

		// if($this->organ_id != $canvas_organ_id)
		// {	
		// 	// check if a user belong to that organ_id
		// 	if($this->ci->organ_user->organisation_member_exists($this->user_id, $canvas_organ_id))
		// 	{
		// 		$switch_organ = array('organ_id' => $canvas_organ_id);

		// 		$this->ci->session->set_userdata($switch_organ);

		// 		redirect('canvases/edit_canvas/'.$type.'/'.encrypt($canvas_id));
		// 	}
		// 	else{
		// 		show_404_page("404_page");
		// 	}
		// }

	}

	function plan_access($chapter_id=null)
	{	
		$user_organ_id = $this->ci->users->get_organ_id('organisation_users', $this->user_id);

		$plan_id = $this->ci->chapter->get_plan_id_by_chapter(decrypt($chapter_id));
		// get organ_id of the plan which the chapter belongs to
		// $plan_organ_id = $this->ci->plan->get_organ_id_by_plan($plan_id);
		$plan = $this->ci->plan->get_by('plan_id', $plan_id);

			
		if($this->user_id != $plan->owner_id)
		{
			if($user_organ_id != $plan->organ_id)
			{
				// check if user has access to this plan
				if($this->ci->organ_user->organisation_member_exists($this->user_id, $plan->organ_id))
				{
					// get the organ_id of requested chapter
					$switch_organ = array('organ_id' => $plan->organ_id);

					$this->ci->session->set_userdata($switch_organ);

					return true;
				}
				else{
					return false;
				}
			}
		}
		return true;
	}

	function user_access($user_id)
	{
		$table = 'organisation_users';

		// organ_id of requested canvas
		$user_organ_id = $this->ci->users->get_organ_id($table, $user_id);
		
		//echo $this->user_id.'-'.$user_organ_id;

		if($this->organ_id != $user_organ_id)
		{	
			// check if a user belong to that organ_id
			if($this->ci->organ_user->organisation_member_exists($this->user_id, $user_organ_id))
			{

				$switch_organ = array('organ_id' => $user_organ_id);

				$this->ci->session->set_userdata($switch_organ);

				redirect('teams/edit_user/'.encrypt($user_id));
			}
			else{
				show_404_page("404_page");
			}
		}
	}


}