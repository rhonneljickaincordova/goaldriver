<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan extends CI_Controller {

	var $plan_id = 0;
	var $user_id = 0;
	var $organ_id = 0;
	var	$readonly = "";
	var	$hidden = "";
	var	$has_access = "";

	function __construct(){
		parent::__construct();

		if(! $this->session->userdata('logged_in')) 
			redirect('account/sign_in');

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

		// load models
		$this->load->model('Chapter_model', 'chapter');
		$this->load->model('Section_model', 'section');
		$this->load->model('Subsection_model', 'subsection');
		$this->load->model('Subsection_comment_model', 'subsection_comment');
		$this->load->model('Plan_model', 'plan');
		$this->load->model('Plan_coverpage_model', 'coverpage');

		$this->load->helper('dompdf_helper');

		/** Implement permission trapping **/
		
		$tab_id  = 3;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1)
			{
				$this->readonly = "yes";
			}
			if($has_access[0]['hidden'] == 1)
			{
				$this->hidden = "yes";
			}
			if($has_access[0]['readwrite'] == 1)
			{
				$this->has_access = "yes";
			}
		}

		if($this->hidden == "yes")
		{
			redirect('dashboard','refresh');
		}

	}

	function index(){
		$data = array();

		$user_id = $this->session->userdata('user_id');
		$organ_id  = $this->session->userdata('organ_id');
		$tab_id  = 3;

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

		$data['title'] = 'Plan';
		// redirect to first positioned chapter
		$chapter = $this->chapter->get_first_position($this->plan_id);
		if(! is_null($chapter)){
			redirect('plan/chapter/'.encrypt($chapter->chapter_id));	
		}
		else{
			$data['chapters'] = $this->chapter->get_ordered_chapters($this->plan_id); // sidebar
			$this->load->view('plan/index', $data);
		}
		
	}

	function edit_plan_chapter(){
		
		$response = array();

		if($this->input->post()){
			$chapter_name = trim($this->input->post('name'));
			$chapter_id = $this->input->post('chapter_id');

			if(trim($chapter_name) != '' && $chapter_id != ''){
				if($this->chapter->update($chapter_id, array('title' => $chapter_name)))
				{
					$response['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Chapter name has been updated.</div>';
					$response['action'] = 'success';
				}
			}
			else{
				$response['msg'] = '<div class="alert alert-danger"><i class="fa fa-info-circle"></i> Please enter Chapter name.</div>';
				$response['action'] = 'failed';
			}
		}
		echo json_encode($response);
	}

	function add_plan_chapter(){
		
		$response = array();
		$chapter = array();



		if($this->input->post()){
			$title = trim($this->input->post('title'));
			$plan_id = $this->input->post('plan_id');
			
			if(trim($title) != ''){
				$chapter = array(
						'plan_id' => $plan_id,
						'title' => $title,
						'position' => $this->chapter->get_last_position(),
					);
				if($this->chapter->insert($chapter)){
					$response['msg'] = $title. ' chapter has been added';
					$response['action'] = 'success';
				}
			}
			else{
				$response['msg'] = 'Please enter a chapter name';
				$response['action'] = 'failed';
			}
		}

		echo json_encode($response);
	}


	function delete_plan_chapter(){
		
		$response = array();

		if($this->input->post()){
			if($this->chapter->delete($this->input->post('chapter_id'))){
				$response['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Chapter has been deleted</div>';
				$response['action'] = 'success';
			}
		}

		echo json_encode($response);
	}

	function sort_chapter(){
		

		if($this->input->post()){
			$i = 0;

			foreach ($_POST['item'] as $value) {
			    // Execute statement:
			    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
				$this->chapter->update($value, array('position' => $i));
			    $i++;
			}
		}
	}

	function sort_section(){
		
		if($this->input->post()){
			$i = 0;

			foreach ($_POST['item'] as $value) {
			    // Execute statement:
			    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
				$this->section->update($value, array('position' => $i));
			    $i++;
			}
		}
	}

	function sort_subsection(){
		
		if($this->input->post()){
			$i = 0;

			foreach ($_POST['item'] as $value) {
			    // Execute statement:
			    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
				$this->subsection->update($value, array('position' => $i));
			    $i++;
			}
		}
	}

	function chapter($chapter_id=null, $section_id=null, $subsection_id=null){
		
		if(is_null($chapter_id))
		{
			show_404_page("404_page");
			return;
		}

		$_chapter_id = decrypt($chapter_id);
		$_section_id = decrypt($section_id);
		$_sub_section_id = decrypt($subsection_id);
		
		// check authorize access
		if(! $this->auth->plan_access($chapter_id)){
			show_404_page("404_page");
			return;
		}

		if(! is_null($section_id)){
			$this->subsection->subsection_reorder($_section_id);	
		}
		

		// if($this->plan->validate_chapter_access($_chapter_id, $this->plan_id) < 1)
		// {
		// 	redirect('plan');
		// }
		
		$data = $update = array();

		$user_id = $this->session->userdata('user_id');
		$organ_id  = $this->session->userdata('organ_id');
		$tab_id  = 3;

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
		
		$plan_id = $this->plan_id;

		$data['sections'] = $this->section->get_ordered_section($_chapter_id);
		$data['chapters'] = $this->chapter->get_ordered_chapters($plan_id); // sidebar
		$data['subsections'] = $this->subsection->get_subsections_by_position($_section_id);
		
		
		$chapter_info = $this->chapter->get($_chapter_id);
		$section_info = $this->section->get($_section_id);

		$data['chapter_id'] = $_chapter_id;

		// chart types
		$data['charts'] = $this->plan->get_chart_types();

		if(! is_null($section_id)){
			$data['title'] = 'Edit your plan';

			$data['section_id'] = $_section_id;
			$data['chapter_id'] = $_chapter_id;

			$data['section_info'] = $this->section->get($_section_id);
			$data['btn_next'] = $this->section->nav_button('next', $section_info->position, $_chapter_id);
			$data['btn_prev'] = $this->section->nav_button('prev', $section_info->position, $_chapter_id);
		}
		else{
			$data['title'] = 'Edit your plan';
			
			$data['chapter_info'] = $this->chapter->get($_chapter_id);	
			$data['btn_next'] = $this->chapter->nav_button('next', $chapter_info->position, $this->plan_id);
			$data['btn_prev'] = $this->chapter->nav_button('prev', $chapter_info->position, $this->plan_id);
		}


		if($this->input->post()){
			$post_section_id = $this->input->post('section_id');
			$post_chapter_id = $this->input->post('chapter_id');

			$update['title'] = $this->input->post('section_title');
			$update['content'] = htmlentities($this->input->post('section_content'));

			if($update['title'] != ''){
				if($this->section->update($post_section_id, $update)){
					redirect('plan/chapter/'.$post_chapter_id.'/'.$post_section_id);
				}
				else{
					die('There was an error. go back');
				}
			}
		}
		
		if($subsection_id != NULL){
			$data['subsection_info'] = $this->subsection->get_by('subsection_id', $_sub_section_id);
			$data['btn_next'] = $this->subsection->next_button($_chapter_id, $_section_id, $_sub_section_id);
			$data['btn_prev'] = $this->subsection->prev_button($_chapter_id, $_section_id, $_sub_section_id);
			$this->load->view('plan/sub_section', $data);	
		}
		else{
			$this->load->view('plan/chapter', $data);	
		}
		
	}


	function add_section_chart($chapter_id, $section_id, $subsection_id){
		if($this->input->post())
		{
			$chart_id = $this->input->post('chart');
			if($this->plan->set_chart_type($chart_id, $subsection_id))
			{
				redirect('plan/chapter/'.$chapter_id.'/'.$section_id);
			}
		}
	}


	function add_section(){
		
		$section = $response = array();
		
		if($this->input->post()){
			$section['title'] = trim($this->input->post('title'));
			$section['chapter_id'] = $this->input->post('chapter_id');
			$section['plan_id'] = $this->input->post('plan_id');
			$section['position'] = $this->section->get_last_position();

			if(trim($section['title']) != '' AND isset($section['chapter_id']) AND isset($section['plan_id'])){
				if($this->section->insert($section)){
					$response['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Success, Section has been added.</div>';
					$response['action'] = 'success';
				}	
			}
			else{
				$response['msg'] = '<div class="alert alert-danger">Please enter Section name.</div>';
				$response['action'] = 'failed';
			}
			
		}

		echo json_encode($response);
	}


	function update_section(){
		
			
		$response = array();	
		if($this->input->post()){
			$title = trim($this->input->post('title'));
			$section_id = $this->input->post('section_id');

			if(trim($title) != ''){
				if($this->section->update($section_id, array('title' => $title))){
					$response['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Section has been updated.</div>';
					$response['action'] = 'success';
				}	
			}
			else{
				$response['msg'] = '<div class="alert alert-danger">Please enter Section name.</div>';
				$response['action'] = 'failed';
			}
		}

		echo json_encode($response);
	}


	function delete_section(){
		
		$response = array();

		if($this->input->post()){
			if($this->section->delete($this->input->post('section_id'))){
				$response['msg'] = '<div class="alert alert-success"><i class="fa fa-check"></i> Section has been deleted</div>';
				$response['action'] = 'success';
			}
		}

		echo json_encode($response);
	}


	// Change whats on this section
	function update_sections(){
		

		if($this->input->post()){
			$section_id = $this->input->post('section_id');
			
			$data = array();
			$data['info'] = $this->section->get_by('section_id', $section_id);
			$data['items'] = $this->subsection->get_many_by('section_id', $section_id);
			$this->load->view('plan/whats_on_this_section', $data);
		}
		return false;
	}
	
	function update_section_field(){

		if($this->input->post()){
			
			$section_id = $this->input->post('section_id');
			$field = $this->input->post('field');
			$content = htmlentities($this->input->post('content'));
			
			$this->section->update($section_id, array( $field => $content ));
		}
	}
	
	function update_subsection_data(){

		if($this->input->post()){
			$response = array();

			$subsection_id = $this->input->post('subsection_id');
			$field = $this->input->post('field'); // which field to update
			$content = htmlentities($this->input->post('content'));
			
			if($this->subsection->update($subsection_id, array( $field => $content )))
			{
				$response['status'] = 'success';
			}

			echo json_encode($response);
		}
	}
	
	function update_subsections(){
		
		if($this->input->post()){
			$response = array(); 
			
			
			$subsec_id = $this->input->post('subsection_id');
			$title = $this->input->post('title');
			
			if($this->subsection->update($subsec_id, array('title' => $title))){
				$response['status'] = 'success';
			}
			echo json_encode($response);
		}
	}
	
	function add_section_item($chart=null){
		
		if($this->input->post()){
			$response = array();
			
			if(is_null($chart)){
				$item_name = trim($this->input->post('item_name'));
				$section_id = $this->input->post('section_id');
				$type = $this->input->post('type');
				$plan_id = $this->input->post('plan_id');
				
				if($item_name != ''){
					$insert_plan_item = $this->subsection->insert(array(
						'plan_id' => $plan_id,
						'section_id' => $section_id,
						'title' => $item_name,
						'type' => $type
					));
					
					if($insert_plan_item){
						$response['status'] = 'success';
						$response['message'] = '<div class="alert alert-success">Success, Item has been added.</div>';
					}
				}
			}
			else{
				// add chart
				
				$insert_plan_item_chart = $this->subsection->insert(array(
					'plan_id' => $this->input->post('plan_id'),
					'section_id' => $this->input->post('section_id'),
					'title' => 'New Chart',
					'type' => $this->input->post('type')
				));
				
				if($insert_plan_item_chart){
					$response['status'] = 'success';
					$response['message'] = '<div class="alert alert-success">Success, Chart has been added.</div>';
				}
			}
			

			echo json_encode($response);
		}
	}
	
	function delete_sections_item(){
		
		if($this->input->post()){
			$response = array();
			
			$subsection_id = $this->input->post('subsection_id');
			
			if($this->subsection->delete($subsection_id)){
				$response['status'] = 'success';
			}
			echo json_encode($response);
		}
	}
	
	function submit_subsection_comment(){
		
		if($this->input->post()){
			$response = array();
			
			$subsection_id = $this->input->post('subsection_id');
			$comment = $this->input->post('comment');
			
			if($subsection_id != '' || $comment != ''){
				$comment = array(
					'subsection_id' => $subsection_id,
					'user_id' => $this->session->userdata('user_id'),
					'comment' => $comment
				);
				
				if($this->subsection_comment->insert($comment)){
					$response['status'] = 'success';
				}
			}
			echo json_encode($response);
		}
	}

	function submit_instruction(){
		
		$response = array();

		if($this->input->post()){
			$this->load->model('Section_model', 'section');
			$this->load->model('Subsection_model', 'subsection');

			$table 			= $this->input->post('table');
			$instructions 	= html_entity_decode($this->input->post('instructions'));
			$section_id 	= $this->input->post('sec_id');
			$uid 			= $this->input->post('uid');
			
			switch ($table) {
				case 'section':
					if($this->section->update($section_id, array('instructions' => $instructions))){
						$response['status'] = 'success';
						$response['text'] = html_entity_decode($instructions);
						$response['uid'] = $uid;
					}
					break;
				
				case 'subsection':
					if($this->subsection->update($section_id, array('instructions' => $instructions))){
						$response['status'] = 'success';
						$response['text'] = html_entity_decode($instructions);
						$response['uid'] = $uid;
					}
					break;
			} # end switch
		}
		echo json_encode($response);
	}

	function submit_example(){
		
		$response = array();

		if($this->input->post()){
			$this->load->model('Section_model', 'section');
			$this->load->model('Subsection_model', 'subsection');

			$table 			= $this->input->post('table');
			$example 		= html_entity_decode($this->input->post('example'));
			$section_id 	= $this->input->post('sec_id');
			$uid 			= $this->input->post('uid'); // unique div id
			
			switch ($table) {
				case 'section':
					if($this->section->update($section_id, array('example' => $example))){
						$response['status'] = 'success';
						$response['text'] = html_entity_decode($example);
						$response['uid'] = $uid;
					}

					break;
				
				case 'subsection':
					if($this->subsection->update($section_id, array('example' => $example))){
						$response['status'] = 'success';
						$response['text'] = html_entity_decode($example);
						$response['uid'] = $uid;
					}
					
					break;
			} # end switch
		}
		echo json_encode($response);
	}
	
	function delete_my_comment(){
		
		if($this->input->post()){
			$response = array();
			
			$this->load->model('Subsection_comment_model', 'subsection_comment');
			$comment_id = $this->input->post('comment_id');
			
			if($this->subsection_comment->delete($comment_id)){
				$response['status'] = 'success';
			}
		}
		echo json_encode($response);
	}

	function cover_page(){

		if($this->readonly == 'yes'){
			show_404_page('404_page');
			return;
		}
		
		$data = $cp = $update = array();
		$data['alert'] = "";

		$this->load->model('Chapter_model', 'chapter');
		$this->load->model('Plan_model', 'plan');
		$this->load->model('Plan_coverpage_model', 'cover_page');
		$this->load->model('Section_model', 'section');

		$plan_id = $this->plan_id;

		$this->form_validation->set_rules('company_name', 'Company Name', 'required');
		$this->form_validation->set_rules('contact_name', 'Contact Name', 'required');
		$this->form_validation->set_rules('contact_email', 'Contact Email', 'required');

		$data['title'] = 'Cover Page';
		$data['plan_id'] = $plan_id;
		$data['plan'] = $this->plan->get_many_by('plan_id', $plan_id); // coverpage info
		$data['cp_info'] = $this->cover_page->get_cover_page_info($plan_id); // coverpage info
		$data['chapters'] = $this->chapter->get_ordered_chapters($plan_id);


		if($this->input->post()){
			//print_r($_POST); die();
			$cp['plan_id'] 		= $this->input->post('plan_id');
			$cp['company_name'] = $this->input->post('company_name');
			$cp['slogan'] 		= $this->input->post('slogan');
			$cp['street_address'] = $this->input->post('address');
			$cp['state'] 		= $this->input->post('state');
			$cp['postal'] 		= $this->input->post('postal');
			$cp['city'] 		= $this->input->post('city');
			$cp['country'] 		= $this->input->post('country');
			$cp['contact_name'] = $this->input->post('contact_name');
			$cp['contact_email'] = $this->input->post('contact_email');
			$cp['contact_phone'] = $this->input->post('contact_phone');
			$cp['company_website'] = $this->input->post('company_website');
			$cp['confidentiality_message'] = $this->input->post('confidentiality_message');
			

			$cp_print_options = @$data['cp_info'][0]->print_options;
			$print_options = $cp_print_options ? unserialize($cp_print_options) : array(
																						'paper_size' => 'a4',
																						'spacing' => '1',
																						'is_plan_title' => '1',
																						'is_paging' => '1',
																						'page' => '1-10',
																						'is_confidential_msg' => '1',
																						'confidentiality_msg' => 'CONFIDENTIAL',
																						'is_toc' => '1'
																						);

			if($this->input->post('is_toc') == 'on')
			{
				if(count($print_options))
				{
				    $print_options['is_toc'] = 1;
				    $cp['print_options'] = serialize($print_options);
				}
				else{
					$cp['print_options'] = serialize(array('is_toc' => '1'));
				}
			}
			else{
				if(count($print_options))
				{
					if(array_key_exists('is_toc', $print_options)){
						unset($print_options['is_toc']);	
					}
				}

				$cp['print_options'] = serialize($print_options);
			}

			if ($this->form_validation->run() != FALSE){
				// Update if exists
				if($this->cover_page->is_exists($plan_id) > 0){
					$update_cover_page = $this->cover_page->update_by('plan_id', $plan_id, array(
							'company_name' 		=> $this->input->post('company_name'),
							'slogan' 			=> $this->input->post('slogan'),
							'street_address' 	=> $this->input->post('address'),
							'state' 			=> $this->input->post('state'),
							'postal' 			=> $this->input->post('postal'),
							'city' 				=> $this->input->post('city'),
							'country' 			=> $this->input->post('country'),
							'contact_name' 		=> $this->input->post('contact_name'),
							'contact_email' 	=> $this->input->post('contact_email'),
							'contact_phone' 	=> $this->input->post('contact_phone'),
							'company_website' 	=> $this->input->post('company_website'),
							'confidentiality_message' => $this->input->post('confidentiality_message'),
							'print_options' => $cp['print_options']
						));

					if($update_cover_page){
						if(! empty($_FILES['company_logo']['name'])){
							$config['upload_path'] = $this->config->item('upload_dir');
							$config['allowed_types'] = $this->config->item('upload_file_type');
							$config['encrypt_name'] = TRUE;
							$config['max_size'] = $this->config->item('upload_max_size'); // 2MB

							$this->upload->initialize($config);

							if ( $this->upload->do_upload('company_logo')) {
								$data = array('upload_data' => $this->upload->data());

								// print_r($data);die();
			                    $config['image_library'] = 'gd2';
			                    $config['create_thumb'] = TRUE;
			                    $config['image_library'] = 'gd2';
			                    $config['source_image'] = $data['upload_data']['full_path'];
			                    
			                    $this->image_lib->initialize($config);
			                    $this->image_lib->resize();

								$update['company_logo'] = $data['upload_data']['file_name'];
							}
							else {
								$data['alert'] = $this->upload->display_errors();	
							}
						}

						if(count($update)){ // company logo changed
							if($this->cover_page->update_by('plan_id', $plan_id, $update)){
								// $data['alert'] = '<div class="alert alert-success" role="alert">Success!, Cover page updated.</div>';	
								redirect('plan/cover_page');
							}	
						}
						else{
							// $data['alert'] = '<div class="alert alert-success" role="alert">Success!, Cover page updated.</div>';	
							redirect('plan/cover_page');
						}
						
					}
				}
				else{
					if($this->cover_page->insert($cp)){
						$cover_page_id = $this->db->insert_id();

						if(! empty($_FILES['company_logo']['name'])){
							$config['upload_path'] = $this->config->item('upload_dir');
							$config['allowed_types'] = $this->config->item('upload_file_type');
							$config['encrypt_name'] = TRUE;
							$config['max_size'] = $this->config->item('upload_max_size'); // 2MB

							$this->upload->initialize($config);

							if ( $this->upload->do_upload('company_logo')) {
								$data = array('upload_data' => $this->upload->data());

								// print_r($data);die();
			                    $config['image_library'] = 'gd2';
			                    $config['create_thumb'] = TRUE;
			                    $config['image_library'] = 'gd2';
			                    $config['source_image'] = $data['upload_data']['full_path'];
			                    
			                    $this->image_lib->initialize($config);
			                    $this->image_lib->resize();

								$update['company_logo'] = $data['upload_data']['file_name'];
							}
							else {
								$data['alert'] = $this->upload->display_errors();	
							}

							$this->cover_page->update($cover_page_id, $update);
						
						}
						// $data['alert'] = '<div class="alert alert-success" role="alert">Success!, Cover page saved.</div>';	
						redirect('plan/cover_page');
					}
				} // end else
			} // end validation
			else{
				$data['alert'] = '<div class="alert alert-danger" role="alert">'.validation_errors().'</div>';	
			}
		}

		$this->load->view('plan/cover_page', $data);
	}

	function is_activate_cover_page(){
		if($this->input->post())
		{
			$is_activate = $this->input->post('activate');
			if($is_activate == 1)
			{
				$activated = $this->plan->update($this->plan_id, array('is_coverpage' => 1));

				if($activated)
				{
					echo json_encode(array(
						'action' => 'success'
					));
				}
			}
			elseif($is_activate == 0)
			{
				$deactivated = $this->plan->update($this->plan_id, array('is_coverpage' => 0));

				if($deactivated)
				{
					$this->coverpage->delete_by('plan_id', $this->plan_id);
					echo json_encode(array(
						'action' => 'success'
					));
				}
			}
		}
	}

	function ajax_document_options(){
		$data = array();
		$data['print'] = $this->coverpage->get_by('plan_id', $this->plan_id);
		$this->load->view('plan/ajax/document_options', $data);
	}

	function ajax_document_options_save(){
		if($this->input->post())
		{
			$save_print_options = $this->coverpage->update_by('plan_id', $this->plan_id, array('print_options' => serialize($this->input->post())));
			if($save_print_options)
			{
				echo json_encode(array(
						'action' => 'success'
					));
			}
		}
	}

	function ajax_edit_comment($comment_id){
		$data = array();
		$data['comment'] = $this->subsection_comment->get_by('id', $comment_id);
		$this->load->view('plan/ajax/edit_comment', $data);
	}

	function ajax_edit_comment_save(){
		$post = $this->input->post();

		$update = $this->subsection_comment->update($post['comment_id'], array('comment' => $post['comment']));

		if($update)
		{
			echo json_encode(array(
						'action' => 'success'
					));
		}
	}

	function download($type=NULL){

		if($this->readonly == 'yes'){
			show_404_page('404_page');
			return;
		}

		$data = array();
		$data['title'] = 'Download';
		$data['print'] = $this->coverpage->get_by('plan_id', $this->plan_id);
		$data['chapters'] = $this->chapter->get_ordered_chapters($this->plan_id);

		// download pdf/doc
		if(! is_null($type))
		{
			$data['coverpage'] = $this->coverpage->get_cover_page_info($this->plan_id);
			$data['chapters'] = $this->chapter->get_ordered_chapters($this->plan_id);

			if(! count($data['coverpage']))
			{
				$session_data = array(
					'res_code' => "warning",
					'res_message' => "Please setup a cover page to download the PDF."
				); 
				$this->session->set_flashdata($session_data); 
				redirect("plan/cover_page"); 
			}

			switch ($type) {
				case 'pdf':
					
					$print_options = unserialize($data['coverpage'][0]->print_options);
					
					$html = $this->load->view('plan/download_file', $data, true);
					pdf_create($html, $data['coverpage'][0]->company_name, $print_options['paper_size']);

					#$this->load->view('plan/download_file', $data);
					break;

				case 'doc':
					
					//$this->load->view('plan/download_file', $data);
					break;			
			}
		}

		$this->load->view('plan/download', $data);
	}

	function comments(){
		$data = array();
		$data['title'] = 'Comments';
		$data['comments'] = $this->subsection_comment->get_all_comments($this->plan_id);

		$tab_id  = 3;

		$has_access = check_access($this->user_id, $this->organ_id, $tab_id);

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

		$this->load->view('plan/comments', $data);
	}

	function sub_section_reorder($direction){
		if($_POST) {
			$resultArray = array();

			$subsection_id = $this->input->post('subsection_id');
			$section_id = $this->input->post('section_id');
			$position = $this->input->post('position');

			$result = $this->subsection->subsection_move($subsection_id, $position, strtoupper($direction));

			if(count($result)) {
				$resultArray['status'] = 'success';
			}

			echo json_encode($resultArray);
		}
	}

	function section_reorder($direction){
		if($_POST) {
			$resultArray = array();

			$section_id = $this->input->post('section_id');
			$position = $this->input->post('position');

			$result = $this->subsection->section_move($section_id, $position, strtoupper($direction));

			if(count($result)) {
				$resultArray['status'] = 'success';
			}

			echo json_encode($resultArray);
		}
		
	}


	// load chapter ajax
	function load_chapter($chapter_id, $plan_id, $section_id){
		$data['sections'] = $this->section->get_ordered_section($chapter_id);
		$data['chapters'] = $this->chapter->get_ordered_chapters($plan_id); // sidebar
		$data['subsections'] = $this->subsection->get_subsections_by_position($section_id);
		$data['section_info'] = $this->section->get($section_id);
		$data['chapter_info'] = $this->chapter->get($chapter_id);	
		$data['section_id'] = $section_id;
		$this->load->view('includes/chapter', $data);
	}

	function load_section($chapter_id, $plan_id, $section_id){
		$data['sections'] = $this->section->get_ordered_section($chapter_id);
		$data['section_info'] = $this->section->get($section_id);
		$data['chapter_info'] = $this->chapter->get($chapter_id);	
		$data['chapters'] = $this->chapter->get_ordered_chapters($plan_id); // sidebar
		$data['subsections'] = $this->subsection->get_subsections_by_position($section_id);
		$data['section_id'] = $section_id;
		$this->load->view('includes/section', $data);
	}

	function script_load(){
		$data['sections'] = $this->section->get_ordered_section($chapter_id);
		$data['section_info'] = $this->section->get($section_id);
		$data['chapter_info'] = $this->chapter->get($chapter_id);	
		$data['chapters'] = $this->chapter->get_ordered_chapters($plan_id); // sidebar
		$data['subsections'] = $this->subsection->get_subsections_by_position($section_id);
		$data['section_id'] = $section_id;
		$this->load->view('includes/section-footer');
	}
}