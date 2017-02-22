<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Permission extends CI_Controller 
{
	var $user_id = 0;
	var $organ_id = 0;
	var $response_code = 1; 
	var $response_message = "";

	public function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('logged_in')) 
		{
			redirect('account/sign_in');
		}

		$this->plan_id = $this->session->userdata('plan_id');
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');

		if($this->organ_id == null)
		{
			$session_data = array(
				'error_message'	=> "Please select an organisation first."
			);	
			$this->session->set_userdata($session_data);	
			redirect("user-settings/organisations"); 
		}

		$this->load->model('Users_model');
		$this->load->model('Organisationusers_model');
		$this->load->model('Organisation_model');
		$this->load->model('Permission_model');


		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}

		if($no_access == "yes")
		{
			redirect('dashboard','refresh');
		}


	}

	public function set_permission($encrypted_userid="", $encrypted_organid="")
	{
		if(!empty($encrypted_userid) && !empty($encrypted_organid))
		{
			$userid = decrypt($encrypted_userid);
			$organid = decrypt($encrypted_organid);

			if(!empty($userid) && !empty($organid))
			{
				$user_id = $this->session->userdata('user_id');
				$organ_id  = $this->session->userdata('organ_id');

				$can_view_list = "";
				$no_access = "";
				$access = "";
				$tab_id  = 7;

				$has_access = check_access($user_id, $organ_id, $tab_id);

				if(!empty($has_access))
				{
					if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 0)
					{
						$data['disabled'] = "disabled";
					}
					if($has_access[0]['readwrite'] == 1 && $has_access[0]['readonly'] == 0)
					{
						$data['disabled'] = "";
					}
					if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 1)
					{
						$data['disabled'] = "";
					}
				}

				if(!empty($has_access))
				{
					if($has_access[0]['readonly'] == 1)
					{
						$can_view_list = "yes";
					}
					if($has_access[0]['hidden'] == 1)
					{
						$no_access = "yes";
					}
					if($has_access[0]['readwrite'] == 1)
					{
						$access = "yes";
					}
				}

				
				if($organid == $organ_id)
				{
					if($can_view_list == "yes")
					{
						show_404_page("404_page" );
					}

					if($no_access == "yes")
					{
						show_404_page("404_page" );
					}

					if($access == "yes")
					{
						$data['title'] = 'Set Permission for '.user_info("first_name", $userid)." ".user_info("last_name", $userid);

						$data['organisations'] = $this->Organisation_model->get_organisations_by_user_per_account_id($userid);
						$data['is_owner'] = $this->Organisation_model->is_owner($userid);
						$data['user_id'] = $userid;
						$data['tabs']	= $this->Permission_model->get("system_tabs", "", "");
						$data['count']	= count($this->Permission_model->get("system_tabs", "", ""));
						
						$this->load->view('permission/index', $data);
					}

					if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
					{
						$data['title'] = 'Set Permission for '.user_info("first_name", $userid)." ".user_info("last_name", $userid);

						$data['organisations'] = $this->Organisation_model->get_organisations_by_user_per_account_id($userid);
						$data['is_owner'] = $this->Organisation_model->is_owner($userid);
						$data['user_id'] = $userid;
						$data['tabs']	= $this->Permission_model->get("system_tabs", "", "");
						$data['count']	= count($this->Permission_model->get("system_tabs", "", ""));
						
						$this->load->view('permission/index', $data);
					}
				}
				else
				{
					show_404_page("404_page" );
				}

			}
			else
			{
				$data['title'] = '404 Page Not Found';
				show_404_page("404_page" );
			}
		}
		else
		{
			redirect("teams/user", "refresh");
		}
	}

	public function save_user_permission()
	{
		$array = array();

		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 7;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$can_view_list = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$no_access = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$access = "yes";
			}
		}

		if($access == "yes" || $this->session->userdata('user_type') == "admin")
		{		
			foreach($_POST['permission'] as $org=>$tabs)
			{
				foreach($tabs as $key=>$value)
				{
					$hidden = 0;
					$readonly = 0;
					$readwrite = 0;

					if($value['right'] == 'hidden')
					{
						$hidden = 1;
					}
					if($value['right'] == "readonly")
					{
						$readonly = 1;
					}
					if($value['right'] == "readwrite")
					{
						$readwrite = 1;
					}

					$array[] = array(
						'user_id' 	=> decrypt($_POST['user_id']),
						'organ_id'	=> $org,
						'tab_id'	=> $key,
						'hidden'	=> $hidden,
						'readonly'	=> $readonly,
						'readwrite'	=> $readwrite,
					);

					$deleted = $this->Permission_model->delete_old_entry(decrypt($_POST['user_id']), $org, $key);
				}
			}
			if($deleted)
			{
				$inserted = $this->Permission_model->insert("permissions", $array);

				if($inserted)
				{
					$this->response_code = 0;
					$this->response_message = "Successfully saved permissions.";
				}
				else
				{
					$this->response_message = "Failed to save permissions.";
				}
			}

			echo json_encode(array(
					"error"			=> $this->response_code,
					"message"		=> $this->response_message,
			));
		}
		else
		{
			echo json_encode(array(
				"error"			=> 1,
				"message"		=> "No rights.",
			));
		}

	}

}