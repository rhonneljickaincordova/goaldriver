<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Canvases extends CI_Controller 
{
	var $user_id;
	var $organ_id;

	public function __construct()
	{
		parent::__construct();

		if(! $this->session->userdata('logged_in'))
			redirect('account/logout');

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

		$this->load->model('Canvases_model', 'canvas');


		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 8;

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

	public function index()
	{
		$user_id = $this->session->userdata('user_id');
		$organ_id  = $this->session->userdata('organ_id');

		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 8;

		$has_access = check_access($user_id, $organ_id, $tab_id);
		
		$data = array();

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
			$data['title'] = 'Canvases';
			$data['canvases'] = $this->canvas->get_cavanases();
			$this->load->view('canvases/index', $data);
		}
		
		if($no_access == "yes")
		{
			show_404_page("404_page" );
		}
		
		if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
		{
			$data['title'] = 'Canvases';
			$data['canvases'] = $this->canvas->get_cavanases();
			$this->load->view('canvases/index', $data);
		}
	}

	public function create()
	{
		$data = array();
		$data['title'] = 'Create new canvas';
		$canvas_id = '';
		$canvas_type = '';
		$error = array();

		if($this->input->post())
		{
			$name = $this->input->post('name');
			$type = $this->input->post('type');

			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('type', 'Type', 'required');

			if ($this->form_validation->run() !== FALSE)
            {
            	$canvas_data = array(
            			'name' => $name,
            			'type' => $type,
            			'user_id' => $this->user_id,
            			'organ_id' => $this->organ_id,
            			'entered' => date('Y-m-d H:i:s'),
            			'entered_by' => $this->user_id
					);

                switch ($type) {
                	case 'canvas_business_model':
                		$canvas_id = $this->canvas->add_business_model($canvas_data);
                		$canvas_type = 'business';
                		if(! $canvas_id) $error[] = 1;
                		break;
                	
                	case 'canvas_personal_goals':
                		$canvas_id = $this->canvas->add_personal_goals($canvas_data);
                		$canvas_type = 'personal';
                		if(! $canvas_id) $error[] = 2;
                		break;
                	
                	case 'canvas_lean':
                		$canvas_id = $this->canvas->add_lean($canvas_data);
                		$canvas_type = 'lean';
                		if(! $canvas_id) $error[] = 3;
                		break;

                	case 'canvas_kpi':
                		$canvas_id = $this->canvas->add_kpi($canvas_data);
                		$canvas_type = 'kpi';
                		if(! $canvas_id) $error[] = 4;
                		break;
                }

                if(count($error) < 1)
                {
                	//redirect to canvas page
                	redirect('canvases/edit_canvas/'.$canvas_type.'/'.encrypt($canvas_id));
                }
            }
		}

		$this->load->view('canvases/create', $data);
	}

	public function edit_canvas($type=null, $id=null)
	{
		$data = array();
		$canvas_id = decrypt($id);

		// check if the requested canvas exists.
		switch ($type) {
			case 'business':
			$is_exists = $this->canvas->is_canvas_exists('canvas_business_model', $canvas_id);
			break;

			case 'personal':
			$is_exists = $this->canvas->is_canvas_exists('canvas_personal_goals', $canvas_id);
			break;

			case 'lean':
			$is_exists = $this->canvas->is_canvas_exists('canvas_lean', $canvas_id);
			break;

			case 'kpi':
			$is_exists = $this->canvas->is_canvas_exists('canvas_kpi', $canvas_id);
			break;
		}

		if(is_null($id) || is_null($type) || false == $is_exists)
		{
			show_404_page("404_page");
			return;
		}

		$data['users'] = $this->canvas->get_canvas_users();

		// check authorize access
		switch ($type) {
			case 'business':
			$data['canvas'] = $this->canvas->get_business_model($canvas_id);
			$data['owner'] = $this->canvas->get_canvas_info('canvas_business_model', 'user_id', $canvas_id);
			$data['shared_users'] = $this->canvas->get_canvas_shared_users('canvas_bm_users', $canvas_id);
			$data['title'] = 'Edit canvas';
			$data['id'] = $canvas_id;
			$view = 'canvases/type/business_model';
			break;

			case 'personal':
			$data['canvas'] = $this->canvas->get_personal_goals($canvas_id);
			$data['owner'] = $this->canvas->get_canvas_info('canvas_personal_goals', 'user_id', $canvas_id);
			$data['shared_users'] = $this->canvas->get_canvas_shared_users('canvas_pg_users', $canvas_id);
			$data['title'] = 'Edit canvas';
			$data['id'] = $canvas_id;
			$view = 'canvases/type/personal_goals';
			break;

			case 'lean':
			$data['canvas'] = $this->canvas->get_lean($canvas_id);
			$data['owner'] = $this->canvas->get_canvas_info('canvas_lean', 'user_id', $canvas_id);
			$data['shared_users'] = $this->canvas->get_canvas_shared_users('canvas_lean_users', $canvas_id);
			$data['title'] = 'Edit canvas';
			$data['id'] = $canvas_id;
			$view = 'canvases/type/lean';
			break;	

			case 'kpi':
			$data['canvas'] = $this->canvas->get_kpi($canvas_id);
			$data['owner'] = $this->canvas->get_canvas_info('canvas_kpi', 'user_id', $canvas_id);
			$data['shared_users'] = $this->canvas->get_canvas_shared_users('canvas_kpi_users', $canvas_id);
			$data['title'] = 'Edit canvas';
			$data['id'] = $canvas_id;
			$view = 'canvases/type/kpi';
			break;			
		}

		if($this->auth->canvas_access($type, $canvas_id))
		{
			$this->load->view($view, $data);	
		}
		else{
			show_404_page("404_page");
		}
		
	}

	function delete_canvas($type=null, $id=null)
	{
		if(is_null($id) || is_null($type))
		{
			show_404_page("404_page");
			return;
		}

		// check if the request is the owner of this canvas
		switch ($type) {
			case 'business':
				$table = 'canvas_business_model';
				break;
			
			case 'personal':
				$table = 'canvas_personal_goals';
				break;

			case 'lean':
				$table = 'canvas_lean';
				break;

			case 'kpi':
				$table = 'canvas_kpi';
				break;
		}
		
		if($this->canvas->is_exists_canvas_user($table, $this->user_id))
		{
			$canvas_id = decrypt($id);
			if($this->canvas->delete_canvas($type, $canvas_id))
			{
				redirect('canvases');
			}
		}
		else{
			show_404_page("404_page");
			return;
		}
	}

	function update_canvas_data()
	{
		$result = array();
		$post = $this->input->post();

		$canvas_id = $post['id'];
		$table = $post['table'];
		$field = $post['field'];
		$data = $post['data'];

		switch ($table) {
			case 'canvas_business_model':
				$type = 'business';
				break;
			
			case 'canvas_lean':
				$type = 'lean';
				break;
			
			case 'canvas_personal_goals':
				$type = 'personal';
				break;
		}

		if($this->canvas->update_canvas($canvas_id, $table, $field, $data))
		{
			$result['response'] = 'success';
		}

		echo json_encode($result);
	}

	function update_canvas_name($table)
	{
		$result = array();

		$post = $this->input->post();

		if($post)
		{
			$id = $post['id'];
			$name = $post['name'];

			switch ($table) {
				case 'business':
					if($this->canvas->update_business_model_name($id, $name))
					{
						$result['response'] = 'success';
					}
					break;
				case 'lean':
					if($this->canvas->update_lean_name($id, $name))
					{
						$result['response'] = 'success';
					}
					break;
				case 'personal':
					if($this->canvas->update_personal_goals_name($id, $name))
					{
						$result['response'] = 'success';
					}
					break;
				case 'kpi':
					if($this->canvas->update_kpi_name($id, $name))
					{
						$result['response'] = 'success';
					}
					break;
			}
		}
		echo json_encode($result);
	}


	function save_canvas_items()
	{
		// echo '<pre>';
		// print_r($_POST);
		// die();

		$error = array(); 
		$post = $this->input->post();
		$table = $post['table'];
		$canvas_id = $post['canvas_id'];
		$canvas_users = array();

		$current_shared_users = array();

		if($post)
		{
			// Canvas users data
			$shared_users = @$post['share_to'];
			$canvas_users['canvas_id'] 	= $canvas_id;
			$canvas_users['entered_on'] = date('Y-m-d H:i:s');

			// $canvas_shared_users = $this->canvas->get_canvas_shared_users('canvas_bm_users', $canvas_id);

			// foreach($canvas_shared_users as $csu)
			// {
			// 	$current_shared_users[] = $csu->user_id;
			// }

			// echo '<pre>';
			// print_r(array_diff($current_shared_users, $shared_users));

			// die();

			switch ($table) {
				case 'canvas_business_model':
					$type = 'business';

					if($this->canvas->is_canvas_exists_in_canvas_users('canvas_bm_users', $canvas_id))
					{
						// update
						$canvas_shared_users = $this->canvas->get_canvas_shared_users('canvas_bm_users', $canvas_id);

						foreach($canvas_shared_users as $csu)
						{
							$current_shared_users[] = $csu->user_id;
						}

						$users_to_remove = @array_diff($current_shared_users, $shared_users);
						
						if($users_to_remove)
						{
							foreach($users_to_remove as $utr_id)
							{
								$this->canvas->delete_canvas_user('canvas_bm_users', $utr_id);
							}
						}
						else{
							if(count($shared_users)){
								foreach ($shared_users as $user) {
									if(! $this->canvas->is_exists_canvas_user('canvas_bm_users', $user))
									{
										$canvas_users['user_id'] = $user;
										$this->canvas->save_canvas_users('canvas_bm_users', $canvas_users);
									}
								}
							}
							else{
								$this->canvas->delete_all_canvas_users('canvas_bm_users', $canvas_id);
							}
						}
					
					}
					else{
						// insert
						if(count($shared_users)){
							foreach ($shared_users as $user) {
								$canvas_users['user_id'] = $user;
								$this->canvas->save_canvas_users('canvas_bm_users', $canvas_users);
							}
						}
					}

					$key_partners 			= @$post['key_partners'][0] != '' ? json_encode(@$post['key_partners']) : NULL;
					$key_activities 		= @$post['key_activities'][0] != '' ? json_encode(@$post['key_activities']) : NULL;
					$key_resources 			= @$post['key_resources'][0] != '' ? json_encode(@$post['key_resources']) : NULL;
					$value_propositions 	= @$post['value_propositions'][0] != '' ? json_encode(@$post['value_propositions']) : NULL;
					$customer_relationships = @$post['customer_relationships'][0] != '' ? json_encode(@$post['customer_relationships']) : NULL;
					$channels 				= @$post['channels'][0] != '' ? json_encode(@$post['channels']) : NULL;
					$customer_segments 		= @$post['customer_segments'][0] != '' ? json_encode(@$post['customer_segments']) : NULL;
					$cost_structure 		= @$post['cost_structure'][0] != '' ? json_encode(@$post['cost_structure']) : NULL;
					$revenue_streams 		= @$post['revenue_streams'][0] != '' ? json_encode(@$post['revenue_streams']) : NULL;

					$data = array(
						'key_partners' => $key_partners,
						'key_activities' => $key_activities,
						'key_resources' => $key_resources,
						'value_propositions' => $value_propositions,
						'customer_relationships' => $customer_relationships,
						'channels' => $channels,
						'customer_segments' => $customer_segments,
						'cost_structure' => $cost_structure,
						'revenue_streams' => $revenue_streams,
						'updated' => date('Y-m-d H:i:s'),
						'updated_by' => $this->user_id
					);

					if(! $this->canvas->update_canvas($canvas_id, $table, $data))
						$error[] = 1;

					break;
				
				case 'canvas_lean':
					$type = 'lean';

					if($this->canvas->is_canvas_exists_in_canvas_users('canvas_lean_users', $canvas_id))
					{
						// update
						$canvas_shared_users = $this->canvas->get_canvas_shared_users('canvas_lean_users', $canvas_id);

						foreach($canvas_shared_users as $csu)
						{
							$current_shared_users[] = $csu->user_id;
						}

						$users_to_remove = @array_diff($current_shared_users, $shared_users);
						
						if($users_to_remove)
						{
							foreach($users_to_remove as $utr_id)
							{
								$this->canvas->delete_canvas_user('canvas_lean_users', $utr_id);
							}
						}
						else{
							if(count($shared_users)){
								foreach ($shared_users as $user) {
									if(! $this->canvas->is_exists_canvas_user('canvas_lean_users', $user))
									{
										$canvas_users['user_id'] = $user;
										$this->canvas->save_canvas_users('canvas_lean_users', $canvas_users);
									}
								}
							}
							else{
								$this->canvas->delete_all_canvas_users('canvas_lean_users', $canvas_id);
							}
						}
					}
					else{
						// insert
						if(count($shared_users)){
							foreach ($shared_users as $user) {
								$canvas_users['user_id'] = $user;
								$this->canvas->save_canvas_users('canvas_lean_users', $canvas_users);
							}
						}
					}

					$problem 					= @$post['problem'][0] != '' ? json_encode(@$post['problem']) : NULL;
					$solution 					= @$post['solution'][0] != '' ? json_encode(@$post['solution']) : NULL;
					$key_metrics 				= @$post['key_metrics'][0] != '' ? json_encode(@$post['key_metrics']) : NULL;
					$unique_value_propositions 	= @$post['unique_value_propositions'][0] != '' ? json_encode(@$post['unique_value_propositions']) : NULL;
					$unfair_advantage 			= @$post['unfair_advantage'][0] != '' ? json_encode(@$post['unfair_advantage']) : NULL;
					$channels 					= @$post['channels'][0] != '' ? json_encode(@$post['channels']) : NULL;
					$customer_segments 			= @$post['customer_segments'][0] != '' ? json_encode(@$post['customer_segments']) : NULL;
					$cost_structure 			= @$post['cost_structure'][0] != '' ? json_encode(@$post['cost_structure']) : NULL;
					$revenue_streams 			= @$post['revenue_streams'][0] != '' ? json_encode(@$post['revenue_streams']) : NULL;

					$data = array(
						'problem' => $problem,
						'solution' => $solution,
						'key_metrics' => $key_metrics,
						'unique_value_propositions' => $unique_value_propositions,
						'unfair_advantage' => $unfair_advantage,
						'channels' => $channels,
						'customer_segments' => $customer_segments,
						'cost_structure' => $cost_structure,
						'revenue_streams' => $revenue_streams,
						'updated' => date('Y-m-d H:i:s'),
						'updated_by' => $this->user_id
					);

					if(! $this->canvas->update_canvas($canvas_id, $table, $data))
						$error[] = 1;
					break;

				case 'canvas_personal_goals':
					$type = 'personal';

					if($this->canvas->is_canvas_exists_in_canvas_users('canvas_pg_users', $canvas_id))
					{
						// update
						$canvas_shared_users = $this->canvas->get_canvas_shared_users('canvas_pg_users', $canvas_id);

						foreach($canvas_shared_users as $csu)
						{
							$current_shared_users[] = $csu->user_id;
						}

						$users_to_remove = @array_diff($current_shared_users, $shared_users);
						
						if($users_to_remove)
						{
							foreach($users_to_remove as $utr_id)
							{
								$this->canvas->delete_canvas_user('canvas_pg_users', $utr_id);
							}
						}
						else{
							if(count($shared_users)){
								foreach ($shared_users as $user) {
									if(! $this->canvas->is_exists_canvas_user('canvas_pg_users', $user))
									{
										$canvas_users['user_id'] = $user;
										$this->canvas->save_canvas_users('canvas_pg_users', $canvas_users);
									}
								}
							}
							else{
								$this->canvas->delete_all_canvas_users('canvas_pg_users', $canvas_id);
							}
						}
					}
					else{
						// insert
						if(count($shared_users)){
							foreach ($shared_users as $user) {
								$canvas_users['user_id'] = $user;
								$this->canvas->save_canvas_users('canvas_pg_users', $canvas_users);
							}
						}
					}

					$why 			= @$post['why'][0] != '' ? json_encode(@$post['why']) : NULL;
					$health 		= @$post['health'][0] != '' ? json_encode(@$post['health']) : NULL;
					$relationships 	= @$post['relationships'][0] != '' ? json_encode(@$post['relationships']) : NULL;
					$balance 		= @$post['balance'][0] != '' ? json_encode(@$post['balance']) : NULL;
					$money 			= @$post['money'][0] != '' ? json_encode(@$post['money']) : NULL;
					$activities 	= @$post['activities'][0] != '' ? json_encode(@$post['activities']) : NULL;
					$values 		= @$post['values'][0] != '' ? json_encode(@$post['values']) : NULL;

					$data = array(
						'why' => $why,
						'health' => $health,
						'relationships' => $relationships,
						'balance' => $balance,
						'money' => $money,
						'activities' => $activities,
						'values' => $values,
						'updated' => date('Y-m-d H:i:s'),
						'updated_by' => $this->user_id
					);

					if(! $this->canvas->update_canvas($canvas_id, $table, $data))
						$error[] = 1;

					break;

				case 'canvas_kpi':
					$type = 'kpi';

					if($this->canvas->is_canvas_exists_in_canvas_users('canvas_kpi_users', $canvas_id))
					{
						// update
						$canvas_shared_users = $this->canvas->get_canvas_shared_users('canvas_kpi_users', $canvas_id);

						foreach($canvas_shared_users as $csu)
						{
							$current_shared_users[] = $csu->user_id;
						}

						$users_to_remove = @array_diff($current_shared_users, $shared_users);
						
						if($users_to_remove)
						{
							foreach($users_to_remove as $utr_id)
							{
								$this->canvas->delete_canvas_user('canvas_kpi_users', $utr_id);
							}
						}
						else{
							if(count($shared_users)){
								foreach ($shared_users as $user) {
									if(! $this->canvas->is_exists_canvas_user('canvas_kpi_users', $user))
									{
										$canvas_users['user_id'] = $user;
										$this->canvas->save_canvas_users('canvas_kpi_users', $canvas_users);
									}
								}
							}
							else{
								$this->canvas->delete_all_canvas_users('canvas_kpi_users', $canvas_id);
							}
						}
					}
					else{
						// insert
						if(count($shared_users)){
							foreach ($shared_users as $user) {
								$canvas_users['user_id'] = $user;
								$this->canvas->save_canvas_users('canvas_kpi_users', $canvas_users);
							}
						}
					}

					$sales 		= @$post['sales'][0] != '' ? json_encode(@$post['sales']) : NULL;
					$operations = @$post['operations'][0] != '' ? json_encode(@$post['operations']) : NULL;
					$customers 	= @$post['customers'][0] != '' ? json_encode(@$post['customers']) : NULL;
					$marketing 	= @$post['marketing'][0] != '' ? json_encode(@$post['marketing']) : NULL;
					$people 	= @$post['people'][0] != '' ? json_encode(@$post['people']) : NULL;
					

					$data = array(
						'sales' => $sales,
						'operations' => $operations,
						'customers' => $customers,
						'marketing' => $marketing,
						'people' => $people,
						'updated' => date('Y-m-d H:i:s'),
						'updated_by' => $this->user_id
					);

					if(! $this->canvas->update_canvas($canvas_id, $table, $data))
						$error[] = 1;

					break;
			}

			if(! count($error))
			{
				redirect('canvases/edit_canvas/'.$type.'/'.encrypt($canvas_id));
			}

		}
	}
}