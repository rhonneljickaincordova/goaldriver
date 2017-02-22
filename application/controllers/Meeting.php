<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Meeting extends CI_Controller
{
	var $response_code = 1; //false or error default
	var $response_message = "";
	var $response_data = array();
	var $latest_id = 0;

	var $plan_id = 0;
	var $user_id = 0;
	var $organ_id = 0;

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

		$this->load->model("meeting_model");
		$this->load->model("users_model");
		$this->load->model('Schedule_model');
		$this->load->model('Task_model');
		$this->load->model('Milestone_model');
		$this->load->model('Organisationusers_model');
		
		$this->load->helper('dompdf_helper');


		/** Implement permission trapping **/
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 6;

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
		$tab_id  = 6;

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

		if($can_view_list == "yes")
		{
			$data['title'] = 'Meetings';
			$data['meetings'] = $this->meeting_model->list_past_meetings();
			$this->load->view('meeting/index', $data);
		}

		if($no_access == "yes")
		{
			show_404_page("404_page" );
		}

		if($access == "yes")
		{
			$data['title'] = 'Meetings';
			$data['meetings'] = $this->meeting_model->list_past_meetings();
			$this->load->view('meeting/index', $data);
		}
		
		if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
		{
			$data['title'] = 'Meetings';
			$data['meetings'] = $this->meeting_model->list_past_meetings();
			$this->load->view('meeting/index', $data);
		}

	}


	public function form_email_temp($meeting_id, $message="", $recipients="", $return=false)
	{
			$id = get_id_from_code($meeting_id);
			$informations = $this->meeting_model->get_meeting_info($id);

			$data['informations'] = $informations;
			$data['message'] = $message;
		    $data['topics'] = $this->meeting_model->get_meeting_topics($id);

			$content = '';

			$data['title'] = 'Meeting Agenda';
			$data['meeting_id'] = $meeting_id;
			$data['recipients'] = $recipients;

			$content = $this->load->view('meeting/email_agenda_template', $data, TRUE);

			if($return)
			{
				$final = $content;
				return $final;
			}
			else
			{
				echo $content;
			}
	}

	public function workspace($meeting_id="", $organ_id="")
	{
		$this->load->model('Organisation_model');

		$id = decrypt($meeting_id);

		$user_organ_id	  = $this->session->userdata("organ_id");
		$meeting_organ_id = decrypt($organ_id);

		$user_id = $this->session->userdata("user_id");
		
		$is_participant = check_if_meeting_participant($user_id, $id);

		$data_get = $this->input->get();

		/* check if from dashboard view meeting */
		$check_from_dashboard = $this->session->userdata("from_dashboard");
		$data['from_dashboard'] = false;
		if($check_from_dashboard == true)
		{
			$data['from_dashboard'] = true;
		}
		
		$can_view_list = "";
		$no_access = "";
		$access = "";
		$tab_id  = 6;
		$has_access = check_access($user_id, $user_organ_id, $tab_id); //check the permission
		$disabled = "";

		if(!empty($has_access))
		{
			if($has_access[0]['readonly'] == 1 && $has_access[0]['readwrite'] == 0)
			{
				$disabled = "disabled";
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

		if($access == "yes")
		{
			if(!empty($organ_id) && !empty($meeting_id))
			{
				//check if has_access on that org
				$has_org_access = $this->Organisation_model->organisation_login($user_id, $meeting_organ_id);
				$this->db->reconnect();
				if($user_organ_id == $meeting_organ_id)
				{
					if(!empty($is_participant))
					{
						$data['title'] = 'Meeting';
						$data['params'] = $data_get;
						$data['meeting_id'] = $meeting_id;
						$data['emails'] = $this->meeting_model->get_user_emails();
						$data['all_emails'] = $this->meeting_model->get_all_user_emails();
						$data['meetings'] = $this->meeting_model->get_meeting_info($id);
						$data['topics'] = $this->meeting_model->get_meeting_topics($id);	
						$data['tasks'] = $this->meeting_model->query_meeting_tasks_from_previous_meeting($id);

						$data['parkinglots'] = $this->meeting_model->query_parkinglot();

						$this->load->view('meeting/workspace', $data);
					}
					else
					{
						show_404_page("404_page");
					}
				}
				else
				{
					if($has_org_access)
					{
						$switch_organ = array('organ_id' => $meeting_organ_id);
						$this->session->set_userdata($switch_organ);

						redirect('meeting/workspace/'.$meeting_id."/".$organ_id);
					}
					else
					{
						show_404_page("404_page");
					}
				}
			}
			else
			{
				$data['title'] = 'Meeting';
				$data['meeting_id'] = $meeting_id;
				$data['emails'] = $this->meeting_model->get_user_emails();
				$data['all_emails'] = $this->meeting_model->get_all_user_emails();
				$data['meetings'] = $this->meeting_model->get_meeting_info($id);
				$data['topics'] = $this->meeting_model->get_meeting_topics($id);
				$data['tasks'] = $this->meeting_model->query_meeting_tasks_from_previous_meeting($id);
				$data['parkinglots'] = $this->meeting_model->query_parkinglot();

				$this->load->view('meeting/workspace', $data);
			}
		}

		if($no_access == "yes")
		{
			show_404_page("404_page" );
		}

		if($can_view_list == "yes")
		{
			show_404_page("404_page" );
		}
		
		if(empty($has_access) && $can_view_list != "yes" && $no_access != "yes" && $access != "yes")
		{
			if(!empty($organ_id) && !empty($meeting_id))
			{
				//check if has_access on that org
				$has_org_access = $this->Organisation_model->organisation_login($user_id, $meeting_organ_id);
				$this->db->reconnect();
				if($user_organ_id == $meeting_organ_id)
				{
					if(!empty($is_participant))
					{
						$data['title'] = 'Meeting';
						$data['params'] = $data_get;
						$data['meeting_id'] = $meeting_id;
						$data['emails'] = $this->meeting_model->get_user_emails();
						$data['all_emails'] = $this->meeting_model->get_all_user_emails();
						$data['meetings'] = $this->meeting_model->get_meeting_info($id);
						$data['topics'] = $this->meeting_model->get_meeting_topics($id);
						$data['tasks'] = $this->meeting_model->query_meeting_tasks_from_previous_meeting($id);
						$data['parkinglots'] = $this->meeting_model->query_parkinglot();

						$this->load->view('meeting/workspace', $data);
					}
					else
					{
						show_404_page("404_page");
					}
				}
				else
				{
					if($has_org_access)
					{
						$switch_organ = array('organ_id' => $meeting_organ_id);
						$this->session->set_userdata($switch_organ);

						redirect('meeting/workspace/'.$meeting_id."/".$organ_id);
					}
					else
					{
						show_404_page("404_page");
					}
				}
			}
			else
			{
				$data['title'] = 'Meeting';
				$data['meeting_id'] = $meeting_id;
				$data['emails'] = $this->meeting_model->get_user_emails();
				$data['all_emails'] = $this->meeting_model->get_all_user_emails();
				$data['meetings'] = $this->meeting_model->get_meeting_info($id);
				$data['topics'] = $this->meeting_model->get_meeting_topics($id);
				$data['tasks'] = $this->meeting_model->query_meeting_tasks_from_previous_meeting($id);
				$data['parkinglots'] = $this->meeting_model->query_parkinglot();

				$this->load->view('meeting/workspace', $data);
			}
		}
	}

	public function duplicate_meeting_entry($meeting_id='', $organ_id='')
	{
		$data_post = $this->input->post();

		$last_inserted_id = $this->meeting_model->duplicate_mysql_record('meeting', 'meeting_id', $meeting_id);

		$data_from_previous_meeting_note = $this->meeting_model->query_prev_meeting_note_data($meeting_id);
		$data_from_previous_meeting_topic = $this->meeting_model->query_prev_meeting_topic_data($meeting_id);

		//update meeting table if is_followup
		$this->meeting_model->update_meeting_is_followup($last_inserted_id);

		//insert new meeting note
		foreach($data_from_previous_meeting_note as $data)
		{
			//if type is 2 (task), put a link on the previous meeting_note entry
			$prev_note_id = 0;

			if($data['type'] == 2)
			{
				$prev_note_id =  $data['id'];
			}

			$notes_array = array(
				'meeting_id' => $last_inserted_id,
				'meeting_topic_id' => $data['meeting_topic_id'],
				'meeting_subtopic_id' => $data['meeting_subtopic_id'],
				'type' => $data['type'],
				'text' => $data['text'],
				'entered_by' => $data['entered_by'],
				'entered_on'	=> date("Y-m-d H:i:s"),
				'assigned_user' => $data['assigned_user'],
				'position'	=> $data['position'],
				'note_id_linked' => $prev_note_id
			);
			$this->meeting_model->insert_new_data('meeting_note', $notes_array);
		}

		//insert new topic and subtopic
		foreach($data_from_previous_meeting_topic as $topic)
		{
			$topics_array = array(
				'meeting_id' => $last_inserted_id,
				'topic_title' => $topic['topic_title'],
				'presenter' => $topic['presenter'],
				'time' => $topic['time'],
				'position' => $topic['position'],
				'moved_to_parkinglot' => $topic['moved_to_parkinglot'],
				'followup_to_meetingid'	=> $topic['meeting_id'],
			);

			$ids = $this->meeting_model->insert_new_data('meeting_topics', $topics_array);

			//update topic id on meeting_note table
			$data_to_update_topic_id = array(
				'meeting_topic_id' => $ids
			);
			$this->meeting_model->update_meeting_note_topic_id($last_inserted_id, $topic['topic_id'], $data_to_update_topic_id);
			//end

			$subtopics = $this->meeting_model->query_prev_meeting_subtopic_data($topic['topic_id']);

			foreach($subtopics as $subtopic)
			{
				$subs_array = array(
					'topic_id' => $ids,
					'subtopic_title' => $subtopic['subtopic_title'],
					's_position' => $subtopic['s_position'],
					'followup_to_topicid' => $subtopic['topic_id']
				);		
				$sub_ids = $this->meeting_model->insert_new_data('meeting_subtopics', $subs_array);

				//update subtopic id on meeting_note table
				$data_to_update_subtopic_id = array(
					'meeting_subtopic_id' => $sub_ids
				);
				$this->meeting_model->update_meeting_note_subtopic_id($last_inserted_id, $subtopic['subtopic_id'], $data_to_update_subtopic_id);
				//end
			}
		}

		//query and save meeting participants on meeting_participants table
		$participants = $this->meeting_model->query_prev_meeting_participants($meeting_id);

		if(!empty($participants))
		{
			$part = unserialize($participants['meeting_participants']);

			foreach($part as $p)
			{
				$part_array = array(
					'meeting_id' => $last_inserted_id,
					'userid'	 => $p
				);

				$this->meeting_model->save_meeting_participants($part_array);
			}
		}
		
		echo encrypt($last_inserted_id);
	}

	public function open_email_tab($meeting_id)
	{
		$id = get_id_from_code($meeting_id);

		$data['meeting_id'] = $meeting_id;
		$data['meetings'] = $this->meeting_model->get_meeting_info($id);
		$data['emails'] = $this->meeting_model->get_user_emails();
		$this->load->view('meeting/ajax_views/email_tab_view', $data);
	}

	public function open_print_tab($meeting_id)
	{
		$data['meeting_id'] = $meeting_id;
		$this->load->view('meeting/ajax_views/print_tab_view', $data);
	}

	public function open_download_tab($meeting_id)
	{
		$data['meeting_id'] = $meeting_id;
		$this->load->view('meeting/ajax_views/download_tab_view', $data);
	}

	public function open_attendance_tab($meeting_id)
	{
		$id = $meeting_id;

		$data['attendees'] = $this->meeting_model->get_meeting_attendees($id);
		$data['participants'] = unserialize($this->meeting_model->get_participant($id));
		$data['nonusers'] = unserialize($this->meeting_model->get_nonuser_participants($id));
		$data['meeting_id'] = $id;

		$this->load->view('meeting/ajax_views/attendance_tab_view', $data);
	}

	public function open_template_tab($meeting_id)
	{
		$data['id'] = get_id_from_code($meeting_id);
		$data['templates'] = $this->meeting_model->list_saved_templates();

		$this->load->view('meeting/ajax_views/template_tab_view', $data);
	}

	public function open_followup_tab($meeting_id)
	{
		$data['meeting_id'] = $meeting_id;
		$data['topics'] = $this->meeting_model->get_meeting_topics($meeting_id);
		$this->load->view('meeting/ajax_views/followup_tab_view', $data);
	}

	public function save_meeting_info()
	{
		$user_data = (object)$this->session->userdata();

		$this->load->model('Organisation_model');
		$this->load->model('Notification_model');

		$user_id = $this->session->userdata('user_id');
  		$organ = $user_data->organ_id;
  		$plan_id = $user_data->plan_id;

		$data_post = $this->input->post();

		$this->form_validation->set_rules('meeting_title', 'Meeting Title' ,'required');
		$this->form_validation->set_rules('participants[]', 'Participants', 'required');
		$this->form_validation->set_rules('meeting_date_from', 'Start Date', 'required');
		$this->form_validation->set_rules('meeting_date_to', 'End Date' , 'required');
		$this->form_validation->set_rules('meeting_location', 'Location', 'required');


		if($this->form_validation->run())
		{
			if(empty($data_post['optionals']))
			{
				$optionals = "NA";
			}
			else
			{
				$optionals = serialize($data_post['optionals']);
			}

			if(empty($data_post['cc']))
			{
				$cc = "NA";
			}
			else
			{
				$cc = serialize($data_post['cc']);
			}

			/** Format Date and Time **/
			$datetime = $data_post['meeting_date_from'];
	        $datetime_string = $datetime;
	        $date = strtok($datetime_string, " ");
	        $format = str_replace('/', '-', $date);
	        $formatted_date = date('Y-m-d', strtotime($format));

	        /*
	        $meeting_title = $data_post['meeting_title'];
	        $meeting_tags = NULL;
	        $meeting_participants = serialize($data_post['participants']);
	        $nonuser_participants = serialize($data_post['nonusers_participant']);
	        $meeting_optional = $optionals;
	        $meeting_cc = $cc;
	        $when_from_date = $data_post['meeting_date_from'];
	        $when_to_date = $data_post['meeting_date_to'];
	        $meeting_location = $data_post['meeting_location'];
	        */

	        
			$insert_data = array(
				'user_id'				=> $user_id,
				'organ_id' 				=> $organ,
				'plan_id'				=> $plan_id,
				'meeting_title'			=> $data_post['meeting_title'],
				//'meeting_tags'			=> serialize($data_post['meeting_tags']),
				'meeting_participants'	=> serialize($data_post['participants']),
				'nonuser_participants'  => serialize($data_post['nonusers_participant']),
				'meeting_optional'		=> $optionals,
				'meeting_cc'			=> $cc,
				'when_from_date'		=> $data_post['meeting_date_from'],
				'when_to_date'			=> $data_post['meeting_date_to'],
				'formatted_when_from_date' => $formatted_date,
				'meeting_location'		=> $data_post['meeting_location'],
			);
			

			$inserted = $this->meeting_model->save_meeting_info($insert_data);

			//$inserted = $this->meeting_model->save_meeting_data($user_id,$organ,$plan_id,$meeting_title,$meeting_tags,$meeting_participants,$nonuser_participants,$meeting_optional,$meeting_cc,$when_from_date,$when_to_date,$formatted_date,$meeting_location);

			/** Save meeting participant **/
			foreach($data_post['participants'] as $part)
			{
				$participants_id = array(
					'meeting_id' => $inserted,
					'userid'	 => $part
				);
				$this->meeting_model->save_meeting_participants($participants_id);
			}

			$_trim_id = trim_slashes(json_encode($inserted));

			if($inserted > 0)
			{
				$participant = $this->meeting_model->get_participant($_trim_id);
 				$participant_ids =  unserialize($participant);

 				$nonuser_participants = $this->meeting_model->get_nonuser_participants($_trim_id);
 				$nonuser_participant_ids = unserialize($nonuser_participants);
 				$nonuser_participant_ids = explode(",", $nonuser_participant_ids);

				foreach ($participant_ids as $participant_id )
				{
					//create user notification for participants
					$meeting_data = array(
						'user_id' => $participant_id,
						'organ_id' => $organ,
						'notification_type_id' => '3',
						'text' => 'You are requested to a meeting',
						'link_value' => 'meeting',
						'status' => 0,
						'enteredon' => date("Y-m-d H:i:s")
					);

					$this->Notification_model->success($meeting_data);
				}

				/******************************************************
				 * Meeting added email notification.
				 * by james
				 *******************************************************/
				$mail_notif = array();
				$mail_notif['name'] = $data_post['meeting_title'];
				$mail_notif['when_from'] = $data_post['meeting_date_from'];
				$mail_notif['when_to'] = $data_post['meeting_date_to'];
				$mail_notif['location'] = $data_post['meeting_location'];
				$mail_notif['owner'] = $this->user_id;
				
				$this->mail_notification->send('meeting_added', $participant_ids, $mail_notif, $nonuser_participant_ids);

				$this->latest_id = $inserted;
				$this->response_code = 0;
				$this->response_message = "Successfully created meeting.";
			}
			else
			{
				$this->response_message = "Failed to create meeting.";
			}

		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

			echo json_encode(array(
					"last_inserted_id"	=> encrypt($this->latest_id),
					"meeting_organ_id"	=> encrypt($organ),
					"error"				=> $this->response_code,
					"message"			=> $this->response_message,
			));

	}

	public function delete_meeting()
	{
		$meeting_id = $this->input->post("meeting_id");

		if(!empty($meeting_id))
		{
			$deleted = $this->meeting_model->delete_meeting($meeting_id);
			
			$this->response_code = 0;
			$this->response_message = "Successfully deleted meeting.";
			
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}

	public function delete_topic()
	{
		$topic_id = $this->input->post("topic_id");

		if(!empty($topic_id))
		{
			$deleted = $this->meeting_model->delete_topic($topic_id);

			if($deleted)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted topic.";
			}
			else
			{
				$this->response_message = "Failed to delete topic.";
			}
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}

	public function delete_subtopic()
	{
		$subtopic_id = $this->input->post("subtopic_id");

		if(!empty($subtopic_id))
		{
			$deleted = $this->meeting_model->delete_subtopic($subtopic_id);

			if($deleted)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted subtopic.";
			}
			else
			{
				$this->response_message = "Failed to delete subtopic.";
			}
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}

	public function delete_topic_ntd()
	{
		$id = $this->input->post("id");

		if(!empty($id))
		{
			$deleted = $this->meeting_model->delete_topic_ntd($id);

			$this->response_code = 0;
			$this->response_message = "Successfully deleted.";
			
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}

	public function delete_subtopic_ntd()
	{
		$id = $this->input->post("id");

		if(!empty($id))
		{
			$deleted = $this->meeting_model->delete_subtopic_ntd($id);

			$this->response_code = 0;
			$this->response_message = "Successfully deleted.";
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}

	public function get_meeting_info()
	{
		$meeting_id = $this->input->post("meeting_id");

		$results = $this->meeting_model->get_meeting_info($meeting_id);
		$info = $results[0];

		$data['info'] = $info;

		$this->load->view('meeting/ajax_views/view_meeting_info', $data);

	}

	public function save_meeting_topic()
	{
		$data_post = $this->input->post();

		$this->form_validation->set_rules('topic_title', 'Topic Title' ,'required');

		if($this->form_validation->run())
		{
			if(empty($data_post['presenter']))
			{
				$presenter = 0;
			}
			else
			{
				$presenter = $data_post['presenter'];
			}

			if(empty($data_post['time']))
			{
				$time = "NA";
			}
			else
			{
				$time =  $data_post['time'];
			}

			$insert_data = array(
				'meeting_id'			=> $data_post['meeting_id'],
				'topic_title'			=> $data_post['topic_title'],
				'presenter'				=> $presenter,
				'time'					=> $time,
				'position'				=> $this->meeting_model->get_last_position_topics(),

			);
			$inserted = $this->meeting_model->save_meeting_topic($insert_data);

			if($inserted)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully saved topic.";
			}
			else
			{
				$this->response_message = "Failed to save topic.";
			}
		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));

	}

	public function save_meeting_subtopic()
	{
		$data_post = $this->input->post();

		$this->form_validation->set_rules('subtopic_title', 'Subtopic Title' ,'required');

		if($this->form_validation->run())
		{
			$insert_data = array(
				'topic_id'			=> $data_post['topic_id'],
				'subtopic_title'	=> $data_post['subtopic_title'],
				's_position'			=> $this->meeting_model->get_last_position_subtopics(),
			);

			$inserted = $this->meeting_model->save_meeting_subtopic($insert_data);
			if($inserted)
			{
				// header('Location: '.$_SERVER['HTTP_REFERER']);
				// die;
				$this->response_code = 0;
				$this->response_message = "Successfully saved subtopic.";
			}
			else
			{
				// header('Location: '.$_SERVER['HTTP_REFERER']);
				// die;
				$this->response_message = "Failed to save subtopic.";
			}
		}

		else
		{
			// header('Location: '.$_SERVER['HTTP_REFERER']);
			// die;
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));
	}

	public function list_subtopics($topic_id)
	{
		$user_id = $this->session->userdata("user_id");
		$results = $this->meeting_model->get_meeting_subtopics($topic_id);
		$html = "";
		$count = "A";

		foreach($results as $res)
		{
			$html .= "<h4 class='subtopic_title_heading'>".$count." - ".$res['subtopic_title']."&nbsp <i class='fa fa-pencil edit-subtopic-link' style='font-size:14px;cursor:pointer' data-toggle='tooltip' data-placement='bottom' title='Edit Topic' data-edit-subtopic-id='".$res['subtopic_id']."'></i></h4>";

			$html .= "<div class='view-subtopic-ntd' style='margin-left:15px'> <a href='javascript:void(0)' class='show-subtopic-ntd' data-subtopic-ntd-id='".$res['subtopic_id']."'><i class='fa fa-eye'></i> View Notes, Decision or Task</a> </div>";

			$html .= "<div class='ntd-subtopic-container".$res['subtopic_id']."' style='height:auto;padding-left:15px;padding-right:15px'></div>";

			$html .= "<div class='col-sm-12'> <div class='subtopic-items-cont'>

					  <form method='POST' id='add-subtopic-note-task-decision".$res['subtopic_id']."'>

					  <input type='hidden' name='meeting_id' value='".$res['meeting_id']."' />
                      <input type='hidden' name='meeting_topic_id' value='".$res['topic_id']."' />
                      <input type='hidden' name='meeting_subtopic_id' value='".$res['subtopic_id']."' />
                      <input type='hidden' name='entered_by' value='".$user_id."' />

                      <textarea class='form-control' placeholder='Write note, decision or task' name='text'></textarea>
                      <div class='toolbar-actions'>
                          <div class='controls-actions'>

                            <div class='col-sm-12'>
                              <div class='save-as-actions'>
                                <p>Save As: &nbsp
                                  <input type='radio' name='type' value='1' /> Note &nbsp
                                  <input type='radio' name='type' value='2' /> Task &nbsp
                                  <input type='radio' name='type' value='3' /> Decision &nbsp
                                  <button type='button' class='btn btn-primary btn-sm btn-save-saveas-actions-subtopic' data-subtopic-id='".$res['subtopic_id']."'><i class='fa fa-floppy-o'></i> Save</button>
                                </p>
                              </div>
                            </div>

                          </div>
                      </div>
                      </form>

                    </div></div>";
			$count++;
		}
		echo $html;

		$this->load->view('meeting/includes/needed_files_subtopic');

	}


	public function update_meeting_info($meeting_id)
	{
		$user_data = (object)$this->session->userdata();

		$this->load->model('Organisation_model');
		$this->load->model('Notification_model');

  		$organ = $user_data->organ_id;
  		$plan_id = $user_data->plan_id;

		$user_id = $this->session->userdata('user_id');

		$data_post = $this->input->post();

		$this->form_validation->set_rules('meeting_title', 'Meeting Title' ,'required');
		$this->form_validation->set_rules('participants[]', 'Participants', 'required');
		$this->form_validation->set_rules('meeting_date_from', 'Start Date', 'required');
		$this->form_validation->set_rules('meeting_date_to', 'End Date' , 'required');
		$this->form_validation->set_rules('meeting_location', 'Location', 'required');


		if($this->form_validation->run())
		{
			if(empty($data_post['optionals']))
			{
				$optionals = "NA";
			}
			else
			{
				$optionals = serialize($data_post['optionals']);
			}

			if(empty($data_post['cc']))
			{
				$cc = "NA";
			}
			else
			{
				$cc = serialize($data_post['cc']);
			}

			/** Format Date and Time **/
			$datetime = $data_post['meeting_date_from'];
	        $datetime_string = $datetime;
	        $date = strtok($datetime_string, " ");
	        $format = str_replace('/', '-', $date);
	        $formatted_date = date('Y-m-d', strtotime($format));

			$update_data = array(
				//'user_id'				=> $user_id,
				'organ_id' 				=> $organ,
				'meeting_title'			=> $data_post['meeting_title'],
				//'meeting_tags'			=> serialize($data_post['meeting_tags']),
				'meeting_participants'	=> serialize($data_post['participants']),
				'nonuser_participants'  => serialize($data_post['nonusers_participant']),
				'meeting_optional'		=> $optionals,
				'meeting_cc'			=> $cc,
				'when_from_date'		=> $data_post['meeting_date_from'],
				'when_to_date'			=> $data_post['meeting_date_to'],
				'formatted_when_from_date' => $formatted_date,
				'meeting_location'		=> $data_post['meeting_location'],
			);

			$updated = $this->meeting_model->update_meeting_info($meeting_id, $update_data);


			/** Save meeting participant **/
			$deleted = $this->meeting_model->delete_old_entry_participants($meeting_id);

			foreach($data_post['participants'] as $part)
			{
				$participants_id = array(
					'meeting_id' => $meeting_id,
					'userid'	 => $part
				);
				
				$this->meeting_model->save_meeting_participants($participants_id);
			}

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully updated meeting.";
			}
			else
			{
				$this->response_message = "Failed to updated meeting.";
			}

		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

			echo json_encode(array(
					"error"				=> $this->response_code,
					"message"			=> $this->response_message,
			));

	}


	public function move_to_parkinglot($topic_id, $meeting_id)
	{
		$data = array(
			"meeting_id"	=> $meeting_id,
			"topic_id" 		=> $topic_id,
		);

		$inserted = $this->meeting_model->move_to_parkinglot($data);

		if($inserted)
		{
			$status = array(
				"moved_to_parkinglot" => 1
			);

			$updated = $this->meeting_model->update_topic_moved_status($topic_id, $status);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully moved to parking lot.";
			}
			else
			{
				$this->response_message = "Failed to move to parking lot.";
			}
		}
		else
		{
			$this->response_message = "Failed to move to parking lot.";
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
			"message"			=> $this->response_message,
		));
	}

	public function removed_topic_from_parkinglot($topic_id, $meeting_id="")
	{
		$meet_id = get_id_from_code($meeting_id);

		$status = array(
			"meeting_id" 		  => $meet_id,
			"moved_to_parkinglot" => 0,
		);

		$updated = $this->meeting_model->update_topic_moved_status($topic_id, $status);

		if($updated)
		{
			$removed = $this->meeting_model->remove_from_parkinglot($topic_id);

			if($removed)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully moved to meeting!";
			}
			else
			{
				$this->response_message = "Failed to move to meeting!";
			}
		}
		else
		{
			$this->response_message = "Failed to move to meeting!";
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
			"message"			=> $this->response_message,
		));
	}


	public function save_meeting_note()
	{
		$data_post = $this->input->post();

		$this->form_validation->set_rules('text','Content', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');

		if($this->form_validation->run())
		{
			$insert = array(
				'meeting_id' 			=> get_id_from_code($data_post['meeting_id']),
				'meeting_topic_id' 		=> $data_post['meeting_topic_id'],
				'entered_by' 			=> $data_post['entered_by'],
				'type' 					=> $data_post['type'],
				'text' 					=> $data_post['text'],
				'position'				=> $this->meeting_model->get_last_position_ntd(),
			);

			$inserted = $this->meeting_model->save_meeting_note($insert);

			if($inserted > 0)
			{
				if($data_post['type'] == 2)
				{
					$participants = null;
					$desc = "";
					$due_date = null;
					$start_date = null;
					$this->Task_model->task_add($this->user_id, $this->organ_id, $participants, $this->user_id, $data_post['text'], $desc, $due_date, $start_date, 0, 1, $this->plan_id, 0, $inserted);
				}

				$this->response_code = 0;
			}
			else
			{
				$this->response_code = 1;
			}
		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
			"message"			=> $this->response_message,
		));

	}


	public function save_meeting_note_subtopic()
	{
		$data_post = $this->input->post();

		$this->form_validation->set_rules('text','Content', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');

		if($this->form_validation->run())
		{
			$insert = array(
				'meeting_id' 				=> get_id_from_code($data_post['meeting_id']),
				'meeting_topic_id' 			=> $data_post['meeting_topic_id'],
				'meeting_subtopic_id' 		=> $data_post['meeting_subtopic_id'],
				'entered_by' 				=> $data_post['entered_by'],
				'type' 						=> $data_post['type'],
				'text' 						=> $data_post['text'],
				'position'					=> $this->meeting_model->get_last_position_ntd(),
			);

			$inserted = $this->meeting_model->save_meeting_note($insert);

			if($inserted > 0)
			{
				if($data_post['type'] == 2)
				{
					$participants = null;
					$desc = "";
					$due_date = null;
					$start_date = null;
					$this->Task_model->task_add($this->user_id, $this->organ_id, $participants, $this->user_id, $data_post['text'], $desc, $due_date, $start_date, 0, 1, $this->plan_id, 0, $inserted);
				}

				$this->response_code = 0;
			}
			else
			{
				$this->response_code = 1;
			}
		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
			"message"			=> $this->response_message,
		));

	}

	public function get_topic_ntd($topic_id) //note,task,decision
	{
		$results = $this->meeting_model->get_topic_ntd($topic_id);
		$html = "";
		$label = "";
		$checkbox = "";
		$task_assign = "";
		$style = "";
		$assignees = "";
		$due = "";

		foreach($results as $res)
		{

			if($res['type'] == 1)
			{
				$label = "Note";
				$checkbox = "";
				$task_assign = "";
				$style = "height:40px";
			}
			if($res['type'] == 2)
			{
				$label = "Task";
				$checkbox = "<input type='checkbox'>";
				$task_assign = "<a href='#' class='show-task-actions' data-toggle='popover' data-placement='bottom' data-content='' data-ntd-id='".$res['id']."' data-topic-id='".$topic_id."'><i class='fa fa-user'></i> Assign task - Set due date</a>";
				$style = "height:55px";
			}
			if($res['type'] == 3)
			{
				$label = "Decision";
				$checkbox = "";
				$task_assign = "";
				$style = "height:40px";
			}

			$html .= "<div class='content-item' style='".$style."'>".$checkbox." ";
			$html .= "<span class='item-label'>".$label."</span>: <span>".$res['text']."</span>";
			$html .= "<p>".$task_assign."</p>";
			$html .= "</div> <br/>";
		}

		echo $html;

		$this->load->view('meeting/includes/needed_files');
	}


	public function get_subtopic_ntd($subtopic_id) //note,task,decision
	{
		$results = $this->meeting_model->get_subtopic_ntd($subtopic_id);
		$html = "";
		$label = "";
		$checkbox = "";
		$task_assign = "";
		$style = "";
		$assignees = "";
		$due = "";

		foreach($results as $res)
		{
			if($res['type'] == 1)
			{
				$label = "Note";
				$checkbox = "";
				$task_assign = "";
				$style = "height:40px";
			}
			if($res['type'] == 2)
			{
				$label = "Task";
				$checkbox = "<input type='checkbox'>";
				// $task_assign = "<a href='#' class='show-task-actions-subtopic' data-toggle='popover' data-placement='bottom' data-content='' data-subtopic-ntd-id='".$res['id']."' data-subtopic-id='".$subtopic_id."'><i class='fa fa-user'></i> Assign task - Set due date</a>";
				$task_assign = "";
				$style = "height:55px";
			}
			if($res['type'] == 3)
			{
				$label = "Decision";
				$checkbox = "";
				$task_assign = "";
				$style = "height:40px";
			}

			$html .= "<div class='content-item' style='".$style."'>".$checkbox." ";
			$html .= "<span class='item-label'>".$label."</span>: <span>".$res['text']."</span>";
			$html .= "<p>".$task_assign."</p>";
			$html .= "</div> <br/>";
		}

		echo $html;

		$this->load->view('meeting/includes/needed_files');

	}

	public function load_task_assignto_due_view()
	{
		$data_post = $this->input->post();

		$results = $this->meeting_model->get_ntd_info($data_post['ntd_id']);

		$data['topic_id'] = $data_post['topic_id'];
		$data['ntd_id'] = $data_post['ntd_id'];
		$data['all_emails'] = $this->meeting_model->get_all_user_emails();
		$data['datas'] = $results;


		$this->load->view('meeting/ajax_views/assign_task_due', $data);
		$this->load->view('meeting/includes/needed_files');
	}

	public function load_task_assignto_due_view_subtopic()
	{
		$data_post = $this->input->post();

		$results = $this->meeting_model->get_ntd_info($data_post['ntd_id']);

		$data['subtopic_id'] = $data_post['subtopic_id'];
		$data['ntd_id'] = $data_post['ntd_id'];
		$data['all_emails'] = $this->meeting_model->get_all_user_emails();
		$data['datas'] = $results;


		$this->load->view('meeting/ajax_views/assign_task_due_subtopic', $data);
		$this->load->view('meeting/includes/needed_files_subtopic');
	}

	public function update_meeting_note()
	{
		$data_post = $this->input->post();

		$id = $data_post['id'];
		$topic_id = $data_post['topic_id'];

		$insert = array(
			'assigned_user' 	=> serialize($data_post['assigned_user']),
			'due_date' 			=> $data_post['due_date'],
		);

		$inserted = $this->meeting_model->update_meeting_note($id, $topic_id, $insert);

		if($insert)
		{
			$this->response_code = 0;
		}
		else
		{
			$this->response_code = 1;
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
		));
	}

	public function update_meeting_note_subtopic()
	{
		$data_post = $this->input->post();

		$id = $data_post['id'];
		$subtopic_id = $data_post['subtopic_id'];

		$insert = array(
			'assigned_user' 	=> serialize($data_post['assigned_user']),
			'due_date' 			=> $data_post['due_date'],
		);

		$inserted = $this->meeting_model->update_meeting_note_subtopic($id, $subtopic_id, $insert);

		if($insert)
		{
			$this->response_code = 0;
		}
		else
		{
			$this->response_code = 1;
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
		));
	}


	public function edit_topic_information($topic_id, $meeting_id="")
	{
		$data['topics'] = $this->meeting_model->get_topic_information($topic_id);
		$data['all_emails'] = $this->meeting_model->get_all_user_emails();
		$data['meetings'] = $this->meeting_model->get_meeting_info($meeting_id);

		$this->load->view('meeting/ajax_views/edit_topic_page', $data);
	}

	public function edit_subtopic_information($subtopic_id)
	{
		$data['subtopics'] = $this->meeting_model->get_subtopic_information($subtopic_id);

		$this->load->view('meeting/ajax_views/edit_subtopic_page', $data);
	}

	public function query_topic_ntd($id)
	{
		$responses = $this->meeting_model->get_ntd_info($id);

		$response = (!empty($responses)) ? $responses[0]['text'] : "";
		
		echo $response;
	}

	public function query_subtopic_ntd($id)
	{
		$responses = $this->meeting_model->get_ntd_info($id);

		$response = (!empty($responses)) ? $responses[0]['text'] : "";
		
		echo $response;
	}

	public function query_subtopic_title($subtopic_id)
	{
		$responses = $this->meeting_model->get_subtopic_title($subtopic_id);

		$response = (!empty($responses)) ? $responses[0]['subtopic_title'] : "";
		
		echo $response;
	}

	public function update_topic_information()
	{
		$data_post = $this->input->post();
		$topic_id = $data_post['hdnTopicID'];

		$this->form_validation->set_rules('topic_title', 'Topic Title' ,'required');

		if($this->form_validation->run())
		{
			if(empty($data_post['presenter']))
			{
				$presenter = 0;
			}
			else
			{
				$presenter = $data_post['presenter'];
			}

			if(empty($data_post['time']))
			{
				$time = "NA";
			}
			else
			{
				$time =  $data_post['time'];
			}

			$update_data = array(
				'topic_title'			=> $data_post['topic_title'],
				'presenter'				=> $presenter,
				'time'					=> $time

			);

			$updated = $this->meeting_model->update_topic_information($topic_id, $update_data);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully updated topic.";
			}
			else
			{
				$this->response_message = "Failed to update topic.";
			}
		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));
	}


	public function update_subtopic_information()
	{
		$data_post = $this->input->post();
		$subtopic_id = $data_post['hdnSubtopicID'];

		$this->form_validation->set_rules('subtopic_title', 'Subtopic Title' ,'required');

		if($this->form_validation->run())
		{
			$update_data = array(
				'subtopic_title'	=> $data_post['subtopic_title'],
			);

			$updated = $this->meeting_model->update_subtopic_information($subtopic_id, $update_data);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully updated subtopic.";
			}
			else
			{
				$this->response_message = "Failed to update subtopic.";
			}
		}

		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));
	}

	public function update_topic_ntd_information()
	{
		$data_post = $this->input->post();
		$id = $data_post['id'];

		$this->form_validation->set_rules('ntd_text', 'Name' ,'required');

		if($this->form_validation->run())
		{
			$update_data = array(
				'text'	=> $data_post['ntd_text'],
			);

			$updated = $this->meeting_model->update_topic_ntd_information($id, $update_data);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully updated.";
			}
			else
			{
				$this->response_message = "Failed to update. Maybe there are invalid characters on your text. Please check and try again.";
			}
		}

		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));
	}

	public function update_subtopic_ntd_information()
	{
		$data_post = $this->input->post();
		$id = $data_post['id'];

		$this->form_validation->set_rules('ntd_text', 'Name' ,'required');

		if($this->form_validation->run())
		{
			$update_data = array(
				'text'	=> $data_post['ntd_text'],
			);

			$updated = $this->meeting_model->update_subtopic_ntd_information($id, $update_data);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully updated.";
			}
			else
			{
				$this->response_message = "Failed to update. Maybe there are invalid characters on your text. Please check and try again.";
			}
		}

		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));
	}

	public function inline_edit_subtopic_title()
	{
		$data_post = $this->input->post();
		$id = $data_post['subtopic_id'];
		$title = $data_post['title'];

		$this->form_validation->set_rules('title', 'Subtopic title' ,'required');

		if($this->form_validation->run())
		{
			$update_data = array(
				'subtopic_title' => $title,
			);

			$updated = $this->meeting_model->update_subtopic_information($id, $update_data);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully updated.";
			}
			else
			{
				$this->response_message = "Failed to update. Maybe there are invalid characters on your text. Please check and try again.";
			}
		}

		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
				"error"				=> $this->response_code,
				"message"			=> $this->response_message,
		));
	}

	public function sort_meeting_topics()
	{

		if($this->input->post())
		{
			$i = 0;
			foreach ($_POST['item'] as $value)
			{
			    // Execute statement:
			    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
				$this->meeting_model->update_position($value, array('position' => $i));
			    $i++;
			}
		}
	}

	public function sort_meeting_subtopics()
	{

		if($this->input->post())
		{
			$i = 0;
			foreach ($_POST['item'] as $value)
			{
			    // Execute statement:
			    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
				$this->meeting_model->update_subtopic_position($value, array('s_position' => $i));
			    $i++;
			}
		}
	}

	public function sort_meeting_ntd()
	{

		if($this->input->post())
		{
			$i = 0;
			foreach ($_POST['item'] as $value)
			{
			    // Execute statement:
			    // UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
				$this->meeting_model->update_ntd_position($value, array('position' => $i));
			    $i++;
			}
		}
	}

	public function move_topic_by_up_arrow()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		$position = $post_data['position'];
		$meeting_id = $post_data['meeting_id'];

		if(!empty($topic_id))
		{
			$new_position = $position - 1;

			$update_position = $this->meeting_model->query_position_to_change($meeting_id, $new_position);

			if(!empty($update_position))
			{
				$replaced_position = $update_position[0]['position'];
				$new_position_replaced = $replaced_position + 1;

				$moved_down = $this->meeting_model->update_replaced_topic($meeting_id, $replaced_position, array('position' => $new_position_replaced));

				if($moved_down)
				{
					$this->meeting_model->update_position($topic_id, array('position' => $new_position));
				}
			}
		}
	}

	public function move_topic_by_down_arrow()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		$position = $post_data['position'];
		$meeting_id = $post_data['meeting_id'];

		if(!empty($topic_id))
		{
			$new_position = $position + 1;

			$update_position = $this->meeting_model->query_position_to_change($meeting_id, $new_position);

			if(!empty($update_position))
			{
				$replaced_position = $update_position[0]['position'];
				$new_position_replaced = $replaced_position - 1;

				

				$moved_up = $this->meeting_model->update_replaced_topic($meeting_id, $replaced_position, array('position' => $new_position_replaced));

				if($moved_up)
				{
					$this->meeting_model->update_position($topic_id, array('position' => $new_position));
				}
			}
		}
	}

	public function move_subtopic_by_up_arrow()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		$position = $post_data['position'];
		$subtopic_id = $post_data['subtopic_id'];

		if(!empty($subtopic_id))
		{
			$new_position = $position - 1;

			$update_position = $this->meeting_model->s_query_position_to_change($topic_id, $new_position);

			if(!empty($update_position))
			{
				$replaced_position = $update_position[0]['s_position'];
				$new_position_replaced = $replaced_position + 1;

				$moved_down = $this->meeting_model->s_update_replaced_topic($topic_id, $replaced_position, array('s_position' => $new_position_replaced));

				if($moved_down)
				{
					$this->meeting_model->update_subtopic_position($subtopic_id, array('s_position' => $new_position));
				}
			}
		}
	}
	public function move_subtopic_by_down_arrow()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		$position = $post_data['position'];
		$subtopic_id = $post_data['subtopic_id'];

		if(!empty($subtopic_id))
		{
			$new_position = $position + 1;

			$update_position = $this->meeting_model->s_query_position_to_change($topic_id, $new_position);

			if(!empty($update_position))
			{
				$replaced_position = $update_position[0]['s_position'];
				$new_position_replaced = $replaced_position - 1;

				$moved_up = $this->meeting_model->s_update_replaced_topic($topic_id, $replaced_position, array('s_position' => $new_position_replaced));

				if($moved_up)
				{
					$this->meeting_model->update_subtopic_position($subtopic_id, array('s_position' => $new_position));
				}
			}
		}
	}

	public function move_topic_ntd_by_up_arrow()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		//$position = $post_data['position'];
		$meeting_id = $post_data['meeting_id'];
		$note_id = $post_data['note_id'];
		
		$subtopic_id = 0;
		$old_position = $post_data['position'];
		$new_position = $old_position - 1; //move up


		if(!empty($meeting_id))
		{
			$moved = $this->meeting_model->meeting_note_reposition($note_id, $topic_id, $subtopic_id, $old_position, $new_position);
		}

		// if(!empty($topic_id))
		// {
		// 	$new_position = $position - 1;
			
		// 	$update_position = $this->meeting_model->query_ntd_position_to_change($meeting_id, $new_position);

		// 	if(!empty($update_position))
		// 	{
		// 		$replaced_position = $update_position[0]['position'];
		// 		$new_position_replaced = $replaced_position + 1;

		// 		$moved_down = $this->meeting_model->update_ntd_replaced_topic($meeting_id, $replaced_position, array('position' => $new_position_replaced));

		// 		if($moved_down)
		// 		{
		// 			$this->meeting_model->update_ntd_position($note_id, array('position' => $new_position));
		// 		}
		// 	}
		// }
	}

	public function move_topic_ntd_by_down_arrow()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		//$position = $post_data['position'];
		$meeting_id = $post_data['meeting_id'];
		$note_id = $post_data['note_id'];

		$subtopic_id = 0;
		$old_position = $post_data['position'];
		$new_position = $old_position + 1; //move down

		if(!empty($meeting_id))
		{
			$moved = $this->meeting_model->meeting_note_reposition($note_id, $topic_id, $subtopic_id, $old_position, $new_position);
		}


		// if(!empty($topic_id))
		// {
		// 	$new_position = $position + 1;
			
		// 	$update_position = $this->meeting_model->query_ntd_position_to_change($meeting_id, $new_position);

		// 	if(!empty($update_position))
		// 	{
		// 		$replaced_position = $update_position[0]['position'];
		// 		$new_position_replaced = $replaced_position - 1;

		// 		$moved_up = $this->meeting_model->update_ntd_replaced_topic($meeting_id, $replaced_position, array('position' => $new_position_replaced));

		// 		if($moved_up)
		// 		{
		// 			$this->meeting_model->update_ntd_position($note_id, array('position' => $new_position));
		// 		}
		// 	}
		// }
	}


	public function subtopic_ntd_reposition_up()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		
		$meeting_id = $post_data['meeting_id'];
		$subtopic_id = $post_data['subtopic_id'];
		$note_id = $post_data['note_id'];
		$old_position = $post_data['position'];
		$new_position = $old_position - 1; //move up

		if(!empty($meeting_id))
		{
			$moved = $this->meeting_model->meeting_note_reposition($note_id, $topic_id, $subtopic_id, $old_position, $new_position);
		}
	}
	public function subtopic_ntd_reposition_down()
	{
		$post_data = $this->input->post();

		$topic_id = $post_data['topic_id'];
		
		$meeting_id = $post_data['meeting_id'];
		$subtopic_id = $post_data['subtopic_id'];
		$note_id = $post_data['note_id'];
		$old_position = $post_data['position'];
		$new_position = $old_position + 1; //move down

		if(!empty($meeting_id))
		{
			$moved = $this->meeting_model->meeting_note_reposition($note_id, $topic_id, $subtopic_id, $old_position, $new_position);
		}
	}



	public function email_agenda()
	{
		$data_post = $this->input->post();

		$participants = "";
		$nonusers = "";
		$nonmembers = "";

		if(!empty($data_post['participants']))
		{
			$participants = $data_post['participants'];
		}
		else
		{
			$participants = array();
		}

		if(!empty($data_post['nonuser_emails']))
		{
			$nonusers = $data_post['nonuser_emails'];
		}
		else
		{
			$nonusers = array();
		}

		if($data_post['nonusers_participant'])
		{
			$nonmembers = explode(",",$data_post['nonusers_participant']);
		}
		else
		{
			$nonmembers = array();
		}



		$email_add = $this->session->userdata('email');
		$subject = 'Agenda for '.$data_post['meeting_title']." - ".$data_post['start_date'];
		$headers = "From:" . $email_add . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";

		if(!empty($data_post['optionals']))
		{
			$headers .= 'Cc: '.implode(',',$data_post['optionals']).' ' . "\r\n";
		}
		$headers .= 'Bcc: saavedra.ted@gmail.com' . "\r\n";

		foreach($participants as $par)
		{

			$email_form = $this->form_email_temp($data_post['meeting_id'], $data_post['email_message'] , $par, TRUE);
			mail($par,$subject,$email_form,$headers);
		}

		// foreach($nonusers as $non)
		// {
		// 	$email_form = $this->form_email_temp($data_post['meeting_id'], $data_post['email_message'] , $non, TRUE);
		// 	mail($non,$subject,$email_form,$headers);
		// }


		foreach($nonmembers as $nonmember)
		{
			//print_me($nonmember);
			$email_form = $this->form_email_temp($data_post['meeting_id'], $data_post['email_message'] , $nonmember, TRUE);
			mail($nonmember,$subject,$email_form,$headers);
		}
		exit;


		/** This is for local test of sending email via gmail smtp
			$email_form = $this->form_email_temp($data_post['meeting_id'], $data_post['meeting_url'], $data_post['email_message'] ,TRUE);
			$this->load->config('email');
			$config =   $this->config->item('me_email');
			$this->load->library('email', $config);

			$this->email->from('moreplanner.agenda@gmail.com','Moreplanner Agenda');
			$this->email->to($data_post['participants']);
			if(!empty($data['optionals']))
			{
				$this->email->cc($data_post['optionals']);
			}
			$this->email->bcc('saavedra.ted@gmail.com');
			$this->email->subject('Agenda for '.$data_post['meeting_title']." - ".$data_post['start_date']);
			$this->email->message($email_form);
			$this->email->send();
		**/
	}


	public function save_meeting_attendees()
	{
		$data_post = $this->input->post();
	}

	public function print_meeting_minutes()
	{
		$data['title'] = 'Minutes of the meeting';
		$data_get = $this->input->get();
		$meeting_id = decrypt($data_get['meetingID']);

		$data['meetings'] = $this->meeting_model->get_meeting_info($meeting_id);

		if(!empty($data_get['show_boxes']))
		{
			$data['show_boxes'] = $data_get['show_boxes'];
		}

		$data['topics'] = $this->meeting_model->get_meeting_topics($meeting_id);

		$html = $this->load->view("meeting/pdf_template", $data , TRUE);
		pdf_create($html, "meeting_minutes" , "a4");
	}

	public function download_meeting_minutes()
	{
		$data['title'] = 'Minutes of the meeting';
		$data_get = $this->input->get();
		$meeting_id = decrypt($data_get['meetingID']);

		$organ_id = $this->session->userdata('organ_id');

		$organ_name = organ_info("name", $organ_id);

		$data['meetings'] = $this->meeting_model->get_meeting_info($meeting_id);

		foreach($data['meetings'] as $row)
		{
			$meeting_name = $row['meeting_title'];
			$meeting_date = $row['when_from_date'];
			$formmated_date = str_replace("/", "-", $meeting_date);
		}

		if(!empty($data_get['show_boxes']))
		{
			$data['show_boxes'] = $data_get['show_boxes'];
		}

		$data['topics'] = $this->meeting_model->get_meeting_topics($meeting_id);

		$html = $this->load->view("meeting/pdf_template", $data , TRUE);
		pdf_download($html, $organ_name." - ".$meeting_name." - ".$formmated_date);
	}

	public function save_meeting_template()
	{
		$data_post = $this->input->post();

		$user_data = (object)$this->session->userdata();

		$user_id = $this->session->userdata('user_id');
  		$organ = $user_data->organ_id;
  		$plan_id = $user_data->plan_id;

		$this->form_validation->set_rules('template_name','Template Name', 'required');

		if($this->form_validation->run())
		{
			$insert = array(
				'from_meeting_id' 		=> $data_post['from_meeting_id'],
				'template_name' 		=> $data_post['template_name'],
				'user_id'				=> $user_id,
				'organ_id'				=> $organ,
				'plan_id'				=> $plan_id,
				'date_saved'			=> date("Y-m-d H:i:s")
			);

			$inserted = $this->meeting_model->save_meeting_template($insert);

			if($insert)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully saved template.";
			}
			else
			{
				$this->response_code = 1;
				$this->response_message = "Failed to save template.";
			}
		}
		else
		{
			$this->response_message = validation_errors("<span></span>");
		}

		echo json_encode(array(
			"error"				=> $this->response_code,
			"message"			=> $this->response_message,
		));
	}


	public function load_save_template()
	{
		$data_post = $this->input->post();

		$from_meeting_id = $data_post['from_meeting_id'];
		$current_meeting_id = $data_post['current_meeting_id'];

		$get_topics = $this->meeting_model->get_saved_template($from_meeting_id);

		$get_notes = $this->meeting_model->get_saved_template_notes($from_meeting_id);
		
		foreach($get_notes as $data)
		{
			$notes_array = array(
				'meeting_id' => $current_meeting_id,
				'meeting_topic_id' => $data['meeting_topic_id'],
				'meeting_subtopic_id' => $data['meeting_subtopic_id'],
				'type' => $data['type'],
				'text' => $data['text'],
				'entered_by' => $data['entered_by'],
				'entered_on'	=> date("Y-m-d H:i:s"),
				'assigned_user' => $data['assigned_user'],
				'position'	=> $data['position'],
			);
			$this->meeting_model->save_meeting_note_for_template($notes_array);
		}

		foreach($get_topics as $topic)
		{
			$tobe_insert = array(
				'meeting_id' 		=> $current_meeting_id,
				'topic_title'		=> $topic['topic_title'],
				'presenter'			=> $topic['presenter'],
				'time'				=> $topic['time'],
				'position'			=> $this->meeting_model->get_last_position_topics(),
				'moved_to_parkinglot' => $topic['moved_to_parkinglot'],
			);

			$inserted = $this->meeting_model->save_meeting_topic_for_template($tobe_insert);

			//update topic id on meeting_note table
			$data_to_update_topic_id = array(
				'meeting_topic_id' => $inserted
			);
			$this->meeting_model->update_meeting_note_topic_id($current_meeting_id, $topic['topic_id'], $data_to_update_topic_id);
			//end

			if($inserted > 0)
			{
				$get_subtopics = $this->meeting_model->get_saved_template_subtopic($topic['topic_id']);

				foreach($get_subtopics as $subtopic)
				{
					$subtopic_arr = array(
						"topic_id"  		=> $inserted,
						"subtopic_title"	=> $subtopic['subtopic_title'],
						"s_position"		=> $this->meeting_model->get_last_position_subtopics()
					);

					$sub_ids = $this->meeting_model->save_meeting_subtopic_for_template($subtopic_arr);

					//update subtopic id on meeting_note table
					$data_to_update_subtopic_id = array(
						'meeting_subtopic_id' => $sub_ids
					);
					$this->meeting_model->update_meeting_note_subtopic_id($current_meeting_id, $subtopic['subtopic_id'], $data_to_update_subtopic_id);
					//end
				}
			}
		}

	}

	public function delete_template()
	{
		$template_id = $this->input->post("template_id");

		if(!empty($template_id))
		{
			$deleted = $this->meeting_model->delete_template($template_id);

			if($deleted)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted template.";
			}
			else
			{
				$this->response_message = "Failed to delete template.";
			}
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}


	/*this function use for supplying the data on dashboard meeting tab*/
	public function get_upcoming_meeting_for_dashboard()
	{

		$this->load->helper('more_helper');
		$this->load->model('Users_model');

		$counter= 0;
		$meetings_participant = array();
		$users = $this->Users_model->get_users();

		$meetings = get_upcoming_meetings();
		if($meetings != null){
			foreach ($meetings as $meeting ) {
			$meetings_participant = unserialize($meeting['meeting_participants']);
				$data[$counter] = array(
	    			"object" => $meeting,
	                "participant" => $meetings_participant ,
					"user" => $users
				);
				$counter++;
			}
			return print json_encode($data);

		}
		else{
			return print json_encode($data);

		}

	}

	public function update_attendees_status()
	{
		$data_post = $this->input->post();

		$updated = $this->meeting_model->update_attendee_status($data_post['id'], $data_post['status']);
	}


	/** Moreplanner meeting manual **/

	public function manual()
	{
		$data['title'] = "Quick start guide";
		$this->load->view('meeting/meeting_manual/index', $data);
	}

	public function print_meeting_agenda()
	{
		$data['title'] = "Print meeting information";
		$this->load->view('meeting/meeting_manual/print', $data);
	}

	public function email_agenda_minutes()
	{
		$data['title'] = "Email agenda and minutes";
		$this->load->view('meeting/meeting_manual/email', $data);
	}

	public function download_agenda_minutes()
	{
		$data['title'] = "Download meeting information";
		$this->load->view('meeting/meeting_manual/download', $data);
	}

	public function manage_attendance()
	{
		$data['title'] = "Manage meeting attendance";
		$this->load->view('meeting/meeting_manual/attendance', $data);
	}

	public function agenda_templates()
	{
		$data['title'] = "Agenda and minutes template";
		$this->load->view('meeting/meeting_manual/template', $data);
	}

	public function follow_up_meeting()
	{
		$data['title'] = "Follow-up meetings";
		$this->load->view('meeting/meeting_manual/follow_up', $data);
	}

	public function setup_logo_image()
	{
		$data['user_id'] = $this->session->userdata("user_id");
		$data['organ_id'] = $this->session->userdata("organ_id");

		$this->load->view('meeting/ajax_views/set_logo_image', $data);
	}

	public function save_logo_image()
	{
		$post_data = $this->input->post();

		//Initialise the upload file class
	  	$config = array(
			'upload_path' => "./uploads/user_logo_images/",
			'allowed_types' => "jpg|png|jpeg",
			'overwrite' => TRUE,
		);
		$this->upload->initialize($config);

		if(isset($_FILES))
    	{
	    	if($this->upload->do_upload())
			{
				$filename = trim(str_replace(" ","_", $_FILES['userfile']['name']));

				$data = array(
					'image_name' => $filename,
					'user_id'	 => decrypt($post_data['user_id']),
					'organ_id'	 => decrypt($post_data['organ_id']),
				);

				$check_existing_record = $this->meeting_model->query_logo_image(decrypt($post_data['organ_id']));

				if(!empty($check_existing_record)) //update record if existing
				{
					$updated = $this->meeting_model->update_logo_image(decrypt($post_data['organ_id']), $data);
					if($updated)
					{
						header('Location: '.base_url("index.php/meeting"));
					}
				}
				else //save new record
				{
					$last_id_saved = $this->meeting_model->save_logo_image($data);

					$array = array(
						'document_id' => $last_id_saved
					);

					$update_org = $this->meeting_model->update_organ_logo(decrypt($post_data['organ_id']), $array);

					if(!empty($last_id_saved))
					{
						header('Location: '.base_url("index.php/meeting"));
					}
				}
			}
			else
			{
				redirect(base_url("index.php/meeting"));
			}
		}
		else
		{
			redirect(base_url("index.php/meeting"));
		}

	}

	public function open_create_task($ntd_id, $text)
	{
		$data['text'] = urldecode($text);
		$data['ntd_id'] = $ntd_id;
		$data['users'] = $this->users_model->get_users_by_organization($this->session->userdata('organ_id'));

		$data['details'] = check_is_exists_in_task($ntd_id);

		$infos = check_is_exists_in_task($ntd_id);

		if(!empty($infos))
		{
			$info = $infos[0];
		}

		if(!empty($info))
		{
			$data['comments'] = $this->Task_model->get_task_comment($info['task_id']);
		}

		$this->load->view('meeting/ajax_views/create_task', $data);
	}
	/*********************************************************************
	VALIDATE Task : ADD, EDIT
	*********************************************************************/
	private function validate_task($owner_id, $status, $name, $priority, $m_id)
	{
		$error = false;
		$status_array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		$priority_array = array(1, 2, 3, 4);
		
		$organ_user = $this->Organisationusers_model->organisation_member_exists($owner_id, $this->organ_id);
		if($organ_user == false){
			$error = true;
			$this->response_message = "Owner doesn't exist in the organisation.";
		}
		if(!in_array($status, $status_array)){
			$error = true;
			$this->response_message = "Invalid Task Status.";
		}
		if(!in_array($priority, $priority_array)){
			$error = true;
			$this->response_message = "Invalid Task Priority.";
		}
		if($name == ""){
			$error = true;
			$this->response_message = "Task Name is required.";
		}	
		if($m_id != 0 && $m_id != null){
			$milestone = $this->Milestone_model->get_milestone($m_id, $this->organ_id);
			if($milestone == false){
				$error = true;
				$this->response_message = "Milestone doesn't exist.";
			}
		}
		return $error;	
	}
	/*********************************************************************
	VALIDATE Task Participants: ADD, EDIT
	*********************************************************************/
	private function validate_task_participants($participants, $serialize = true)
	{
		$tmp_participants = array();
		if(!empty($participants)){
			foreach($participants as $participant){
				if(is_numeric($participant)){
					$organ_user = $this->Organisationusers_model->organisation_member_exists($participant, $this->organ_id);
					if($organ_user){
						$tmp_participants[] = (int)$participant;
					}
				}
				
			}
		}
		
		return ($serialize == true) ? serialize($tmp_participants) : $tmp_participants;	
	}
	/*********************************************************************
	Extension Functions 
	*********************************************************************/
	private function validate_dates($start_date, $due_date)
	{
		$array = array(
			"error" => 0,
			"message" => "",
			"start_date" => NULL,
			"due_date" => NULL
		);
		if($start_date != NULL){
			if($this->is_valid_date($start_date)){
				$array['start_date'] = date("Y-m-d", strtotime($start_date));	
			}else{
				$array['error'] = 1;
				$array['message'] = "Invalid Start Date";
			}
		}

		if($due_date != NULL){
			if($this->is_valid_date($due_date)){
				$array['due_date'] = date("Y-m-d", strtotime($due_date));	
			}else{
				$array['error'] = 1;
				$array['message'] = "Invalid Due Date";
			}
		}
		
		if($start_date != NULL && $due_date != NULL && strtotime($start_date) > strtotime($due_date)){ 
			$array['error'] = 1;
			$array['message'] = "Start Date is greater than Due Date.";
		}
		
		return $array;
	}
	
	private function is_valid_date($date){
		preg_match('/\d{2}\/\d{2}\/\d{4}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}
	private function is_valid_date_dash($date){
		preg_match('/\d{4}-\d{2}-\d{2}/', $date, $match);
		if(!empty($match)){
			return true;
		}else{
			return false;
		}
	}

	public function save_meeting_task()
	{
		$post_data = $this->input->post();

		$this->load->model('Notification_model');
		$this->load->model('Organisation_model');
		$this->load->model('Task_users_model');

		$ntd_id = decrypt($post_data['ntd_id']);
		$owner_id =  (int)$post_data['owner'];
		$name = trim($post_data['name']);
		$desc = $post_data['description'];
		$status = (int)$post_data['status'];
		$priority = (int)$post_data['priority'];
		$m_id = 0;
		$tmp_participants = $post_data['participant'];

		$participants = $this->validate_task_participants($tmp_participants);

		$error =  $this->validate_task($owner_id, $status, $name, $priority, $m_id);
		
		//start date
		$start_date = $post_data['start_date'];
        $datetime_string = $start_date;
        $date = strtok($datetime_string, " ");
        $format = str_replace('/', '-', $date);
        $formatted_date = date('Y-m-d', strtotime($format));

        //due date
		$due_date = $post_data['date'];
        $due_datetime_string = $due_date;
        $date_due = strtok($due_datetime_string, " ");
        $due_format = str_replace('/', '-', $date_due);
        $formatted_due_date = date('Y-m-d', strtotime($due_format));

        if($start_date != '')
		{
			$start_date = $formatted_date;
		}
		else
		{
			$start_date = NULL;
		}
	
		
		if($due_date != ''){
			$due_date = $formatted_due_date;
		}
		else
		{
			$due_date = NULL;
		}

		/** Update text from meeting_note table **/
		$ntd_text = array(
			"text" => $name
		);
		$this->meeting_model->update_ntd_text($ntd_id, $ntd_text);


		/** Process **/
		if($dates['error'] == 1)
		{
			$error = true;
			$this->response_message = $dates['message'];
		}
		if($error == false)
		{
			$task_added = $this
							->Task_model
							->task_add(
								$this->user_id, $this->organ_id, $participants, $owner_id, $name, $desc, $due_date, $start_date, $status, $priority, $this->plan_id, $m_id, $ntd_id);
			
			if($task_added)
			{

				$new_participants = $this->validate_task_participants($tmp_participants, false);
				$this->Task_users_model->task_users_add($this->organ_id, $task_added->task_id, $this->user_id, $new_participants);
				
				$participants = array();
				$task_users = $this->Task_users_model->get_task_users($task_added->task_id);
				if($task_users){
					foreach($task_users as $task_user){
						$participants[] = $task_user->user_id;
					}
					
				}
				$task_added->participant_id = $participants;
				
				$this->response_code = 0;	
				$this->response_message = "Save successfully.";	
				$this->response_data = $task_added;	
				
				/* Task add notification */
				foreach ($task_added->participant_id as $___id) 
				{
						$task_data = array(
							'user_id' => $___id,
							'organ_id' => $this->organ_id,
							'notification_type_id' => '4',
							'text' => 'You have a task ' . $name . ' in Milestone ',
							'link_value' => 'schedule/update_task/' . $task_added->task_id,
							'status' => 0,
							'enteredon' => date("Y-m-d H:i:s")
						);


					$this->Notification_model->success($task_data);	 
					
				}

				/********************************************************************
				 * Send notification to people assigned to this task
				 * by: James
				 *********************************************************************/
				$recipients = $task_added->participant_id;
				if($this->user_id != $task_added->owner_id)
				{
					array_push($recipients, $task_added->owner_id);	
				}
				$mail_notif = array();
				$mail_notif['name'] = $name;
				$mail_notif['owner'] = $this->user_id;
				$mail_notif['url'] = site_url('schedule');
				$mail_notif['milestone'] = $task_added->milestone_id;
				$mail_notif['start_date'] = $start_date;
				$mail_notif['due_date'] = $due_date;
				$this->mail_notification->send('task_added', $recipients, $mail_notif);
			}
		}

		
		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}



	public function update_meeting_task()
	{
		$post_data = $this->input->post();

		$this->load->model('Notification_model');
		$this->load->model('Organisation_model');
		$this->load->model('Task_users_model');

		$ntd_id = decrypt($post_data['ntd_id']);
		$task_id = decrypt($post_data['task_id']);

		$owner_id =  (int)$post_data['owner'];
		$name = trim($post_data['name']);
		$desc = $post_data['description'];
		$status = (int)$post_data['status'];
		$priority = (int)$post_data['priority'];
		$m_id = 0;
		$tmp_participants = $post_data['participant'];

		$participants = $this->validate_task_participants($tmp_participants);
		$error =  $this->validate_task($owner_id, $status, $name, $priority, $m_id);
		
		//start date
		$start_date = $post_data['start_date'];
        $datetime_string = $start_date;
        $date = strtok($datetime_string, " ");
        $format = str_replace('/', '-', $date);
        $formatted_date = date('Y-m-d', strtotime($format));

        //due date
		$due_date = $post_data['date'];
        $due_datetime_string = $due_date;
        $date_due = strtok($due_datetime_string, " ");
        $due_format = str_replace('/', '-', $date_due);
        $formatted_due_date = date('Y-m-d', strtotime($due_format));

        if($start_date != '')
		{
			$start_date = $formatted_date;
		}
		else
		{
			$start_date = NULL;
		}
	
		
		if($due_date != ''){
			$due_date = $formatted_due_date;
		}
		else
		{
			$due_date = NULL;
		}
			

		/** Update text from meeting_note table **/
		$ntd_text = array(
			"text" => $name
		);

		$this->meeting_model->update_ntd_text($ntd_id, $ntd_text);

		if($dates['error'] == 1){
			$error = true;
			$this->response_message = $dates['message'];
		}
		if($error == false) {
			$task_update = $this
							->Task_model
							->task_update(
								$this->user_id, $task_id, $this->organ_id, $participants, $owner_id, $name, $desc, $due_date, $start_date, $status, $priority, $m_id, $this->user_id
							);
							
			//update note_id_linked field for meeting note table
			$update_array = array(
				'text' => $name
			);
			$this->meeting_model->update_meeting_task_name_with_link($ntd_id, $update_array);
			//end

			$new_participants = $this->validate_task_participants($tmp_participants, false);
			$this->Task_users_model->task_users_edit($this->organ_id, $task_id, $this->user_id, $new_participants);
			
			if($task_update)
			{
				$update_data = $this->Task_model->get_task($task_id, $this->organ_id);
				
				$participants = array();
				$task_users = $this->Task_users_model->get_task_users($update_data->task_id);
				if($task_users){
					foreach($task_users as $task_user){
						$participants[] = $task_user->user_id;
					}
					
				}
				$update_data->participant_id = $participants;
				
				$this->response_code = 0;	
				$this->response_message = "Save successfully.";	
				$this->response_data = $update_data;	
				/******************************************************
				 * update task email notification.
				 * by james
				 *******************************************************/
				$recipients = $update_data->participant_id;
				if($this->user_id != $update_data->owner_id)
				{
					array_push($recipients, $update_data->owner_id);
				}

				$mail_notif = array();
				$mail_notif['name'] = $name;
				$mail_notif['owner'] = $update_data->owner_id;
				$mail_notif['url'] = site_url('schedule');
				$mail_notif['milestone'] = $update_data->milestone_id;
				$mail_notif['start_date'] = $start_date;
				$mail_notif['due_date'] = $due_date;
				$this->mail_notification->send('task_updated', $recipients, $mail_notif);
			}
		}


		echo json_encode(array(
			"error"			=> $this->response_code,
			"message"		=> $this->response_message,
		));
		
	}


	public function save_comment()
	{
		$data_post = $this->input->post();

		$organ =  $this->session->userdata("organ_id");

		$comment = $data_post['comment'];	
		$task_id =  decrypt($data_post['task_id']);

		$insert_data = array(
			'task_id' => $task_id,
			'comment' =>  $comment,
			'date_post' => date("Y-m-d H:i:s"),
			'user_id' => $this->session->userdata('user_id'),
			'organ_id' => $organ,
			'plan_id' => $this->session->userdata('plan_id'),
		);

		$inserted = $this->Schedule_model->save_task_comment($insert_data);
		$_trim_id = trim_slashes(json_encode($inserted));

		if($inserted > 0 )
		{
			$this->response_code = 0;	
			$this->response_message = "Save successfully.";	
		}
		else
		{
			$this->response_code = 1;	
			$this->response_message = "Failed to update.";
		}

		echo json_encode(array(
			"error"			=> $this->response_code,
			"message"		=> $this->response_message,
		));

	}

	public function delete_comment()
	{
		$data_post = $this->input->post();
		$id = $data_post['task_id'];
		
		if(!empty($id))
		{
			$deleted = $this->Schedule_model->delete_comment($id);

			if($deleted == true)
			{
				$this->response_code = 0;
				$this->response_message = "Successfully deleted.";
			}
			else
			{
				$this->response_code = 1;
				$this->response_message = "Failed to delete.";
			}
			
			echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
			));
		}
	}

	public function save_update_comment(){
		
		$data_post = $this->input->post();

		$id = decrypt($data_post['task_progress_id']);
		$comment =  $data_post['update_comment'];	

		$update_data = array(
			'comment' =>$comment,
		);

		 $response = $this->Schedule_model->update_comment($update_data,$id);
		
		if($response == true)
		{
			$this->response_code = 0;
			$this->response_message = "Successfully updated.";
		}
		else
		{
			$this->response_code = 1;
			$this->response_message = "Failed to update.";
		}
		
		
		echo json_encode(array(
			"error"			=> $this->response_code,
			"message"		=> $this->response_message,
		));
	
	}

	public function mark_status_complete()
	{
		$post_data = $this->input->post();
		$this->response_message = "Error.";
		$id = $post_data['id'];
		$status = $post_data['status'];

		if(!empty($id))
		{
			$update = $this->Task_model->task_status_update_meeting($this->user_id, $id, $status, $this->organ_id);
			
			if($update)
			{
				$this->response_code = 0;
				$this->response_message = "Task status successfully updated.";
			}
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));
	}

	public function inline_edit_location()
	{
		$data_post = $this->input->post();

		$meeting_id = $data_post['meeting_id'];
		$location = $data_post['location'];


		if(!empty($meeting_id))
		{
			$array = array(
				'meeting_location' => $location
			);

			$updated = $this->meeting_model->update_meeting_info($meeting_id, $array);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Location successfully updated.";
			}
			else
			{
				$this->response_code = 1;
				$this->response_message = "Failed to update.";
			}
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));

	}

	public function inline_edit_title()
	{
		$data_post = $this->input->post();

		$meeting_id = $data_post['meeting_id'];
		$title = $data_post['title'];


		if(!empty($meeting_id))
		{
			$array = array(
				'meeting_title' => $title
			);

			$updated = $this->meeting_model->update_meeting_info($meeting_id, $array);

			if($updated)
			{
				$this->response_code = 0;
				$this->response_message = "Meeting title successfully updated.";
			}
			else
			{
				$this->response_code = 1;
				$this->response_message = "Failed to update.";
			}
		}

		echo json_encode(array(
				"error"			=> $this->response_code,
				"message"		=> $this->response_message,
		));

	}


}
