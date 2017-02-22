<?php

class Feedback extends CI_Controller{

	public function __construct()
	{
		parent::__construct();

	}

	public function index(){

	}

	public function save_feedback()
	{
		$this->load->model('Organisation_model');
		$this->load->model('Feedback_model');
	
		$feedback = $this->input->post('feedback');
		$status_id = $this->input->post('status_id');	
		$url = $this->input->post('url');

		$insert_data = array(
			'comments' => $feedback,
			'status_id' =>  $status_id,
			'url' => $url,
			'user_id' => $this->session->userdata('user_id'),
			'organ_id' => $this->session->userdata("organ_id"),
			'plan_id' => $this->session->userdata('plan_id'),
			'enteredon' => date("Y-m-d H:i:s")
		);

		$inserted = $this->Feedback_model->save_feedback($insert_data);

		if($inserted > 0 )
		{
			/** Set session for feedback status emoticons **/
			$feedback_status = array(
				'feedback_status' => ""
			);
			$this->session->set_userdata($feedback_status);
			/** END **/

			$this->response_code = 0;	
			$this->response_message = "Save successfully.";	

			$this->email_feedback($feedback,$status_id);

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

	public function get_status(){
		
		/*** Commented due to change of logic **/
		/*
			$_POST = json_decode(file_get_contents('php://input'), true);
			$this->load->model('Feedback_model');
			
			$url =  $_POST['url'];
			
			$item['status'] = $this->Feedback_model->get_status($url);
			return print json_encode($item);
		*/
		/** END **/

		/** New logic **/
			$post_data = $this->input->post();

			$url = $post_data['url'];
			$feedback_status = $this->session->userdata('feedback_status'); //session for feedback status, default is "";

			if($feedback_status == "")
			{
				$this->load->model('Feedback_model');
				$query_status = $this->Feedback_model->get_status($url);

				if(!empty($query_status))
				{
					$status = $query_status[0]->status_id;
					
					$feedback_status = $this->session->set_userdata('feedback_status', $status);
				}
			}

			return print $feedback_status;
		/** End **/


	}

	public function email_feedback($feedback,$status_id){

		$email_add = $this->session->userdata('email');
		$user_id = $this->session->userdata('user_id');

		$to = "tim.pointon@crunchersaccountants.co.uk";
		//$subject = "Moreplanner Feedback";

		// $data['feedback'] = $feedback;
		// $data['status'] = $status_id;
		// $data['email_add'] = $email_add;
		// $data['user_id'] = $user_id;

		/******************************************************
		 * Feedback added email notification.
		 * by Ted
		 *******************************************************/
		$mail_notif = array();
		$mail_notif['feedback'] = $feedback;
		$mail_notif['status'] = $status_id;
		$mail_notif['email_add'] = $email_add;
		$mail_notif['user_id'] = $user_id;
		$this->mail_notification->send('feedback_sent', NULL, $mail_notif, array($to));
		// End: Mail notification


		//$message = $this->load->view('includes/email_footer_template', $data, TRUE);
		//mail($to,$subject,$message,$headers);
		


	}

	
}
?>