<?php
// created by: James Erie
defined('BASEPATH') OR exit('No direct script access allowed');
class Mail_notification
{
	var $appName = 'GoalDriver';
	var $appEmail = 'no-reply@goaldriver.com';
	var $ci = '';
	var $user_id = 0;
	var $template = '';
	var $recipients = array();

	function __construct()
	{
		$this->ci = &get_instance();
		$this->user_id = $this->ci->session->userdata('user_id');

		$this->ci->load->library('email');
		$this->ci->load->helper('more_helper');
		$this->ci->load->model('Mail_notification_model');
	}

	/*
	 * $action = task_added, task_updated, milestone_added, milestone_updated
	 * 			 pitch_published, meeting_added, meeting_updated 
	 *
	 * $recipient_ids = array()
	 * $recipient_emails = array of email addresses
	 */
	function send($action, $recipient_ids=null, $data=null, $recipient_emails=null)
	{
		$errors = array();
		$templateData = array();
		$creator = $this->ci->Mail_notification_model->creator($data['owner']); // first_name, last_name

		switch ($action) 
		{
			case 'task_added':
				$templateData['title'] = 'Task added';
				$templateData['task_name'] = $data['name'];
				$templateData['task_url'] = $data['url'];
				$templateData['creator'] = $creator[0]->first_name.' '.$creator[0]->last_name;
				$templateData['milestone'] = milestone_name($data['milestone']);
				$templateData['start_date'] = $data['start_date'];
				$templateData['due_date'] = $data['due_date'];
				$this->template = $this->ci->load->view('notification/task_added', $templateData, true);
				break;

			case 'task_updated':
				$templateData['title'] = 'Task updated';
				$templateData['task_name'] = $data['name'];
				$templateData['task_url'] = $data['url'];
				$templateData['creator'] = $creator[0]->first_name.' '.$creator[0]->last_name;
				$templateData['milestone'] = milestone_name($data['milestone']);
				$templateData['start_date'] = $data['start_date'];
				$templateData['due_date'] = $data['due_date'];
				$this->template = $this->ci->load->view('notification/task_updated', $templateData, true);
				break;

			case 'milestone_added':
				$templateData['title'] = 'Milestone added';
				$templateData['milestone_name'] = $data['name'];
				$templateData['milestone_url'] = $data['url'];
				$templateData['creator'] = $creator[0]->first_name.' '.$creator[0]->last_name;
				$templateData['start_date'] = $data['start_date'];
				$templateData['due_date'] = $data['due_date'];
				$this->template = $this->ci->load->view('notification/milestone_added', $templateData, true);
				break;

			case 'milestone_updated':
				$templateData['title'] = 'Milestone updated';
				$templateData['milestone_name'] = $data['name'];
				$templateData['milestone_url'] = $data['url'];
				$templateData['creator'] = $creator[0]->first_name.' '.$creator[0]->last_name;
				$templateData['start_date'] = $data['start_date'];
				$templateData['due_date'] = $data['due_date'];
				$this->template = $this->ci->load->view('notification/milestone_updated', $templateData, true);
				break;
			// on hold muna.
			case 'strategy_published':
				$templateData['title'] = 'Strategy published';
				$templateData['secret_url'] = $data['url'];
				$templateData['creator'] = $creator[0]->first_name.' '.$creator[0]->last_name;
				$this->template = $this->ci->load->view('notification/milestone_updated', $templateData, true);
				break;

			case 'meeting_added':
				$templateData['title'] = 'Meeting added';
				$templateData['meeting_name'] = $data['name'];
				$templateData['when_from'] = $data['when_from'];
				$templateData['when_to'] = $data['when_to'];
				$templateData['location'] = $data['location'];
				$templateData['creator'] = $creator[0]->first_name.' '.$creator[0]->last_name;
				$this->template = $this->ci->load->view('notification/meeting_added', $templateData, true);
				break;

			case 'user_added2organisation':
				$templateData['title'] = 'An organisation has been added to your account';
				$templateData['organ_name'] = $data['organ_name'];
				$templateData['added_by'] = $data['added_by'];
				$templateData['added_on'] = date('Y-m-d H:i:s');
				$this->template = $this->ci->load->view('notification/user_added2organisation', $templateData, true);
				break;

			case 'new_user_added2organisation':
				$templateData['title'] = $data['sender'].' added you to their ogranisation';
				$templateData['site_url'] = site_url('account/sign_in');
				$templateData['your_name'] = $data['your_name'];
				$templateData['your_email'] = $data['your_email'];
				$templateData['your_password'] = $data['your_password'];
				
				$this->template = $this->ci->load->view('notification/new_user_added2organisation', $templateData, true);
				break;

			case 'reset_password':
				$templateData['title'] = 'Password recovery';
				$templateData['token'] = $data['token'];
				$templateData['first_name'] = $data['first_name'];
				$this->template = $this->ci->load->view('notification/reset_password', $templateData, true);
				break;

			case 'password_changed':
				$templateData['title'] = 'Your password changed';
				$templateData['your_name'] = $data['your_name'];
				$templateData['your_email'] = $data['your_email'];
				$this->template = $this->ci->load->view('notification/password_changed', $templateData, true);
				break;

			case 'feedback_sent':
				$templateData['title'] = 'Feedback';
				$templateData['feedback'] = $data['feedback'];
				$templateData['status'] = $data['status'];
				$templateData['email_add'] = $data['email_add'];
				$templateData['user_id'] = $data['user_id'];
				$this->template = $this->ci->load->view('notification/sent_feedback', $templateData, true);
				break;

		}

		if(! is_null($recipient_ids))
		{
			if(is_array($recipient_ids))
			{
				if(count($recipient_ids))
				{
					foreach($recipient_ids as $recipient)
					{
						$this->recipients[] = $this->ci->Mail_notification_model->get_recipient_email($recipient);
					}
				}
				else{
					$errors[] = 1;
				}
			}
		}
		

		// more recipient email address
		if(! is_null($recipient_emails)){
			if(is_array($recipient_emails))
			{
				if(count($recipient_emails))
				{
					foreach ($recipient_emails as $email) {
						$this->recipients[] = $email;
					}	
				}
				else{
					$errors[] = 2;
				}
			}
		}
		
		
		

		if(count($errors) < 1){
			if($this->send_mail($this->recipients, $templateData['title'], $this->template))
			{
				return true;
			}
		}
		
	}

	function send_mail($recipients, $subject, $body)
	{
		if(count($recipients))
		{
			$this->ci->email->set_mailtype("html");
			$this->ci->email->from($this->appEmail, $this->appName);
			$this->ci->email->to($recipients);
			
			$this->ci->email->subject($this->appName.' Notification ' .$subject);
			$this->ci->email->message($body);

			if($this->ci->email->send())
			{
				return true;
			}
		}
		
	}

}