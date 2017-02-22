<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {
	var $user_id;
	var $organ_id;
	var $plan_id;

	function __construct(){
		parent::__construct();

		$this->plan_id = $this->session->userdata('plan_id');
		$this->user_id = $this->session->userdata('user_id');
		$this->organ_id = $this->session->userdata('organ_id');
		
		$this->load->model('Organisation_model');
		$this->load->model('Chat_model');
		
		
	}

	/* get users and group chats */
	public function ajax_get_chat_list(){
		if(isset($_POST['action']) && $_POST['action'] ==  "get_chat_list")
		{
			$array = array(
				"result"=> "success", 
				"chat_list_count" => 0, 
				"groups_count" => 0,
				"groups" => array(),
				"users_count" => 0, 
				"users" => array(),
				"unread_messages" =>
					array(
						"count" => 0,
						"users" => 
							array(
								"count" => 0,
								"messages" => 0
							),
						"groups" =>
							array(
								"count" => 0,
								"messages" => 0
							)
					)
			);
			
			$users = $this->Chat_model->get_users_list($this->user_id);
			if($users){
				$array['users'] = $users;
				$array['users_count'] = count($users);
				$array['chat_list_count'] = count($users);
				
				$unread_messages = $this->Chat_model->user_unread_messages_count($this->user_id);
				if($unread_messages){
					$array['unread_messages']['count'] = count($unread_messages);
					$array['unread_messages']['users']['count'] = count($unread_messages);
					$array['unread_messages']['users']['messages'] = $unread_messages;	
				}
			}
			
			$groups = $this->Chat_model->get_chat_groups($this->user_id);
			if($groups){
				$array['groups'] = $groups;
				$array['groups_count'] = count($groups);
				$array['chat_list_count'] += count($groups);
				
				$unread_messages = $this->Chat_model->chat_group_unreadMsgs_allGroup_count($this->user_id);
				if($unread_messages){
					$array['unread_messages']['count'] += count($unread_messages);
					$array['unread_messages']['groups']['count'] = count($unread_messages);
					$array['unread_messages']['groups']['messages'] = $unread_messages;	
				}
			}
			die(json_encode($array));
		}
	}
	
	
	/* 
	Get users for this organization except current user 
	*/
	public function get_org_users()
	{
		$array = array("result"=> "success", "users_count" => 0, "users" => array());
		
		$users = $this->Chat_model->get_users_list($this->user_id);
		if($users){
			$array = array("result"=> "success", "users_count" => count($users), "users" => $users, 'unread_count' => 0, 'unread_messages' => array() );
			$unread_messages = $this->Chat_model->user_unread_messages_count($this->user_id);
			if($unread_messages){
				$array['unread_count'] = count($unread_messages);
				$array['unread_messages'] = $unread_messages;	
			}
		}
		
		die(json_encode($array));
	}
	
	
	/* 
	On Load get first 15 messages in user to user conversation 
	*/
	public function ajax_user_to_user_conversation()
	{
		
		$array = array("result"=> "success", "message_count" => 0, "messages" => array());
		
		if(isset($_POST['other_user_id']) && is_numeric($_POST['other_user_id']))
		{
			$messages = $this->Chat_model->get_user_messages($this->user_id, $_POST['other_user_id'], $this->organ_id);
			if($messages){
				/* change unread messages to read status */
				$this->Chat_model->unread_userMgs_update($this->user_id, $_POST['other_user_id']);
				$unread_messages = $this->Chat_model->specific_user_unread_messages_count($this->user_id, $this->organ_id, $_POST['other_user_id']);
				$new_messages = array();
				
				foreach($messages as $key=>$message){
					/* formatting messages */
					$new_message = "";
					$split_messages = preg_split("/\n/", $message->message);
					foreach($split_messages as $split_message){
						$new_message .= "<span>" .$split_message. "</span>";
					}
					$new_messages[$key] = $new_message;
				}
				foreach($new_messages as $key=>$message){
					$messages[$key]->message = $message;
				}
				$array = array("result"=> "success", "message_count" => count($messages), "messages" => $messages, "unread_messages"=> $unread_messages);
			}
		}
		
		die(json_encode($array));	
	}
	
	
	
	/* 
	On Load get first 15 messages in user to group conversation 
	*/
	public function ajax_user_to_group_conversation()
	{
		$array = array("result"=> "success", "message_count" => 0, "messages" => array());
		
		if(isset($_POST['chat_id']) && is_numeric($_POST['chat_id']))
		{
			$messages = $this->Chat_model->get_specGroup_messages($_POST['chat_id'], $this->user_id);
			if($messages){
				/* change unread messages to read status */
				$this->Chat_model->unread_specGroup_messages_update($_POST['chat_id'], $this->user_id);
				$unread_messages = $this->Chat_model->chat_group_unreadMsgs_specGroup_count($_POST['chat_id'], $this->user_id);
				$new_messages = array();
				
				foreach($messages as $key=>$message){
					/* formatting messages */
					$new_message = "";
					$split_messages = preg_split("/\n/", $message->message);
					foreach($split_messages as $split_message){
						$new_message .= "<span>" .$split_message. "</span>";
					}
					$new_messages[$key] = $new_message;
				}
				foreach($new_messages as $key=>$message){
					$messages[$key]->message = $message;
				}
				$array = array("result"=> "success", "message_count" => count($messages), "messages" => $messages, "unread_messages"=> $unread_messages);
			}
		}
		
		die(json_encode($array));	
	}
	
	
	/* 
	Save send user to user message 
	*/
	public function ajax_send_user_message()
	{
		$array = array("result"=> "error", "message" => false);
		$message_type = "text";
		/* $message_type = $post['message_type']; */
		
		if(isset($_POST['other_user_id']) && is_numeric($_POST['other_user_id']) && isset($_POST['message']) && trim($_POST['message']) != "")
		{
			$message = $_POST['message'];
			$other_user_id = $_POST['other_user_id'];
			
			$add_user_message = $this->Chat_model->user_message_add($this->user_id, $other_user_id, $message, $message_type);
			if($add_user_message){
					$new_message = "";
					$split_messages = preg_split("/\n/", $add_user_message->message);
					foreach($split_messages as $split_message){
						$new_message .= "<span>" .$split_message. "</span>";
					}
					$add_user_message->message = $new_message;
					$array = array("result"=> "success", "message"=>$add_user_message);
			}
		}
		
		die(json_encode($array));	
	}
	
	
	public function ajax_get_unread_messages()
	{
		$array = array("result"=> "error");
		
		if(isset($_POST['chat_ids'])&& !empty($_POST['chat_ids'])){
			$array = array(
				"result"=> "success", 
				"users" => array(
					"unread_messages" => 0, 
					"unread_count" => 0
				),
				"groups" => array(
					"unread_messages" => 0, 
					"unread_count" => 0
				)
			);
			
			if(isset($_POST['chat_ids']['users'])){
				$users_messages = $this->Chat_model->get_user_unread_messages($this->user_id, $_POST['chat_ids']['users'], $this->organ_id);
				$array['users']['unread_messages'] = $users_messages['unread_messages'];
				$array['users']['unread_count'] = $users_messages['unread_count'];
			}
			if(isset($_POST['chat_ids']['groups'])){
				$groups_messages = $this->Chat_model->get_userGroup_unread_messages($this->user_id, $_POST['chat_ids']['groups']);
				$array['groups']['unread_messages'] = $groups_messages['unread_messages'];
				$array['groups']['unread_count'] = $groups_messages['unread_count'];
			}
			
			
		}	
		die(json_encode($array));		
	}
	
	public function ajax_get_unread_messages_count()
	{
		if(isset($_POST['action']) && $_POST['action'] == "get_unread_messages_count")
		{
			$array = array(
				"total" => 0,
				"users" => array(
					"unread_count"=> 0, 
					"unread_messages"=>array()
				),
				"groups" => array(
					"unread_count"=> 0, 
					"unread_messages"=>array()
				)
			);
			
			$users = $this->Chat_model->user_unread_messages_count($this->user_id);
			$groups = $this->Chat_model->chat_group_unreadMsgs_allGroup_count($this->user_id);
			if($users){
				$array['users']['unread_count'] = count($users);
				$array['users']['unread_messages'] = $users;	
			}
			
			if($groups){
				$array['groups']['unread_count'] = count($groups);	
				$array['groups']['unread_messages'] = $groups;	
			}
			$array['total'] = $array['users']['unread_count'] + $array['groups']['unread_count'];
			
			die(json_encode($array));	
		}
	}
	
	public function ajax_get_user_older_messages(){
		$array = array("result"=> "success", "message_count" => 0);
		
		if(isset($_POST['user_id'])&& isset($_POST['last_chat_id'])){
			
			$messages = $this->Chat_model->get_user_older_messages($this->user_id, $_POST['user_id'], $_POST['last_chat_id']);
			$array = array("result"=> "success", "older_messages"=>$messages['older_messages'], "older_message_count"=>$messages['older_message_count']);
		}	
		die(json_encode($array));		
	}
	
	public function ajax_unread_userMsgs_update(){
		$array = array("result"=> "error", "update" => 0);
		
		if(isset($_POST['user_id'])&& isset($_POST['last_chat_id'])){
			
			$update = $this->Chat_model->unread_userMgs_update($this->user_id, $_POST['user_id'], $_POST['last_chat_id']);
			if($update){
				$array = array("result"=> "success");
			}
		}	
		die(json_encode($array));	
	}

	/* 
	Check if has no chat group, and create one. 
	*/
	public function ajax_chat_group_exist(){
		if(isset($_POST['action']) && $_POST['action'] == "chat_group_exist")
		{
			if($this->organ_id == "" || $this->organ_id == null){
				die(array("result"=>"error"));
			}
			$is_organisation_owner = $this->Organisation_model->get_owner_permission($this->organ_id, $this->user_id);
			$has_group_chat = $this->Chat_model->chat_group_exist($this->organ_id);
			if($is_organisation_owner != false && $has_group_chat == false){
				/* create chat group */
				$add_group = $this->Chat_model->chat_group_add($this->organ_id, $this->user_id, $is_organisation_owner->name);
				if($add_group){
					$has_group_chat = true;
					$chat_group_id = $add_group; 
					$users = $this->Organisation_model->organisation_members($this->user_id, $this->organ_id);
					if($users){
						foreach($users as $user){
							$this->Chat_model->chat_group_user_add($chat_group_id, $user->user_id);
						}
					}	
				}
			}
			$array = array("chat_group"=>$has_group_chat);
			die(json_encode($array));	
		}
	}
	
	/* 
	Save send user to GROUP message 
	*/
	public function ajax_send_group_message()
	{
		$array = array("result"=> "error", "message" => false);
		$message_type = "text";
		/* $message_type = $_POST['message_type']; */
		
		if(isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && isset($_POST['message']) && trim($_POST['message']) != "" )
		{
			$message = $_POST['message'];
			$chat_group_id = $_POST['chat_id'];
			$add_group_message = $this->Chat_model->group_message_add($chat_group_id, $this->user_id, $message, $message_type);
			
			if($add_group_message){
				$new_message = "";
				$split_messages = preg_split("/\n/", $add_group_message->message);
				foreach($split_messages as $split_message){
					$new_message .= "<span>" .$split_message. "</span>";
				}
				$add_group_message->message = $new_message;
				$array = array("result"=> "success", "message"=>$add_group_message);
			}
		}
		
		die(json_encode($array));	
	}
	
	
	public function ajax_get_group_older_messages(){
		$array = array("result"=> "success", "message_count" => 0);
		
		if(isset($_POST['last_chat_id']) && isset($_POST['chat_group_id'])){
			
			$messages = $this->Chat_model->chat_group_olderMsgs_specGroup_load($_POST['chat_group_id'], $this->user_id, $_POST['last_chat_id']);
			$array = array("result"=> "success", "older_messages"=>$messages['older_messages'], "older_message_count"=>$messages['older_message_count']);
		}	
		die(json_encode($array));		
	}
	
	public function ajax_unread_groupMsgs_update(){
		$array = array("result"=> "error", "update" => 0);
		
		if(isset($_POST['chat_id'])&& isset($_POST['last_chat_id'])){
			
			$update = $this->Chat_model->unread_groupMgs_update( $_POST['chat_id'], $this->user_id, $_POST['last_chat_id']);
			if($update){
				$array = array("result"=> "success");
			}
		}	
		die(json_encode($array));	
	}
	
}
?>