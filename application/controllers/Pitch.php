<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pitch extends CI_Controller {
	
	var $plan_id = 0;
	var $user_id = 0;
	var $organ_id = 0;

	function __construct(){
		parent::__construct();
		
		if(! $this->session->userdata('logged_in'))
			redirect('account/logout');

		$this->plan_id = $this->session->userdata('plan_id');
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');

		if($this->organ_id == null)
		{ 
			$session_data = array(
			'error_message' => "Please select an organisation first."
		); 
			$this->session->set_userdata($session_data); 
			redirect("user-settings/organisations"); 
		}

		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 2;

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
	
	function index(){
		$data = array();

		$user_id = $this->session->userdata('user_id');
		$organ_id  = $this->session->userdata('organ_id');

		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 2;

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


		if($can_view_list == "yes" || $access == "yes")
		{
			$data['title'] = 'Strategy';
			
			// load pitch models
			$this->load->model('Pitch_company_model', 'company');
			$this->load->model('Pitch_headline_model', 'headline');
			$this->load->model('Pitch_problem_model', 'problem');
			$this->load->model('Pitch_competition_model', 'competition');
			$this->load->model('Pitch_funding_model', 'funding');
			$this->load->model('Pitch_marketing_model', 'marketing');
			$this->load->model('Pitch_sales_model', 'sales');
			$this->load->model('Pitch_solution_model', 'solution');
			$this->load->model('Pitch_targetmarket_model', 'market');
			$this->load->model('Pitch_partners_model', 'partner');
			$this->load->model('Pitch_forecast_model', 'forecast');
			$this->load->model('Pitch_teamkey_model', 'pitch_teamkey');
			$this->load->model('Pitch_milestone_model', 'pitch_milestone');
			$this->load->model('Pitch_purpose_model', 'purpose');
			$this->load->model('Pitch_values_model', 'values');
			$this->load->model('Pitch_positioning_model', 'positioning');
			$this->load->model('Milestone_model', 'milestones');
			$this->load->model('Task_model', 'task');
			$this->load->model('Team_model', 'team');
			$this->load->model('Organisation_model');
			

			$data['company'] = $this->company->get_by('plan_id', $this->plan_id);
			$data['headline'] = $this->headline->get_by('plan_id', $this->plan_id);
			$data['purpose'] = $this->purpose->get_by('plan_id', $this->plan_id);
			$data['values'] = $this->values->get_by('plan_id', $this->plan_id);
			$data['positioning'] = $this->positioning->get_by('plan_id', $this->plan_id);
			$data['problem'] = $this->problem->get_by('plan_id', $this->plan_id);
			$data['solution'] = $this->solution->get_by('plan_id', $this->plan_id);
			$data['competition'] = $this->competition->get_many_by('plan_id', $this->plan_id);
			$data['targetmarket'] = $this->market->get_many_by('plan_id', $this->plan_id);
			$data['funding_needs'] = $this->funding->get_by('plan_id', $this->plan_id);
			$data['sales_channel'] = $this->sales->get_by('plan_id', $this->plan_id);
			$data['marketing'] = $this->marketing->get_by('plan_id', $this->plan_id);
			$data['partners'] = $this->partner->get_many_by('plan_id', $this->plan_id);
			$data['milestones'] = $this->milestones->get_upcoming_milestones($this->organ_id);
			$data['forecasts'] = $this->forecast->get_many_by('plan_id', $this->plan_id);
			$data['pitch_teamkey'] = $this->pitch_teamkey->get_many_by('plan_id', $this->plan_id);
			$data['pitch_milestone'] = $this->pitch_milestone->get_many_by('plan_id', $this->plan_id);
			$data['users'] = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);

			$this->load->view('pitch/index', $data);
		}

		if($no_access == "yes")
		{
			show_404_page("404_page" );
		}
		
		if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
		{
			$data['title'] = 'Strategy';
			
			// load pitch models
			$this->load->model('Pitch_company_model', 'company');
			$this->load->model('Pitch_headline_model', 'headline');
			$this->load->model('Pitch_problem_model', 'problem');
			$this->load->model('Pitch_competition_model', 'competition');
			$this->load->model('Pitch_funding_model', 'funding');
			$this->load->model('Pitch_marketing_model', 'marketing');
			$this->load->model('Pitch_sales_model', 'sales');
			$this->load->model('Pitch_solution_model', 'solution');
			$this->load->model('Pitch_targetmarket_model', 'market');
			$this->load->model('Pitch_partners_model', 'partner');
			$this->load->model('Pitch_forecast_model', 'forecast');
			$this->load->model('Pitch_teamkey_model', 'pitch_teamkey');
			$this->load->model('Pitch_milestone_model', 'pitch_milestone');
			$this->load->model('Pitch_purpose_model', 'purpose');
			$this->load->model('Pitch_values_model', 'values');
			$this->load->model('Pitch_positioning_model', 'positioning');
			$this->load->model('Milestone_model', 'milestones');
			$this->load->model('Task_model', 'task');
			$this->load->model('Team_model', 'team');
			$this->load->model('Organisation_model');
			

			$data['company'] = $this->company->get_by('plan_id', $this->plan_id);
			$data['headline'] = $this->headline->get_by('plan_id', $this->plan_id);
			$data['purpose'] = $this->purpose->get_by('plan_id', $this->plan_id);
			$data['values'] = $this->values->get_by('plan_id', $this->plan_id);
			$data['positioning'] = $this->positioning->get_by('plan_id', $this->plan_id);
			$data['problem'] = $this->problem->get_by('plan_id', $this->plan_id);
			$data['solution'] = $this->solution->get_by('plan_id', $this->plan_id);
			$data['competition'] = $this->competition->get_many_by('plan_id', $this->plan_id);
			$data['targetmarket'] = $this->market->get_many_by('plan_id', $this->plan_id);
			$data['funding_needs'] = $this->funding->get_by('plan_id', $this->plan_id);
			$data['sales_channel'] = $this->sales->get_by('plan_id', $this->plan_id);
			$data['marketing'] = $this->marketing->get_by('plan_id', $this->plan_id);
			$data['partners'] = $this->partner->get_many_by('plan_id', $this->plan_id);
			$data['milestones'] = $this->milestones->get_upcoming_milestones($this->organ_id);
			$data['forecasts'] = $this->forecast->get_many_by('plan_id', $this->plan_id);
			$data['pitch_teamkey'] = $this->pitch_teamkey->get_many_by('plan_id', $this->plan_id);
			$data['pitch_milestone'] = $this->pitch_milestone->get_many_by('plan_id', $this->plan_id);
			$data['users'] = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);

			$this->load->view('pitch/index', $data);
		}

	}

	function dashboard(){
		$this->load->model('Users_model');
		$data['title'] = "Dashboard";
		$this->load->view('account/dashboard', $data);
	}

	function edit($page=null){
		$data = $insert = array();
		$data['title'] = 'Pitch';
		$data['alert'] = "";
		
		$user_id = $this->user_id;
		$plan_id = $this->plan_id;
		$organ_id = $this->organ_id;

		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 2;

		$has_access = check_access($user_id, $organ_id, $tab_id);

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

		if($can_view_list == "yes")
		{
			redirect('pitch/index','refresh');
		}
		
		switch ($page) {
			case 'company':
				$this->load->model('Pitch_company_model', 'company');
				$data['title'] = 'Edit your strategy';
				
				$data['company'] = $this->company->get_many_by('plan_id', $plan_id);

				// post
				if($this->input->post()){
					$company['name'] = trim(htmlentities($this->input->post('company_name')));
					$company['plan_id'] = $plan_id;
					$company['updated_by'] = $user_id;
					
					// handle file uploading...
					if(! empty($_FILES['company_logo']['name'])){
						$config['upload_path'] = $this->config->item('upload_dir');
						$config['allowed_types'] = $this->config->item('upload_file_type');
						$config['encrypt_name'] = TRUE;
						$config['max_size'] = $this->config->item('upload_max_size'); // 2MB
		
						$this->upload->initialize($config);
		
						if ( $this->upload->do_upload('company_logo')) {
							$upload = array('upload_data' => $this->upload->data());
							$company['logo'] = $upload['upload_data']['file_name'];
						}
						else {
							$data['alert'] = $this->upload->display_errors();	
						}
					}

					if(count($data['company'])){
						if($this->company->update($data['company'][0]->id, $company))
							redirect('pitch/edit/vision');
						return;
					}
					else{
						if($this->company->insert($company))
							redirect('pitch/edit/vision');
						return;	
					}
					

					
				} // end post
				
				$this->load->view('pitch/edit/company', $data);
				break;
			
			case 'vision':
				$data['title'] = 'Edit your strategy';

				$this->load->model('Pitch_headline_model', 'headline');

				$data['headline'] = $this->headline->get_by('plan_id', $plan_id);

				//var_dump($data['headline']);

				if($this->input->post()){
					$headline['value'] = htmlentities($this->input->post('headline'));
					$headline['updated_by'] = $user_id;
					$headline['plan_id'] = $plan_id;

					
						if(count($data['headline'])){
							if($this->headline->update($data['headline']->id, $headline))
								redirect('pitch/edit/purpose');
							return;
						}
						else{
							if($this->headline->insert($headline))
								redirect('pitch/edit/purpose');
							return;
						}
					
				}

				$this->load->view('pitch/edit/vision', $data);
				break;

			case 'purpose':
				$data = array();
				$data['title'] = 'Edit your strategy';

				$this->load->model('Pitch_purpose_model', 'purpose');

				$data['purpose'] = $this->purpose->get_by('plan_id', $plan_id);

				//var_dump($data['headline']);

				if($this->input->post()){
					$purpose['value'] = htmlentities($this->input->post('purpose'));
					$purpose['updated_by'] = $user_id;
					$purpose['plan_id'] = $plan_id;

					
						if(count($data['purpose'])){
							if($this->purpose->update($data['purpose']->id, $purpose))
								redirect('pitch/edit/values');
							return;
						}
						else{
							if($this->purpose->insert($purpose))
								redirect('pitch/edit/values');
							return;
						}
					
				}

				$this->load->view('pitch/edit/purpose', $data);
				break;

			case 'values':
				$data = array();
				$data['title'] = 'Edit your strategy';

				$this->load->model('Pitch_values_model', 'values');

				$data['values'] = $this->values->get_by('plan_id', $plan_id);

				//var_dump($data['headline']);

				if($this->input->post()){
					$values['value'] = htmlentities($this->input->post('values_content'));
					$values['updated_by'] = $user_id;
					$values['plan_id'] = $plan_id;

					
						if(count($data['values'])){
							if($this->values->update($data['values']->id, $values))
								redirect('pitch/edit/positioning');
							return;
						}
						else{
							if($this->values->insert($values))
								redirect('pitch/edit/positioning');
							return;
						}
					
				}

				$this->load->view('pitch/edit/values', $data);
				break;

			case 'positioning':
				$data = array();
				$data['title'] = 'Edit your strategy';

				$this->load->model('Pitch_positioning_model', 'positioning');

				$data['positioning'] = $this->positioning->get_by('plan_id', $plan_id);

				//var_dump($data['headline']);

				if($this->input->post()){
					$positioning['value'] = htmlentities($this->input->post('positioning'));
					$positioning['updated_by'] = $user_id;
					$positioning['plan_id'] = $plan_id;

					
						if(count($data['positioning'])){
							if($this->positioning->update($data['positioning']->id, $positioning))
								redirect('pitch/edit/problem_solving');
							return;
						}
						else{
							if($this->positioning->insert($positioning))
								redirect('pitch/edit/problem_solving');
							return;
						}
					
				}

				$this->load->view('pitch/edit/positioning', $data);
				break;

			case 'problem_solving':
				$data = $problem = array();
				$data['title'] = 'Edit your strategy';

				$this->load->model('Pitch_problem_model', 'problem');

				$data['problems'] = $this->problem->get_by('plan_id', $plan_id);

				if($this->input->post()){
					$problems = $this->input->post('problem');

					$problem['plan_id'] = $plan_id;
					$problem['updated_by'] = $user_id;
					
					if($this->input->post('type') == 'list'){
						// short list
						$problem['type'] = 'list';
						$problem['list_value'] = serialize($problems);
					}
					else{
						// description
						$problem['type'] = 'desc';
						$problem['text_value'] = htmlentities($this->input->post('description'));
					}

					if(count($data['problems'])){
						if($this->problem->update($data['problems']->id, $problem)){
							redirect('pitch/edit/solution');
						}
					}
					else{
						if($this->problem->insert($problem)){
							redirect('pitch/edit/solution');
						}
					}
				}

				$this->load->view('pitch/edit/problem_solving', $data);
				break;

			case 'solution':
				$data = $solution = array();
				$data['title'] = 'Edit your strategy';

				$this->load->model('Pitch_solution_model', 'solution');

				$data['solutions'] = $this->solution->get_by('plan_id', $plan_id);

				if($this->input->post()){
					$solutions = $this->input->post('solution');
					$solution['plan_id'] = $plan_id;
					$solution['updated_by'] = $user_id;
					
					if($this->input->post('type') == 'list'){
						// short list
						$solution['type'] = 'list';
						$solution['list_value'] = serialize($solutions);
					}
					else{
						// description
						$solution['type'] = 'desc';
						$solution['text_value'] = htmlentities($this->input->post('description'));
					}

					if(count($data['solutions'])){
						if($this->solution->update($data['solutions']->id, $solution)){
							redirect('pitch/edit/target_market');
						}
					}
					else{
						if($this->solution->insert($solution)){
							redirect('pitch/edit/target_market');
						}
					}
				}


				$this->load->view('pitch/edit/solution', $data);
				break;

			case 'target_market':
				$data['title'] = 'Edit your strategy';
				$this->load->model('Pitch_targetmarket_model', 'targetmarket');

				// load Target market segment
				$data['segments'] = $this->targetmarket->get_many_by('plan_id', $plan_id);

				$this->load->view('pitch/edit/market', $data);
				break;

			case 'competition':
				$data['title'] = 'Edit your strategy';
				$this->load->model('Pitch_competition_model', 'competition');

				$data['competitors'] = $this->competition->get_many_by('plan_id', $plan_id);
				$this->load->view('pitch/edit/competition', $data);
				break;

			case 'funding_needs':
				$data['title'] = 'Edit your strategy';
				$funding = array();

				$this->load->model('Pitch_funding_model', 'funding');

				$data['funding_needs'] = $this->funding->get_by('plan_id', $plan_id);

				if($this->input->post())
				{
					$funding['plan_id'] = $plan_id;
					$funding['amount'] = $this->input->post('amount');
					$funding['text'] = $this->input->post('text');
					$funding['updated_by'] = $user_id;

					// check if already exists
					if(count($data['funding_needs']))
					{
						if($this->funding->update($data['funding_needs']->id, $funding))
						{
							redirect('pitch/edit/sales_channel');
						}
					}
					else{
						if($this->funding->insert($funding))
						{
							redirect('pitch/edit/sales_channel');
						}	
					}
					
				}

				$this->load->view('pitch/edit/funding', $data);
				break;

			case 'sales_channel':
				$data['title'] = 'Edit your strategy';
				$sales = array();

				$this->load->model('Pitch_sales_model', 'sales');
				$data['sales'] = $this->sales->get_by('plan_id', $plan_id);

				if($this->input->post())
				{
					$sales['type'] 			= $this->input->post('type');
					$sales['text_value'] 	= htmlentities($this->input->post('description'));
					$sales['list_value'] 	= serialize($this->input->post('sales'));
					$sales['updated_by'] 	= $user_id;
					$sales['plan_id'] 		= $plan_id;

					if(count($data['sales']))
					{
						// update
						if($this->sales->update($data['sales']->id, $sales))
						{
							redirect('pitch/edit/marketing_activities');
						}
					}
					else{
						// insert
						if($this->sales->insert($sales))
						{
							redirect('pitch/edit/marketing_activities');
						}
					}
				}

				$this->load->view('pitch/edit/sales', $data);
				break;

			case 'marketing_activities':
				$data['title'] = 'Edit your strategy';
				$marketing = array();

				$this->load->model('Pitch_marketing_model', 'marketing');

				$data['marketing'] = $this->marketing->get_by('plan_id', $plan_id);

				if($this->input->post())
				{
					$marketing['plan_id'] 		= $plan_id;
					$marketing['type'] 			= $this->input->post('type');
					$marketing['text_value'] 	= htmlentities($this->input->post('description'));
					$marketing['list_value'] 	= serialize($this->input->post('marketing'));
					$marketing['updated_by'] 	= $user_id;

					if(count($data['marketing']))
					{
						// update
						if($this->marketing->update($data['marketing']->id, $marketing))
						{
							// redirect('pitch/edit/forecast');
							redirect('pitch/edit/milestones');
						}
					}
					else{
						// insert
						if($this->marketing->insert($marketing))
						{
							// redirect('pitch/edit/forecast');
							redirect('pitch/edit/milestones');
						}
					}

				}


				$this->load->view('pitch/edit/marketing', $data);
				break;

			case 'forecast':
				$data['title'] = 'Edit your strategy';
				$data['response_msg'] = '';

				$this->load->model('Pitch_forecast_model', 'forecast');

				if($this->input->post())
				{

					$forecast['plan_id'] = $this->plan_id;
					$forecast['organ_id'] = $this->organ_id;
					$forecast['owner_id'] = $this->user_id;

					if($this->input->post('url') != '')
					{
						$forecast['url'] = $this->input->post('url');

						if($this->forecast->insert($forecast))
						{
							$data['response_msg'] = '<div class="alert alert-success">URL has been saved successfully.</div>';
						}
					}
					

					if(file_exists($_FILES['document']['tmp_name']))
					{
						// handle file uploading...
						if(! empty($_FILES['document']['name']))
						{
							$config['upload_path'] = $this->config->item('upload_docs_dir');
							$config['allowed_types'] = $this->config->item('upload_doc_file_type');
							$config['max_size'] = $this->config->item('upload_doc_max_size'); // 1GB
			
							$this->upload->initialize($config);
			
							if ( $this->upload->do_upload('document')) {
								$upload = array('upload_data' => $this->upload->data());
								$forecast['file'] = $upload['upload_data']['file_name'];
								$forecast['file_url'] = base_url('uploads/docs/'.$upload['upload_data']['file_name']);

								if($this->forecast->insert($forecast))
								{
									$data['response_msg'] = '<div class="alert alert-success">Document has been saved successfully.</div>';
								}
							}
							else {
								$data['response_msg'] = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';	
							}
						}// end file uploading
					}
				}


				$data['forecasts'] = $this->forecast->get_many_by('plan_id', $this->plan_id);
				$this->load->view('pitch/edit/forecast', $data);
				break;

			case 'milestones':
				$this->load->model('Milestone_model');
				$this->load->model('Pitch_milestone_model');
				$data['title'] = 'Edit your strategy';
				$data['late_milestones'] = $this->Milestone_model->get_late_milestones($this->organ_id);
				$data['upcoming_milestones'] = $this->Milestone_model->get_upcoming_milestones($this->organ_id);
				$data['milestones'] = $this->Pitch_milestone_model->get_many_by('plan_id', $this->plan_id);

				$this->load->view('pitch/edit/milestones', $data);
				break;

			case 'team_key_roles':
				$this->load->model('Pitch_teamkey_model');
				$this->load->model('Organisation_model');

				$data['title'] = 'Edit your strategy';
				$data['users'] = $this->Organisation_model->organisation_users($user_id, $organ_id);
				$data['teamkey'] = $this->Pitch_teamkey_model->get_many_by('plan_id', $this->plan_id);
				$this->load->view('pitch/edit/team', $data);
				break;

			case 'partners':
				$data['title'] = 'Edit your strategy';
				$resource = $response = array();

				$this->load->model('Pitch_partners_model', 'partners');
				$data['partners'] = $this->partners->get_many_by('plan_id', $plan_id);

				if($this->input->post())
				{
					$resource['name'] = $this->input->post('resource_name');
					$resource['description'] = $this->input->post('resource_description');
					$resource['plan_id'] = $plan_id;
					$resource['updated_by'] = $user_id;

					// handle file uploading...
					if(! empty($_FILES['resource_logo']['name']))
					{
						$config['upload_path'] = $this->config->item('upload_dir');
						$config['allowed_types'] = $this->config->item('upload_file_type');
						$config['encrypt_name'] = TRUE;
						$config['max_size'] = $this->config->item('upload_max_size'); // 2MB
		
						$this->upload->initialize($config);
		
						if ( $this->upload->do_upload('resource_logo')) {
							$upload = array('upload_data' => $this->upload->data());
							$resource['logo'] = $upload['upload_data']['file_name'];
						}
						else {
							$data['alert'] = $this->upload->display_errors();	
						}
					}// end file uploading

					if($this->partners->insert($resource)){
						redirect('pitch/edit/partners');					
					}
				}

				$this->load->view('pitch/edit/partners', $data);
				break;

			// default:
			// 	echo 'eh?';
			// 	break;
		}

		//$this->load->view('pitch/edit', $data);
	}

	/****************** MARKET SEGMENT ******************/

	function save_market_segment()
	{
		$segment = $return = array();

		if($this->input->post())
		{
			$this->load->model('Pitch_targetmarket_model', 'targetmarket');
			
			$segment['plan_id'] = $this->plan_id;
			$segment['data'] = serialize($_POST);
			$segment['updated_by'] = $this->user_id;

			if(trim($this->input->post('name_segment')) != '')
			{
				if($this->targetmarket->insert($segment))
				{
					$return['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Market segment successfuly added.</div>';
					$return['action'] = 'success';
				}	
			}
			else{
				$return['msg'] = '<div class="alert alert-danger">Segment name is required.</div>';
				$return['action'] = 'failed';
			}
		}

		echo json_encode($return);
	}

	// load ajax content
	function load_update_market_segment($segment_id)
	{
		$data = array();

		$this->load->model('Pitch_targetmarket_model', 'target_model');

		$data['segment_info'] = $this->target_model->get_by('id', $segment_id);
		$this->load->view('pitch/ajax/load_update_market_segment', $data);
	}

	function update_market_segment()
	{
		$segment = $return = array();

		if($this->input->post())
		{
			$segment_id = $this->input->post('id');

			$this->load->model('Pitch_targetmarket_model', 'targetmarket');
			
			$segment['data'] = serialize($_POST);
			$segment['updated_by'] = $this->user_id;

			if($this->targetmarket->update($segment_id, $segment))
			{
				$return['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Market segment successfuly updated.</div>';
				$return['action'] = 'success';
			}
		}
		echo json_encode($return);
	}

	function delete_market_segment(){
		$return = array();
		if($this->input->post()){
			$this->load->model('Pitch_targetmarket_model', 'targetmarket');
			$id = $this->input->post('id');

			if($this->targetmarket->delete($id)){
				$return['action'] = 'success';
			}
		}
		echo json_encode($return);
	}

	/**************** COMPETITOR ***************/

	function save_competitor()
	{
		$competitor = $return = array();

		if($this->input->post())
		{
			$this->load->model('Pitch_competition_model', 'competition');

			$competitor['name'] = $this->input->post('competitor_name');
			$competitor['advantage'] = $this->input->post('competitor_advantage');
			$competitor['plan_id'] = $this->plan_id;
			$competitor['updated_by'] = $this->user_id;

			if(trim($competitor['name']) != '')
			{
				if($this->competition->insert($competitor))
				{
					$return['action'] = 'success';
				}
			}
		}

		echo json_encode($return);
	}

	function load_update_competitor($competitor_id)
	{
		$this->load->model('Pitch_competition_model', 'competition');
		$data = array();
		$data['competitor'] = $this->competition->get_by('id', $competitor_id);
		$this->load->view('pitch/ajax/load_update_competitor', $data);
	}

	function update_competitor(){

		$update = $response = array();

		if($this->input->post())
		{
			$this->load->model('Pitch_competition_model', 'competition');
			$id = $this->input->post('id');
			$update['name'] = $this->input->post('competitor_name');
			$update['advantage'] = $this->input->post('competitor_advantage');
			$update['updated_by'] = $this->user_id;
			$update['updated'] = date('Y-m-d H:i:s');

			if($this->competition->update($id, $update))
			{
				$response['action'] = 'success';
			}
		}
		
		echo json_encode($response);
	}

	function delete_competitor()
	{
		$response = array();

		if($this->input->post())
		{
			$this->load->model('Pitch_competition_model', 'competitor');
			$compitetor_id = $this->input->post('id');

			if($this->competitor->delete($compitetor_id))
			{
				$response['action'] = 'success';
			}
		}
		echo json_encode($response);
	}

	/*********** PARTNERS & RESOURCE ************/

	function update_resource()
	{
		$update = array();
		if($this->input->post())
		{
			$this->load->model('Pitch_partners_model', 'partner');
			$partner_id = $this->input->post('id');

			$update['name'] = $this->input->post('resource_name');
			$update['description'] = $this->input->post('resource_description');
			$update['updated_by'] = $this->user_id;

			// handle file uploading...
			if(! empty($_FILES['resource_logo']['name']))
			{
				$config['upload_path'] = $this->config->item('upload_dir');
				$config['allowed_types'] = $this->config->item('upload_file_type');
				$config['encrypt_name'] = TRUE;
				$config['max_size'] = $this->config->item('upload_max_size'); // 2MB

				$this->upload->initialize($config);

				if ( $this->upload->do_upload('resource_logo')) {
					$upload = array('upload_data' => $this->upload->data());
					$update['logo'] = $upload['upload_data']['file_name'];
				}
				else {
					$data['alert'] = $this->upload->display_errors();	
				}
			}// end file uploading

			// update
			if($this->partner->update($partner_id, $update))
			{
				redirect('pitch/edit/partners');
			}
		}
	}

	function load_update_resource($id)
	{
		$this->load->model('Pitch_partners_model', 'partners');
		$data = array();
		$data['resource'] = $this->partners->get_by('id', $id);
		$this->load->view('pitch/ajax/load_update_resource', $data);
	}

	function delete_resource()
	{
		$response = array();
		if($this->input->post())
		{
			$this->load->model('Pitch_partners_model', 'partner');
			$resource_id = $this->input->post('id');

			if($this->partner->delete($resource_id))
			{
				$response['action'] = 'success';
				$response['msg'] = '<div class="alert alert-success"><i class="fa fa-info-circle"></i> Successfully deleted.</div>';
			}
		}
		echo json_encode($response);
	}

	// Delete forecast
	function delete_forecast()
	{	
		$response['action'] = '';
		if($this->input->post())
		{
			$this->load->model('Pitch_forecast_model', 'forecast');

			$forecast_id = $this->input->post('id');

			if($this->forecast->delete($forecast_id))
			{
				$response['action'] = 'success';
			}
		}
		echo json_encode($response);
	}

	function publish(){
		$data = $publish = array();
		$data['title'] = 'Publish your strategy';
		// load model
		$this->load->model('Pitch_published_model', 'pitch_publish');
		$data['mypitch'] = $this->pitch_publish->get_by('plan_id', $this->plan_id);

		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 2;

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

		if($can_view_list == "yes")
		{
			redirect('pitch/index','refresh');
		}


		if($this->input->post())
		{
			
			$is_publish = $this->input->post('is_publish');

			if($is_publish == 'yes')
			{
				$publish['plan_id'] = $this->plan_id;
				$publish['user_id'] = $this->user_id;
				$publish['code'] = random_string('alnum', 8);	

				if($this->pitch_publish->insert($publish))
				{
					/********************************************************************
					 * Mail notification when strategy is published
					 * added by: james
					 ********************************************************************/	
					
					$mail_notif = array();
					$mail_notif['name'] = 'Strategy published';
					$mail_notif['owner'] = $this->user_id; // current logged in
					$mail_notif['url'] = site_url('pitch/view/'.$publish['code']);
					// not for now
					//$this->mail_notification->send('strategy_published', array($this->user_id), $mail_notif);	
					
					// End: Mail notification
					redirect('pitch/publish');
				}
			}
			elseif($is_publish == 'no')
			{
				$id = $this->input->post('id');
				if($this->pitch_publish->delete($id))
				{
					redirect('pitch/publish');
				}
				// delete pitch published
			}
		}

		$this->load->view('pitch/publish', $data);
	}

	// View published pitch
	function view($code)
	{
		$data = array();
		$data['title'] = 'Your published strategy';

		$this->load->model('Pitch_published_model', 'pitch');
		$published_pitch = $this->pitch->get_by('code', $code);

		if(! empty($published_pitch))
		{
			// load pitch models
		// load pitch models
		$this->load->model('Pitch_company_model', 'company');
		$this->load->model('Pitch_headline_model', 'headline');
		$this->load->model('Pitch_problem_model', 'problem');
		$this->load->model('Pitch_competition_model', 'competition');
		$this->load->model('Pitch_funding_model', 'funding');
		$this->load->model('Pitch_marketing_model', 'marketing');
		$this->load->model('Pitch_sales_model', 'sales');
		$this->load->model('Pitch_solution_model', 'solution');
		$this->load->model('Pitch_targetmarket_model', 'market');
		$this->load->model('Pitch_partners_model', 'partner');
		$this->load->model('Pitch_forecast_model', 'forecast');
		$this->load->model('Pitch_teamkey_model', 'pitch_teamkey');
		$this->load->model('Pitch_milestone_model', 'pitch_milestone');
		$this->load->model('Pitch_purpose_model', 'purpose');
		$this->load->model('Pitch_values_model', 'values');
		$this->load->model('Pitch_positioning_model', 'positioning');
		$this->load->model('Milestone_model', 'milestones');
		$this->load->model('Task_model', 'task');
		$this->load->model('Team_model', 'team');
		$this->load->model('Organisation_model');
		

		$data['company'] = $this->company->get_by('plan_id', $this->plan_id);
		$data['headline'] = $this->headline->get_by('plan_id', $this->plan_id);
		$data['purpose'] = $this->purpose->get_by('plan_id', $this->plan_id);
		$data['values'] = $this->values->get_by('plan_id', $this->plan_id);
		$data['positioning'] = $this->positioning->get_by('plan_id', $this->plan_id);
		$data['problem'] = $this->problem->get_by('plan_id', $this->plan_id);
		$data['solution'] = $this->solution->get_by('plan_id', $this->plan_id);
		$data['competition'] = $this->competition->get_many_by('plan_id', $this->plan_id);
		$data['targetmarket'] = $this->market->get_many_by('plan_id', $this->plan_id);
		$data['funding_needs'] = $this->funding->get_by('plan_id', $this->plan_id);
		$data['sales_channel'] = $this->sales->get_by('plan_id', $this->plan_id);
		$data['marketing'] = $this->marketing->get_by('plan_id', $this->plan_id);
		$data['partners'] = $this->partner->get_many_by('plan_id', $this->plan_id);
		$data['milestones'] = $this->milestones->get_upcoming_milestones($this->organ_id);
		$data['forecasts'] = $this->forecast->get_many_by('plan_id', $this->plan_id);
		$data['pitch_teamkey'] = $this->pitch_teamkey->get_many_by('plan_id', $this->plan_id);
		$data['pitch_milestone'] = $this->pitch_milestone->get_many_by('plan_id', $this->plan_id);
		$data['users'] = $this->Organisation_model->organisation_users($this->user_id, $this->organ_id);


			$this->load->view('pitch/published_pitch', $data);
		}
		else{
			echo 'The pitch you requested is not found.';
			return;	
		}
	}

	function present()
	{
		$data = array();
		$data['title'] = 'Present';

		$this->load->view('pitch/present', $data);
	}

	function download_forecast($forecast_id)
	{	
		$this->load->model('Pitch_forecast_model', 'forecast');
		// organisation_member_exists
		$download = $this->forecast->download_forecast(decrypt($forecast_id), $this->user_id);

		if(count($download))
		{	
			//print_r($download);
			
			header( 'Cache-Control: public' );
			header( 'Content-Description: File Transfer' );
			header( "Content-Disposition: attachment; filename={$download[0]->file}" );
			header( 'Content-Transfer-Encoding: binary' );
			readfile( $download[0]->file_url );
			exit;
		    
		}
		else{
			die("You don't have permission to access this file.");
			exit;
		}
	}

	function hide_view()
	{
		$post = $this->input->post();
		$response = array();

		$plan_id = $post['plan_id'];

		if($post)
		{
			switch ($post['table']) {
				case 'pitch_company':
					$this->load->model('Pitch_company_model');

					$is_exist = $this->Pitch_company_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_company_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_company_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));
					}
					
					break;
				
				case 'pitch_competition':
					$this->load->model('Pitch_competition_model');

					$is_exist = $this->Pitch_competition_model->get_by('plan_id', $this->plan_id);
					
					if(count($is_exist) > 0){
						$this->Pitch_competition_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_competition_model->insert(array('hide' => $post['hide'], 'entered' => $this->user_id, 'plan_id' => $plan_id));	
					}

					break;

				case 'pitch_funding':
					$this->load->model('Pitch_funding_model');

					$is_exist = $this->Pitch_funding_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_funding_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_funding_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}

					break;

				case 'pitch_headline':
					$this->load->model('Pitch_headline_model');

					$is_exist = $this->Pitch_headline_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_headline_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_headline_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}

					break;

				case 'pitch_purpose':
					$this->load->model('Pitch_purpose_model');

					$is_exist = $this->Pitch_purpose_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_purpose_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_purpose_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}

					break;

				case 'pitch_values':
					$this->load->model('Pitch_values_model');

					$is_exist = $this->Pitch_values_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_values_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_values_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}

					break;

				case 'pitch_positioning':
					$this->load->model('Pitch_positioning_model');

					$is_exist = $this->Pitch_positioning_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_positioning_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_positioning_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));
					}

					break;

				case 'pitch_marketing':
					$this->load->model('Pitch_marketing_model');

					$is_exist = $this->Pitch_marketing_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_marketing_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_marketing_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}

					break;

				case 'pitch_partners':
					$this->load->model('Pitch_partners_model');

					$is_exist = $this->Pitch_partners_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_partners_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_partners_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));
					}

					break;

				case 'pitch_milestone':
					$this->load->model('Pitch_milestone_model');
					$is_exist = $this->Pitch_milestone_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_milestone_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));	
					}
					else{
						// add one
						$this->Pitch_milestone_model->insert(array(
							'plan_id' => $this->plan_id,
							'updated_by' => $this->user_id,
							'hide' => $post['hide']
						));
					}
					
					break;

				case 'pitch_problems':
					$this->load->model('Pitch_problem_model');

					$is_exist = $this->Pitch_problem_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_problem_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_problem_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}
					break;

				case 'pitch_sales':
					$this->load->model('Pitch_sales_model');

					$is_exist = $this->Pitch_sales_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_sales_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_sales_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}
					break;

				case 'pitch_solution':
					$this->load->model('Pitch_solution_model');

					$is_exist = $this->Pitch_solution_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){					
						$this->Pitch_solution_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_solution_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}
					break;

				case 'pitch_target_market':
					$this->load->model('Pitch_targetmarket_model');

					$is_exist = $this->Pitch_targetmarket_model->get_by('plan_id', $this->plan_id);

					if(count($is_exist) > 0){
						$this->Pitch_targetmarket_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));
					}
					else{
						$this->Pitch_targetmarket_model->insert(array('hide' => $post['hide'], 'plan_id' => $plan_id));	
					}
					break;

				case 'pitch_teamkey':
					$this->load->model('Pitch_teamkey_model');
					$teamkey_roles_exists = $this->Pitch_teamkey_model->get_many_by('plan_id', $this->plan_id);

					if(count($teamkey_roles_exists))
					{
						$this->Pitch_teamkey_model->update_by('plan_id', $plan_id, array('hide' => $post['hide']));	
					}
					else{
						// add
						$this->Pitch_teamkey_model->insert(array(
							'plan_id' => $this->plan_id,
							'updated_by' => $this->user_id,
							'hide' => $post['hide']
						));
					}
					break;
					
				default:
					break;
			}

			$response['action'] = 'success';
			echo json_encode($response);
		}
	}

	
}