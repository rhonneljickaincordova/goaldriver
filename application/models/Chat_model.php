<?php
class Chat_model extends MY_Model{
	public $_table = 'chat_user_messages';
	public $primary_key = 'chat_id';
	
	public function get_users_list($current_user_id)
	{
		$current_user_id = (int)$current_user_id;
		$query = $this->db->query("CALL chat_users_list_load($current_user_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function get_user_messages($current_user_id, $other_user_id, $organ_id, $limit_start = 0, $limit_count = 15)
	{
		$organ_id = (int)$organ_id;
		$current_user_id = (int)$current_user_id;
		$other_user_id = (int)$other_user_id;
		$limit_start = (int)$limit_start;
		$limit_count = (int)$limit_count;
		
		$query = $this->db->query("CALL chat_user_messages_load($current_user_id, $other_user_id, $limit_start, $limit_count)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : array();
		
	}
	
	
	
	public function get_user_unread_messages($current_user_id, $other_users, $organ_id)
	{
		$array = array("unread_count"=> 0, "unread_messages"=>array());
		$unread_count = 0;
		$unread_messages = array();
		$organ_id = (int)$organ_id;
		$current_user_id = (int)$current_user_id;
		
		
		if(!empty($other_users)){
			foreach($other_users as $other_user){
				$other_user_id = $other_user['other_user_id'];
				$last_id = $other_user['last_id'];
				$last_date = $other_user['last_date'];
				
				$query = $this->db->query("CALL chat_specUser_unread_load($current_user_id, $organ_id, $other_user_id, $last_id, '$last_date' )");
				$query->next_result();
		
				if($query->num_rows() > 0){
					$unread_count++;
					$messages = $query->result_array();
					foreach($messages as $key=>$message){
						$new_message = "";
						$split_messages = preg_split("/\n/", $message['message']);
						foreach($split_messages as $split_message){
							$new_message .= "<span>" .$split_message. "</span>";
						}
						$new_messages[$key] = $new_message;
					}
					foreach($new_messages as $key=>$message){
						$messages[$key]['message'] = $message;
					}
					
					$unread_messages[] = $messages;
					
					
				}
			}
			if($unread_count > 0){
				$array['unread_messages'] = $unread_messages;
				$array['unread_count'] = $unread_count;
			}
		}
		
		return $array;
		
	}
	
	
	public function user_message_add($current_user_id, $other_user_id, $message, $message_type = "text"){
		$message = $this->db->escape_str($message);	
		$this->db->query("CALL chat_user_message_add($current_user_id, $other_user_id, '$message', '$message_type', @chat_user_message_id )");	
		$query = $this->db->query("select * from chat_user_messages where chat_user_messages.chat_user_message_id = @chat_user_message_id");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;	
	}
	
	
	public function user_unread_messages_count($current_user_id){
		$current_user_id = (int)$current_user_id;
		$query = $this->db->query("CALL chat_user_unread_counts($current_user_id)");
		$query->next_result();
		if($query->num_rows() > 0){
			$rows = $query->result();
			$unread_messages = array();
			foreach($rows as $row){
				$unread_messages[$row->from_user_id] = $row;
			}
			return $unread_messages;	
		}else{
			return false;
		}
	}
	
	public function specific_user_unread_messages_count($current_user_id, $organ_id, $other_user_id){
		$organ_id = (int)$organ_id;
		$current_user_id = (int)$current_user_id;
		$other_user_id = (int)$other_user_id;
		$query = $this->db->query("CALL chat_specific_user_unread_count($current_user_id, $organ_id, $other_user_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
	
	
	public function unread_userMgs_update($current_user_id, $other_user_id, $last_chat_id = 0 ){
		$current_user_id = (int)$current_user_id;
		$other_user_id = (int)$other_user_id;
		$last_chat_id = (int)$last_chat_id;
		if($last_chat_id == 0){
			$query = $this->db->query("CALL chat_user_unread_update($current_user_id, $other_user_id)"); 
		}else{
			$query = $this->db->query("CALL chat_user_unread_withLastID_update($current_user_id, $other_user_id, $last_chat_id)"); 
		}
		
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	
	
	public function get_user_older_messages($current_user_id, $other_user_id, $last_chat_id, $limit = 0, $limit_count = 15 ){
		$older_message_count = 0;
		$older_messages = array();
		$limit = (int)$limit;
		$limit_count = (int)$limit_count;
		$last_chat_id = (int)$last_chat_id;
		$other_user_id = (int)$other_user_id;
		$current_user_id = (int)$current_user_id;
		
		$query = $this->db->query("CALL chat_user_olderMessages_load($current_user_id, $other_user_id, $last_chat_id, $limit, $limit_count )");
		$query->next_result();

		if($query->num_rows() > 0){
			$messages = $query->result_array();
			foreach($messages as $key=>$message){
				$new_message = "";
				$split_messages = preg_split("/\n/", $message['message']);
				foreach($split_messages as $split_message){
					$new_message .= "<span>" .$split_message. "</span>";
				}
				$new_messages[$key] = $new_message;
			}
			foreach($new_messages as $key=>$message){
				$messages[$key]['message'] = $message;
			}
			
			return array("older_messages" => $messages, "older_message_count" => count($messages));
		}else{
			return array("older_messages" => array(), "older_message_count" => 0);
		}
	}
	
	/* GROUP CHATS */
	public function chat_group_exist($organ_id){
		$query = $this->db->query("CALL chat_org_groupChat_exist($organ_id)");	
		$query->next_result();
		$row = $query->row();
		
		return ($row->count > 0) ? true : false;
	}
	
	public function chat_group_add($organ_id, $user_id, $organ_name)
	{
		$this->db->query("CALL chat_group_add($organ_id, $user_id, '$organ_name', @chat_group_id)");	
		$query = $this->db->query("select * from chat_group where chat_group_id = @chat_group_id");	
		return ($query->num_rows() > 0) ? $query->row()->chat_group_id : false;	
	}
	
	public function chat_group_user_add($chat_group_id, $user_id){
		$this->db->query("CALL chat_group_user_add($chat_group_id, $user_id, @chat_group_user_id)");	
		$query = $this->db->query("select * from chat_group_user where chat_group_user_id = @chat_group_user_id");	
		return ($query->num_rows() > 0) ? $query->row()->chat_group_user_id : false;	
	}
	
	public function get_chat_groups($user_id){
		$query = $this->db->query("CALL chat_groups_list_load($user_id)");	
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function get_specGroup_messages($chat_group_id, $current_user_id, $limit_start = 0, $limit_count = 15)
	{
		$chat_group_id = (int)$chat_group_id;
		$current_user_id = (int)$current_user_id;
		$limit_start = (int)$limit_start;
		$limit_count = (int)$limit_count;
		
		$query = $this->db->query("CALL chat_group_msgs_specGroup_load($chat_group_id, $current_user_id, $limit_start, $limit_count)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function chat_group_unreadMsgs_allGroup_count($user_id){
		$user_id = (int)$user_id;
		$query = $this->db->query("CALL chat_group_unreadMsgs_allGroup_count($user_id)");
		$query->next_result();
		if($query->num_rows() > 0){
			$rows = $query->result();
		
			if($rows[0]->chat_group_id == null){
				return false;
			}else{
				$unread_messages = array();
				foreach($rows as $row){
					$unread_messages[$row->chat_group_id] = $row;
				}
				return $unread_messages;	
			}
		}else{
			return false;
		}
		
	
	}
	
	public function unread_specGroup_messages_update($chat_group_id, $current_user_id, $last_chat_id = 0 ){
		$current_user_id = (int)$current_user_id;
		$chat_group_id = (int)$chat_group_id;
		$last_chat_id = (int)$last_chat_id;
		if($last_chat_id == 0){
			$query = $this->db->query("CALL chat_group_unreadMsgs_specGroup_update($chat_group_id, $current_user_id)"); 
		}else{
			$query = $this->db->query("CALL chat_group_unreadMsgs_specGroup_withLastID_update($chat_group_id, $current_user_id, $last_chat_id)"); 
		}
		
		return ((int)$this->db->affected_rows() > 0) ? $this->db->affected_rows() : false;	
	}
	
	
	public function chat_group_unreadMsgs_specGroup_count($chat_group_id, $user_id){
		$chat_group_id = (int)$chat_group_id;
		$user_id = (int)$user_id;
		$query = $this->db->query("CALL chat_group_unreadMsgs_specGroup_count($chat_group_id, $user_id)");
		$query->next_result();
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
	
	public function group_message_add($chat_group_id, $user_id, $message, $message_type = "text"){
		$user_id = (int)$user_id;
		$chat_group = (int)$chat_group_id;
		$message = $this->db->escape_str($message);	
		$this->db->query("CALL chat_group_msg_specGroup_add($chat_group_id, $user_id, '$message', '$message_type', @chat_group_message_id )");	
		$query = $this->db->query("select * from chat_group_messages where chat_group_messages.chat_group_message_id = @chat_group_message_id");	
		
		return ($query->num_rows() > 0) ? $query->row() : false;	
	}
	
	public function chat_group_olderMsgs_specGroup_load($chat_group_id, $user_id, $last_chat_id, $limit = 0, $limit_count = 15 ){
		$older_message_count = 0;
		$older_messages = array();
		$limit = (int)$limit;
		$limit_count = (int)$limit_count;
		$last_chat_id = (int)$last_chat_id;
		$user_id = (int)$user_id;
		$chat_group_id = (int)$chat_group_id;
		
		$query = $this->db->query("CALL chat_group_olderMsgs_specGroup_load($chat_group_id, $user_id, $last_chat_id, $limit, $limit_count )");
		$query->next_result();

		if($query->num_rows() > 0){
			$messages = $query->result_array();
			foreach($messages as $key=>$message){
				$new_message = "";
				$split_messages = preg_split("/\n/", $message['message']);
				foreach($split_messages as $split_message){
					$new_message .= "<span>" .$split_message. "</span>";
				}
				$new_messages[$key] = $new_message;
			}
			foreach($new_messages as $key=>$message){
				$messages[$key]['message'] = $message;
			}
			
			return array("older_messages" => $messages, "older_message_count" => count($messages));
		}else{
			return array("older_messages" => array(), "older_message_count" => 0);
		}
	}
	
	
	public function get_userGroup_unread_messages($current_user_id, $groups)
	{
		$array = array("unread_count"=> 0, "unread_messages"=>array());
		$unread_count = 0;
		$unread_messages = array();
		$current_user_id = (int)$current_user_id;
		
		
		if(!empty($groups)){
			foreach($groups as $group){
				$chat_group_id = (int)$group['chat_group_id'];
				$last_id = (int)$group['last_id'];
				$last_date = $group['last_date'];
				
				$query = $this->db->query("CALL chat_group_unreadMsgs_specGroup_load($chat_group_id, $current_user_id, $last_id, '$last_date' )");
				$query->next_result();
		
				if($query->num_rows() > 0){
					$unread_count++;
					$messages = $query->result_array();
					foreach($messages as $key=>$message){
						$new_message = "";
						$split_messages = preg_split("/\n/", $message['message']);
						foreach($split_messages as $split_message){
							$new_message .= "<span>" .$split_message. "</span>";
						}
						$new_messages[$key] = $new_message;
					}
					foreach($new_messages as $key=>$message){
						$messages[$key]['message'] = $message;
					}
					
					$unread_messages[] = $messages;
					
					
				}
			}
			if($unread_count > 0){
				$array['unread_messages'] = $unread_messages;
				$array['unread_count'] = $unread_count;
			}
		}
		
		return $array;
		
	}
	
	
	public function unread_groupMgs_update($chat_group_id, $user_id, $last_chat_id = 0 ){
		$user_id = (int)$user_id;
		$chat_group_id = (int)$chat_group_id;
		$last_chat_id = (int)$last_chat_id;
		if($last_chat_id == 0){
			$query = $this->db->query("CALL chat_group_unreadMsgs_specGroup_update($chat_group_id, $user_id)"); 
		}else{
			$query = $this->db->query("CALL chat_group_unreadMsgs_specGroup_withLastID_update($chat_group_id, $user_id, $last_chat_id)"); 
		}
		
		return ((int)$this->db->affected_rows() > 0) ? (int)$this->db->affected_rows() : false;	
	}
	
	/* 
	TABLES :
	*user to user chat
	1. chat_user
		-use for checking user status if online or offline
	2. chat_user_messages
		-use for saving user to user messages
	
	*user to group chat
	3.  chat_group
		-use for saving group chats
	4. chat_group_user
		-use for saving users under a group chat
	5. chat_group_messages
		-use for saving user to group messages
	
	*/
}