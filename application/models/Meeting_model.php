<?php

Class Meeting_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_user_emails()
	{
		$user_data = (object)$this->session->userdata();
		$user_id = $this->session->userdata('user_id');
		$organ = $user_data->organ_id;

		$query = $this->db->query("CALL organisation_users_load($user_id, $organ)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result_array() : false;
	}

	public function get_all_user_emails()
	{
		$user_data = (object)$this->session->userdata();
		$user_id = $this->session->userdata('user_id');
		$organ = $user_data->organ_id;

		$query = $this->db->query("CALL organisation_users_load($user_id, $organ)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result_array() : false;
	}

	public function save_meeting_info($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert('meeting', $data);
			$latest_id = $this->db->insert_id();

		}

		return $latest_id;
	}

	/*
	public function save_meeting_data($user_id, $organ_id, $plan_id, $meeting_title, $meeting_tags, $meeting_participants, $nonuser_participants, $meeting_optional, $meeting_cc, $when_from_date, $when_to_date, $formatted_when_from_date, $meeting_location)
	{
		$query = $this->db->query("CALL meeting_add($user_id, $organ_id, $plan_id, '$meeting_title', '$meeting_tags', '$meeting_participants', '$nonuser_participants', '$meeting_optional', '$meeting_cc', '$when_from_date', '$when_to_date', $formatted_when_from_date, '$meeting_location')");
		$response = $query->result_array();
		return ($this->db->affected_rows() > 0) ? $response[0]['LAST_INSERT_ID()'] : false;	
	}
	*/

	public function update_meeting_info($meeting_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{

			$this->db->where('meeting_id', $meeting_id);
			$response = $this->db->update('meeting', $data);

		}

		return $response;
	}

	public function save_meeting_participants($data=array())
	{
		$this->db->insert('meeting_participants', $data);
	}

	public function delete_old_entry_participants($meeting_id)
	{
		$response = false;

		if(!empty($meeting_id))
		{
			$this->db->where('meeting_id', $meeting_id);
			$response = $this->db->delete('meeting_participants');
		}

		return $response;
	}

	public function save_meeting_topic($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert('meeting_topics', $data);
		}
		return $response;
	}

	public function save_meeting_subtopic($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert('meeting_subtopics', $data);
		}
		return $response;
	}

	public function list_past_meetings()
	{
		$user_id = $this->session->userdata('user_id');

		$this->db->where('user_id', $user_id);
		$this->db->order_by('meeting_id', 'DESC');
		$query = $this->db->get('meeting');

		return $query->result_array();
	}

	public function delete_meeting($meeting_id)
	{
		$this->db->query("CALL meeting_delete($meeting_id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}

	public function delete_topic_ntd($id)
	{
		$this->db->query("CALL tasks_delete($id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}

	public function delete_subtopic_ntd($id)
	{
		$this->db->query("CALL tasks_delete($id)");	
		return ($this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;
		
	}

	public function delete_topic($topic_id)
	{
		$response = false;

		if(!empty($topic_id))
		{
			$this->db->where('topic_id', $topic_id);
			$response = $this->db->delete('meeting_topics');
		}

		return $response;
	}

	public function delete_subtopic($subtopic_id)
	{
		$response = false;

		if(!empty($subtopic_id))
		{
			$this->db->where('subtopic_id', $subtopic_id);
			$response = $this->db->delete('meeting_subtopics');
		}

		return $response;
	}

	public function get_meeting_info($meeting_id)
	{
		$this->db->where('meeting_id', $meeting_id);
		$query = $this->db->get('meeting');

		return ($query->num_rows() > 0) ? $query->result_array() : false;
	}

	public function get_participant($meeting_id)
	{
		$query = $this->db->query("SELECT  meeting_participants FROM meeting WHERE meeting_id = " .$meeting_id);
        return $query->row()->meeting_participants;
	}

	public function get_nonuser_participants($meeting_id)
	{
		$query = $this->db->query("SELECT  nonuser_participants	FROM meeting WHERE meeting_id = " .$meeting_id);
        return $query->row()->nonuser_participants;
	}

	public function get_meeting_topics($meeting_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_topics');
		$this->db->where('meeting_id', $meeting_id);
		$this->db->where('moved_to_parkinglot', 0);
		$this->db->order_by('position', 'asc');

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_meeting_subtopics($topic_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_subtopics as ms');
		$this->db->join('meeting_topics as mt','ms.topic_id=mt.topic_id', 'left');
		$this->db->where('ms.topic_id', $topic_id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function move_to_parkinglot($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert('meeting_parkinglot', $data);
		}

		return $response;
	}

	public function remove_from_parkinglot($topic_id)
	{
		$response = false;

		if(!empty($topic_id))
		{
			$this->db->where('topic_id', $topic_id);
			$response = $this->db->delete('meeting_parkinglot');
		}

		return $response;
	}

	public function update_topic_moved_status($topic_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->where('topic_id', $topic_id);
			$response = $this->db->update('meeting_topics', $data);
		}

		return $response;
	}

	public function query_parkinglot()
	{
		$this->db->select('*');
		$this->db->from('meeting_parkinglot as mp');
		$this->db->join('meeting_topics as mt', 'mp.topic_id=mt.topic_id', 'left');
		//$this->db->where('mt.meeting_id', $meeting_id);
		$query = $this->db->get()->result_array();

		return $query;
	}

	public function save_meeting_note($data=array())
	{
		if(!empty($data))
		{
			$this->db->insert('meeting_note', $data);
			$response = $this->db->insert_id();
		}

		return $response;
	}

	public function get_topic_ntd($topic_id) //notes,task,decision
	{
		$this->db->select('*');
		$this->db->from('meeting_note');
		$this->db->where('meeting_topic_id', $topic_id);
		$this->db->where('meeting_subtopic_id', NULL);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_subtopic_ntd($subtopic_id) //notes,task,decision
	{
		$this->db->select('*');
		$this->db->from('meeting_note');
		$this->db->where('meeting_subtopic_id', $subtopic_id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_ntd_info($id) //notes,task,decision
	{
		$this->db->select('*');
		$this->db->from('meeting_note');
		$this->db->where('id', $id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_subtopic_title($id) //notes,task,decision
	{
		$this->db->select('subtopic_title');
		$this->db->from('meeting_subtopics');
		$this->db->where('subtopic_id', $id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function update_meeting_note($id, $topic_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->where('id', $id);
			$this->db->where('meeting_topic_id', $topic_id);
			$response = $this->db->update('meeting_note', $data);
		}

		return $response;
	}

	public function update_meeting_note_subtopic($id, $subtopic_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->where('id', $id);
			$this->db->where('meeting_subtopic_id', $subtopic_id);
			$response = $this->db->update('meeting_note', $data);
		}

		return $response;
	}

	public function get_topic_information($topic_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_topics');
		$this->db->where('topic_id', $topic_id);

		$query = $this->db->get()->result_array();
		return $query;
	}

	public function get_subtopic_information($subtopic_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_subtopics');
		$this->db->where('subtopic_id', $subtopic_id);

		$query = $this->db->get()->result_array();
		return $query;
	}

	public function update_topic_information($topic_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{

			$this->db->where('topic_id', $topic_id);
			$response = $this->db->update('meeting_topics', $data);
		}

		return $response;
	}

	public function update_subtopic_information($subtopic_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{

			$this->db->where('subtopic_id', $subtopic_id);
			$response = $this->db->update('meeting_subtopics', $data);
		}

		return $response;
	}

	public function update_topic_ntd_information($id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{

			$this->db->where('id', $id);
			$response = $this->db->update('meeting_note', $data);
		}

		return $response;
	}

	public function update_subtopic_ntd_information($id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{

			$this->db->where('id', $id);
			$response = $this->db->update('meeting_note', $data);
		}

		return $response;
	}

	public function get_last_position_topics()
	{
 		$query = $this->db->query("SELECT max(position) as pos FROM meeting_topics");
 		$row = $query->row();
 		return $row->pos+1;
 	}

 	public function get_last_position_subtopics()
	{
 		$query = $this->db->query("SELECT max(s_position) as pos FROM meeting_subtopics");
 		$row = $query->row();
 		return $row->pos+1;
 	}

 	public function get_last_position_ntd()
	{
 		$query = $this->db->query("SELECT max(position) as pos FROM meeting_note");
 		$row = $query->row();
 		return $row->pos+1;
 	}

 	/** TOPICS SORTING **/
 	public function update_position($primary_value, $data)
 	{
 		$this->db->where("topic_id", $primary_value);
 		$this->db->update("meeting_topics", $data);
 	}

 	public function query_position_to_change($meeting_id, $position)
 	{
 		$this->db->select('position');
 		$this->db->from('meeting_topics');
 		$this->db->where("meeting_id", $meeting_id);
 		$this->db->where('position', $position);

 		$query = $this->db->get()->result_array();

 		return $query;
 	}
 	public function update_replaced_topic($meeting_id, $position, $data=array())
 	{
 		$response = false;
 		if(!empty($meeting_id))
 		{
	 		$this->db->where("meeting_id", $meeting_id);
	 		$this->db->where("position", $position);
	 		$response = $this->db->update("meeting_topics", $data);
 		}
 		return $response;
 	}
 	/** END **/


 	/** Subtopic reorder **/
 	public function update_subtopic_position($primary_value, $data)
 	{
 		$this->db->where("subtopic_id", $primary_value);
 		$this->db->update("meeting_subtopics", $data);
 	}
 	public function s_query_position_to_change($topic_id, $position)
 	{
 		$this->db->select('s_position');
 		$this->db->from('meeting_subtopics');
 		$this->db->where("topic_id", $topic_id);
 		$this->db->where('s_position', $position);

 		$query = $this->db->get()->result_array();

 		return $query;
 	}
 	public function s_update_replaced_topic($topic_id, $position, $data=array())
 	{
 		$response = false;

 		if(!empty($topic_id))
 		{
	 		$this->db->where("topic_id", $topic_id);
	 		$this->db->where("s_position", $position);
	 		$response = $this->db->update("meeting_subtopics", $data);
 		}
 		return $response;
 	}
 	/** END **/

 	/** Note ordering **/
 	public function update_ntd_position($primary_value, $data)
 	{
 		$this->db->where("id", $primary_value);
 		$this->db->update("meeting_note", $data);
 	}
 	public function query_ntd_position_to_change($meeting_id, $position)
 	{
 		$this->db->select('position');
 		$this->db->from('meeting_note');
 		$this->db->where("meeting_id", $meeting_id);
 		$this->db->where('position', $position);

 		$query = $this->db->get()->result_array();

 		return $query;
 	}
 	public function update_ntd_replaced_topic($meeting_id, $position, $data=array())
 	{
 		$response = false;
 		if(!empty($meeting_id))
 		{
	 		$this->db->where("meeting_id", $meeting_id);
	 		$this->db->where("position", $position);
	 		$response = $this->db->update("meeting_note", $data);
 		}
 		return $response;
 	}

 	public function meeting_note_reposition($noteid, $topic_id, $subtopic_id, $old_position, $new_position)
 	{
 		$noteid = (int)$noteid;
		$topic_id = (int)$topic_id;
		$subtopic_id = (int)$subtopic_id;
		$old_position = (int)$old_position;
		$new_position = (int)$new_position;

		$query = $this->db->query("CALL meeting_note_repsition($noteid, $topic_id, $subtopic_id, $old_position, $new_position)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;
		
 	}
 	/** End **/


 	public function get_previous_meeting_info($meeting_id)
 	{
 		$this->db->select('*');
 		$this->db->from('meeting');
 		$this->db->where('meeting_id', $meeting_id);

 		$query = $this->db->get()->result_array();

 		if(!empty($query))
 		{
 			return $query[0];
 		}
 	}

 	public function duplicate_mysql_record($table, $primary_key_field, $primary_key_val)
	{
	   /* generate the select query */
	   $this->db->where($primary_key_field, $primary_key_val);
	   $query = $this->db->get($table);

	    foreach ($query->result() as $row)
	    {
	       foreach($row as $key=>$val)
	       {
	          if($key != $primary_key_field)
	          {
	          	/* $this->db->set can be used instead of passing a data array directly to the insert or update functions */
	          	$this->db->set($key, $val);
	          }//endif
	       }//endforeach
	    }//endforeach

	    /* insert the new record into table*/
	    $this->db->insert($table);
	    return $this->db->insert_id();
	}

	public function get_last_meeting_id()
	{
		$this->db->select('meeting_id');
		$this->db->from('meeting');
		$this->db->order_by('meeting_id', 'DESC');
		$this->db->limit(1);

		$query = $this->db->get()->result_array();

		return (!empty($query)) ?  $query[0] : false;
	}

	public function update_meeting_topic_table($topic_id,$data=array())
	{
		$this->db->where_in('topic_id', $topic_id);
		$this->db->update('meeting_topics', $data);
	}

	public function query_prev_meeting_note_data($meeting_id)
	{
		$this->db->select('id, meeting_topic_id, meeting_subtopic_id, type, text, assigned_user, entered_by, position');
		$this->db->from('meeting_note');
		$this->db->where('meeting_id', $meeting_id);
		$query = $this->db->get()->result_array();

		return (!empty($query)) ?  $query : false;
	}

	public function query_prev_meeting_topic_data($meeting_id)
	{
		$this->db->select('topic_id, meeting_id, topic_title, presenter, time, position, moved_to_parkinglot');
		$this->db->from('meeting_topics');
		$this->db->where('meeting_id', $meeting_id);
		$query = $this->db->get()->result_array();

		return (!empty($query)) ?  $query : false;
	}

	public function query_prev_meeting_subtopic_data($topic_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_subtopics');
		$this->db->where_in('topic_id', $topic_id);
		$query = $this->db->get()->result_array();

		return (!empty($query)) ?  $query : false;
	}

	public function query_prev_meeting_participants($meeting_id)
	{
		$this->db->select('meeting_participants');
		$this->db->from('meeting');
		$this->db->where('meeting_id', $meeting_id);

		$query = $this->db->get()->result_array();

		return (!empty($query)) ?  $query[0] : false;

	}


	public function insert_new_data($table, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->insert($table, $data);
			$response = $this->db->insert_id();
		}

		return $response;
	}
	public function update_meeting_is_followup($new_meetng_id)
	{
		$this->db->where('meeting_id', $new_meetng_id);
		$this->db->update('meeting', array('is_followup' => 1));
	}

	public function update_meeting_note_topic_id($meeting_id, $meeting_topic_id, $data=array())
	{
		$response = false;

		if(!empty($meeting_id))
		{
			$this->db->where('meeting_id', $meeting_id);
			$this->db->where_in('meeting_topic_id', $meeting_topic_id);
			$response = $this->db->update('meeting_note', $data);
		}
		return $response;
	}

	public function update_meeting_note_subtopic_id($meeting_id, $meeting_subtopic_id, $data=array())
	{
		$response = false;

		if(!empty($meeting_id))
		{
			$this->db->where('meeting_id', $meeting_id);
			$this->db->where_in('meeting_subtopic_id', $meeting_subtopic_id);
			$response = $this->db->update('meeting_note', $data);
		}
		return $response;
	}

	public function update_meeting_task_name_with_link($link_task_id, $data=array())
	{
		$this->db->where('note_id_linked', $link_task_id);
		$this->db->update('meeting_note', $data);
	}

	public function query_meeting_tasks_from_previous_meeting($meeting_id)
	{
		$this->db->select('*');
		$this->db->from('tasks as t');
		$this->db->join('meeting_note as mn', 't.ntd_id=mn.note_id_linked', 'right');
		$this->db->where('mn.meeting_id', $meeting_id);
		$this->db->where('mn.type', 2);
		$this->db->order_by('task_id', 'DESC');
		//$this->db->where('t.status', 10);
		$query = $this->db->get()->result_array();

		return $query;
	}

	public function save_meeting_template($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert('meeting_templates', $data);
		}

		return $response;
	}

	public function list_saved_templates()
	{
		$user_data = (object)$this->session->userdata();

		$user_id = $this->session->userdata('user_id');
  		$organ = $user_data->organ_id;
  		$plan_id = $user_data->plan_id;

		$this->db->select('*');
		$this->db->from('meeting_templates');
		$this->db->where('organ_id', $organ);
		$this->db->order_by('template_name', 'ASC');
		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_saved_template($meeting_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_topics');
		$this->db->where('meeting_id', $meeting_id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_saved_template_subtopic($topic_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_subtopics');
		$this->db->where_in('topic_id', $topic_id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function get_saved_template_notes($meeting_id, $topic_id=0, $subtopic_id=0)
	{
		$this->db->select('*');
		$this->db->from('meeting_note');
		$this->db->where('meeting_id', $meeting_id);

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function save_meeting_topic_for_template($data=array())
	{
		$response = 0;

		if(!empty($data))
		{
			$this->db->insert('meeting_topics', $data);
			$response = $this->db->insert_id();
		}
		return $response;
	}

	public function save_meeting_subtopic_for_template($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->insert('meeting_subtopics', $data);
			$response = $this->db->insert_id();
		}
		return $response;
	}

	public function save_meeting_note_for_template($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$response = $this->db->insert('meeting_note', $data);
		}
		return $response;
	}

	public function delete_template($template_id)
	{
		$response = false;

		if(!empty($template_id))
		{
			$this->db->where('template_id', $template_id);
			$response = $this->db->delete('meeting_templates');
		}
		return $response;
	}

	public function save_meeting_attendance($data=array())
	{
		if(!empty($data))
		{
			$this->db->insert('meeting_attendees', $data);
		}
	}

	public function check_if_email_attendees_exists($email, $meetingID)
	{

        $query = null; //emptying in case

        $query = $this->db->get_where('meeting_attendees', array(//making selection
            'email' => $email,
            'meeting_id' => $meetingID
        ));

        $count = $query->num_rows(); //counting result from query

        if($count == 0)
        {
            return TRUE;
        }
        else
        {
        	return FALSE;
        }
	}

	public function get_meeting_attendees($meeting_id)
	{
		$this->db->select('*');
		$this->db->from('meeting_attendees');
		$this->db->where('meeting_id', $meeting_id);
		$this->db->where('acceptance_status', 1);
		$this->db->order_by('email', 'ASC');

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function update_attendee_status($id, $status)
	{
		if($status == 0)
		{
			$array = array(
				'attended' => 1
			);

			$this->db->where('meeting_attendee_id', $id);
			$this->db->update('meeting_attendees', $array);
		}

		if($status == 1)
		{
			$array = array(
				'attended' => 0
			);

			$this->db->where('meeting_attendee_id', $id);
			$this->db->update('meeting_attendees', $array);
		}
	}

	public function save_logo_image($data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->insert('documents', $data);
			$response = $this->db->insert_id();
		}
		return $response;
	}

	public function update_logo_image($organ_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->where('organ_id', $organ_id);
			$response = $this->db->update('documents', $data);
		}
		return $response;
	}

	public function query_logo_image($organ_id)
	{
		$this->db->select('*');
		$this->db->from('documents');
		$this->db->where('organ_id', $organ_id);
		$query = $this->db->get()->result_array();

		return $query;
	}

	public function update_organ_logo($organ_id, $data=array())
	{
		$response = false;

		if(!empty($data))
		{
			$this->db->where('organ_id', $organ_id);
			$response = $this->db->update('organisation', $data);
		}

		return $response;
	}

	public function update_ntd_text($ntd_id, $data=array())
	{
		$this->db->where('id', $ntd_id);
		$this->db->update('meeting_note', $data);
	}

	public function save_copied_template($data=array())
	{
		if(!empty($data))
		{
			$this->db->insert('meeting_templates', $data);
		}
	}

	public function copy_meeting_template($plan_id ,$new_organ_id, $old_organ_id=NULL, $new_plan_id=NULL)
	{
		$user_id = $this->session->userdata('user_id');

		$query_meeting_templates = $this->db->query("SELECT * FROM meeting_templates WHERE plan_id = {$plan_id} AND organ_id = {$old_organ_id}");
		if($query_meeting_templates->num_rows() > 0)
		{
			foreach($query_meeting_templates->result_array() as $template)
			{
				$array = array(
					'user_id' => $template['user_id'],
					'organ_id'=> $new_organ_id,
					'plan_id' => $new_plan_id,
					'from_meeting_id' => $template['from_meeting_id'],
					'template_name' => $template['template_name'],
					'date_saved' => $template['date_saved']
				);

				$this->save_copied_template($array);
			}
			
		}
		
	}
	
	function get_all_meetings($user_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;

		$query = $this->db->query("CALL meetings_organ_load($user_id, $organ_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result() : false;  
	}
	
	function check_if_meeting_participant($user_id, $meeting_id, $organ_id)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$meeting_id = (int)$meeting_id;

		$this->db->select('meeting_participants, user_id');
		$this->db->from('meeting');
		$this->db->where(array('meeting_id'=> $meeting_id, "organ_id"=>$organ_id));
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$row = $query->row();
			$ids = unserialize($row->meeting_participants);
			
			if($row->user_id == $user_id || in_array($user_id, $ids)){
				return true;
			}	
		}
		
		return false;
	}
	
	function get_monthly_meetings($user_id, $organ_id, $month, $year)
	{
		$user_id = (int)$user_id;
		$organ_id = (int)$organ_id;
		$month = (int)$month;
		$year = (int)$year;
		
		$query = $this->db->query("CALL meetings_organ_monthly_load($organ_id, $user_id, $month, $year)");
		$query->next_result();
		return ($query->num_rows() > 0) ?  $query->result_array() : false;
	}
}
