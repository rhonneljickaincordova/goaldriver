<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Attendance extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("meeting_model");
		$this->load->helper('dompdf_helper');

	}

	public function record_meeting_attendees()
	{
		$get_data = $this->input->get();

		if($get_data['status'] == "yes")
		{
			$status = 1; //accepted
			$attended = 1;
		}

		if($get_data['status'] == "no")
		{
			$status = 0; //decline
			$attended = 0;
		}
		
		$check = $this->meeting_model->check_if_email_attendees_exists(urldecode(decrypt($get_data['email'])), decrypt($get_data['meetingID']));
		
		if($check)
		{
			$array = array(
				'meeting_id' => decrypt($get_data['meetingID']),
				'attended'	 => $attended,
				'acceptance_status' => $status,
				'email'		=> urldecode(decrypt($get_data['email'])),
			);

			$this->meeting_model->save_meeting_attendance($array);
			$this->email_response_to_organizer(urldecode(decrypt($get_data['organizer'])), urldecode(decrypt($get_data['email'])), decrypt($get_data['meetingID']), $status);
			$this->load->view('meeting/includes/meeting_invitation_response');
		}
		else
		{
			echo "You can just respond once to the invitation.";
		}
	}

	public function email_response_to_organizer($organizer, $user_email, $meetingID, $status)
	{
		$meetings = $this->meeting_model->get_meeting_info($meetingID);
		
		foreach($meetings as $meet)
		{
			$meeting_title = $meet['meeting_title'];
			$meeting_date  = $meet['when_from_date'];
		}
	
		$subject = 'Invitees response for '.$meeting_title." - ".$meeting_date;
		$headers = "From:" . $user_email . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n"; 
		$headers .= 'Bcc: saavedra.ted@gmail.com' . "\r\n"; 
		
		$message = "<h4>Please be informed that: </h4>";
		if($status == 1)
		{
			$message .= "<p>".$user_email." accepted your meeting invitation.</p>"; 
		}
		if($status == 0) 
		{
			$message .= "<p>".$user_email." declined your meeting invitation.</p>"; 
		}
		
		return mail($organizer,$subject,$message,$headers);
		
	}
}