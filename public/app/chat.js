$(document).ready(function(){
	var users_on_chat = {};
	var groups_on_chat = {};
	
	/* Check if Has No Chat Group */
	check_chat_exist();
	
	
	/* Click chat button */
	$("body").on("click", "#chat_btn" , function(){
		var isHidden = $(".div_chat_list").hasClass("hide");
		
		if(isHidden){
			get_chat_list();
		}else{
			$(".div_chat_list").addClass("hide");
			$(".div_chat_list-bottom").addClass("hide");
		}
		
	});
	/* minimize users list for chat*/
	$("body").on("click", "#div_chat_list-minimize" , function(){
		$(".div_chat_list").addClass("hide");
		$(".div_chat_list-bottom").addClass("hide");
	});
	
	/* Click a user, then open chatbox */
	$("body").on("click", ".li-users_list", function(){
		var li_user = $(this);
		var user_id = li_user.attr("data-id");
		var full_name = li_user.attr("data-name");
		
		create_chat_box(user_id, full_name, 'user');
	});
	/* Click a group, then open chatbox */
	$("body").on("click", ".li-groups_list", function(){
		var li_group = $(this);
		var group_id = li_group.attr("data-id");
		var name = li_group.attr("data-name");
		
		create_chat_box(group_id, name, 'group');
	});
	
	/* Remove chat div */
	$("body").on("click",".remove_chat_div", function(){
		var chat_id = $(this).parents('div.chat_div').attr("data-id");
		var type = $(this).parents('div.chat_div').attr("data-type");
		if(type == 'user'){
			$("#chat_user-"+chat_id).remove();
			delete users_on_chat[chat_id];	
		}else if(type =='group'){
			$("#chat_group-"+chat_id).remove();
			delete groups_on_chat[chat_id];
		}
		
	});
	
	/* Minimize chatbox */
	$("body").on("click", ".chat_title", function(){
		var chat_container = $(this).parents('div.chat_div');
		$(this).parent(".chat_div-maximize").addClass("hide");
		chat_container.find(".chat_div-minimize").removeClass("hide");
	});
	
	/* Maximize chatbox */
	$("body").on("click", ".chat_div-minimize", function(){
		var chat_container = $(this).parents('div.chat_div');
		var chat_id = "#"+chat_container.attr("id");
		$(this).addClass("hide");
		$(chat_id).find(".chat_div-maximize").removeClass("hide");
	});
	
	$("body").on("click", ".send_user_message_btn", function(){
		var chat_id = $(this).parents('div.chat_div').attr("data-id");
		var type = $(this).parents('div.chat_div').attr("data-type");
		var message = $("#chat_"+type+"-"+chat_id).find(".my_message").val();
		if(type == "user"){
			send_user_message(chat_id, message);
		}else if(type == "group"){
			send_group_message(chat_id, message);
		}
	});
	
	function bindonEnter_SendMessage(id){
		
		$(this).on('keyup', function(e){
			
			if ((e.which == 13 || e.keyCode == 13) && !e.shiftKey) 
			{
				var chat_id = $(id).parents('div.chat_div').attr("data-id");
				var type = $(id).parents('div.chat_div').attr("data-type");
				var message = $(id).val();
				if(type == "user"){
					send_user_message(chat_id, message);
				}else if(type == "group"){
					send_group_message(chat_id, message);
				}
			}
			
		});
	}
	
	
	
	/*  */
	$(document).click(function(e){
		if(($(e.target).closest(".chat_div .chat_div-maximize").length 
			&& !$(e.target).closest(".chat_div .chat_div-maximize .chat_title").length)
			||
			$(e.target).closest(".chat_div .chat_div-minimize").length 
			){
			var chat_id = $(e.target).parents(".chat_div").attr("data-id");
			var type = $(e.target).parents(".chat_div").attr("data-type");
			make_selectedChatbox("chat_"+type+"-"+chat_id);
		}else if(!$(e.target).closest(".li-users_list").length && !$(e.target).closest(".li-groups_list").length ){
			$(".chat_div").removeClass('selected_chatbox');
		}
	});
	
	function send_user_message(other_user_id, message){
		var url = base_url + "index.php/chat/ajax_send_user_message";
		var data = {
				other_user_id : other_user_id, 
				message: message,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				if(response.result == "success"){
					var other_user_id = response.message['to_user_id'];
					var message = response.message['message'];
					var message_id = response.message['chat_user_message_id'];
					var last_message_con = $("#chat_user-"+other_user_id +" .chat_messages .message_container").last().find("div").first();
					var owner_string = "";
					if(last_message_con.hasClass('message-other')){
						owner_string = "<span class='chat_receiver-name'>Me :</span>";
					}
				
					var message_html = "<div class='message_container' id='message_container-"+message_id+"'>";
						message_html += owner_string;
						message_html += "<div class='message-mine'>"+message+"</div>";
						message_html += "<div style='clear:both;'></div>";
						message_html += "</div>";
					
					$("#chat_user-"+other_user_id).find('.chat_messages').find('.chat_div-container').append(message_html);
					seen_message("#chat_user-"+other_user_id, "hide", 0, '');
					$("#chat_user-"+other_user_id).find('.chat_messages').attr("data-latest_id",message_id);	
					$("#chat_user-"+other_user_id).find('.my_message').val("");
					
					
					var chat_container = $("#chat_user-"+other_user_id).find(".chat_messages").find(".chat_div-container");
					var last_id = chat_container.find('.message_container').last().attr('id');
					document.getElementById(last_id).scrollIntoView();
					
					make_selectedChatbox("chat_user-"+other_user_id);
					
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	/* Get user conversation from other user */
	function get_user2user_conversation(other_user_id, last_datetime){
		var url = base_url + "index.php/chat/ajax_user_to_user_conversation";
		var data = {
				other_user_id : other_user_id, 
				last_datetime: last_datetime,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				if(response.result == "success"){
					var x = 0;
					var messages = response.messages;
					var message_count = response.message_count;
					var unread_messages = response.unread_messages;
					var next_message_owner = "";
					if(message_count > 0){
						$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-latest_id', messages[0]['chat_user_message_id']);
						
						while( x < message_count ){
							var from_user_id = messages[x]['from_user_id'];
							var to_user_id = messages[x]['to_user_id'];
							var message = messages[x]['message'];
							var message_id = messages[x]['chat_user_message_id'];
							var formatted_seendate = messages[x]['formatted_seendate'];
							var send_datetime = messages[x]['send_datetime'];
							var f_fname = messages[x]['f_fname'];
							var t_fname = messages[x]['t_fname'];
							var day_counter = messages[x]['day_counter'];
							var formatted_date = messages[x]['formatted_date'];
							var owner_string = "";
							
							if(x < message_count - 1){
								next_message_owner_id = messages[x+1]['from_user_id'];
								next_message_owner_name = messages[x+1]['f_fname'];
								next_message_owner_t_fname = messages[x+1]['t_fname'];
								next_message_day_counter = messages[x+1]['day_counter'];
								
								if(from_user_id == other_user_id && next_message_owner_name != f_fname){
									owner_string = "<span class='chat_sender-name'>"+f_fname+" :</span>";
								}else if(from_user_id != other_user_id && next_message_owner_name != f_fname){
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}
							}else if(x == message_count - 1){
								if(from_user_id == other_user_id){
									owner_string = "<span class='chat_sender-name'>"+f_fname+" :</span>";
								}else{
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}
							} 
							var date_string = "";
							if(parseInt(day_counter) != parseInt(next_message_day_counter)){
								switch(parseInt(day_counter)){
									case 0:
									date_string = "<span class='message_group_date'>Today</span>";
									break;
									case 1:
									date_string = "<span class='message_group_date'>Yesterday</span>";
									break;
									default:
									date_string = "<span class='message_group_date'>"+formatted_date+"</span>";
									break;
								}
							}
							
							var date_html = date_string;
							var message_html = "<div class='message_container' id='message_container-"+message_id+"'>";
							 message_html += owner_string;
							if(from_user_id == other_user_id){
								message_html += "<div class='message-other'>"+message+"</div>";
							}else{
								message_html += "<div class='message-mine'>"+message+"</div>";	
							}
							
							message_html += "<div style='clear:both;'></div>";
							if(x == 0 && other_user_id == to_user_id && formatted_seendate != null){
								message_html += "<span class='seen_message'> Seen " +formatted_seendate+"</span>";
							}
							
							message_html += "</div>";
							$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-oldest_id', message_id);
							$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-oldest_date', send_datetime);
							$("#chat_user-"+other_user_id).find('.chat_messages').find('.chat_div-container').prepend(date_html + message_html);
							
							x++;
						}
						
						
						var chat_container = $("#chat_user-"+other_user_id).find(".chat_messages").find(".chat_div-container");
						var latest_id = chat_container.find('.message_container').last().attr('id');
						document.getElementById(latest_id).scrollIntoView();	
						
						
						if(unread_messages == false){
							if($("#li-users_list-"+other_user_id+" span.unread_count")){
								$("#li-users_list-"+other_user_id+" span.unread_count").remove();
							}
						}
						
						bindscroll_function("#chat_user-"+other_user_id + " .chat_message_scroll");
					}
					
					make_selectedChatbox("chat_user-"+other_user_id);
					
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	
	/* Get user conversation from other user */
	function get_user2group_conversation(chat_id, last_datetime){
		var url = base_url + "index.php/chat/ajax_user_to_group_conversation";
		var data = {
				chat_id : chat_id, 
				last_datetime: last_datetime,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				if(response.result == "success"){
					var x = 0;
					var messages = response.messages;
					var message_count = response.message_count;
					var unread_messages = response.unread_messages;
					var next_message_owner = "";
					if(message_count > 0){
						$("#chat_group-"+chat_id).find('.chat_messages').attr('data-latest_id', messages[0]['chat_user_message_id']);
						
						while( x < message_count ){
							var from_user_id = messages[x]['from_user_id'];
							var owner = messages[x]['owner'];
							var message = messages[x]['message'];
							var message_id = messages[x]['chat_group_message_id'];
							var send_datetime = messages[x]['send_datetime'];
							var first_name = messages[x]['first_name'];
							var last_name = messages[x]['last_name'];
							var day_counter = messages[x]['day_counter'];
							var formatted_date = messages[x]['formatted_date'];
							var owner_string = "";
							
							if(x < message_count - 1){
								next_message_owner = messages[x+1]['owner'];
								next_message_owner_id = messages[x+1]['from_user_id'];
								next_message_owner_first_name = messages[x+1]['first_name'];
								next_message_day_counter = messages[x+1]['day_counter'];
								
								if(next_message_owner == "me" && owner == "others"){
									owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
								}else if(next_message_owner == "others" && owner == "me"){
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}else if(next_message_owner == "others" && owner == "others" && from_user_id != next_message_owner_id){
									owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
								}
							} else if(x == message_count - 1){
								if(owner == "others"){
									owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
								}else if( owner == "me"){
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}
							}  
							
							var date_string = "";
							if(parseInt(day_counter) != parseInt(next_message_day_counter)){
								switch(parseInt(day_counter)){
									case 0:
									date_string = "<span class='message_group_date'>Today</span>";
									break;
									case 1:
									date_string = "<span class='message_group_date'>Yesterday</span>";
									break;
									default:
									date_string = "<span class='message_group_date'>"+formatted_date+"</span>";
									break;
								}
							}
							
							
							var date_html = date_string;
							var message_html = "<div class='message_container' id='message_container-"+message_id+"' data-id='"+from_user_id+"'>";
							 message_html += owner_string;
							if(owner == "others"){
								message_html += "<div class='message-other'>"+message+"</div>";
							}else{
								message_html += "<div class='message-mine'>"+message+"</div>";	
							}
							
							message_html += "<div style='clear:both;'></div>";
							
							
							message_html += "</div>";
							$("#chat_group-"+chat_id).find('.chat_messages').attr('data-oldest_id', message_id);
							$("#chat_group-"+chat_id).find('.chat_messages').attr('data-oldest_date', send_datetime);
							$("#chat_group-"+chat_id).find('.chat_messages').find('.chat_div-container').prepend(date_html + message_html);
							
							x++;
						}
						
						
						var chat_container = $("#chat_group-"+chat_id).find(".chat_messages").find(".chat_div-container");
						var latest_id = chat_container.find('.message_container').last().attr('id');
						document.getElementById(latest_id).scrollIntoView();	
						
						
						if(unread_messages == false){
							if($("#li-groups_list-"+chat_id+" span.unread_count")){
								$("#li-groups_list-"+chat_id+" span.unread_count").remove();
							}
						}
						
						bindscroll_function("#chat_group-"+chat_id + " .chat_message_scroll");
					}
					
					make_selectedChatbox("chat_group-"+chat_id);
					
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	
	function get_chat_list(){
		var url = base_url + "index.php/chat/ajax_get_chat_list";
		var data = {action: "get_chat_list", csrf_gd : Cookies.get('csrf_gd')};
		
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				if(response.result == "success"){
					create_chat_list(response)
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
		
	function create_chat_list(data){
		var chat_list_count = data.chat_list_count;
		var users = data.users;
		var users_count = data.users_count;
		var groups = data.groups;
		var groups_count = data.groups_count;
		var unread_messages = data.unread_messages;
		var unread_count = unread_messages.count;
		var users_unread = unread_messages.users;
		var users_unread_count = users_unread.count;
		var users_unread_messages = users_unread.messages;
		var groups_unread = unread_messages.groups;
		var groups_unread_count = groups_unread.count;
		var groups_unread_messages = groups_unread.messages;
		
	
		var x = 0;
		var users_string = "";
		while(x < users_count){
			var user_id = users[x]['user_id'];
			var full_name = users[x]['first_name']+" "+users[x]['last_name'];
			
			users_string += "<li class='li-chat_list li-users_list' id='li-users_list-"+user_id+"' data-id='"+user_id+"' data-name='"+full_name+"'>";
			users_string += " <img class='pic-users_list'>";
			users_string += "<p>" + full_name + "</p>";
			if(users_unread_messages[user_id] != undefined){
				users_string += "<span class='badge unread_count ng-binding margin_left_5'>" +users_unread_messages[user_id]['unread']+ "</span>";	
			}
			
			users_string += "</li>";
			
			x++;
		}
		
		var z = 0;
		var groups_string = "";
		while(z < groups_count){
			var chat_group_id = groups[z]['chat_group_id'];
			var chat_name = groups[z]['name'];
			groups_string += "<li class='li-chat_list li-groups_list' id='li-groups_list-"+chat_group_id+"' data-id='"+chat_group_id+"' data-name='"+chat_name+"'>";
			groups_string += " <img class='pic-groups_list'>";
			groups_string += "<p>" + chat_name + "</p>";
			if(users_unread_messages[user_id] != undefined){
				groups_string += "<span class='badge unread_count ng-binding margin_left_5'>" +users_unread_messages[user_id]['unread']+ "</span>";	
			}
			
			groups_string += "</li>";
			
			z++;
		}
		
		$(".ul-users_list").html(users_string);	
		$(".ul-groups_list").html(groups_string);	
		$(".div_chat_list").removeClass("hide");
		$(".div_chat_list-bottom").removeClass("hide");
	}
	
	function create_chat_box(chat_id, name, type){
		var div_string = "";
		var chat_string = "";
		var isListHidden = $(".div_chat_list").hasClass("hide");
		var isListBottomHidden = $(".div_chat_list-bottom").removeClass("hide");
		
		
		
		if((users_on_chat[chat_id] == undefined && type == "user")
			||
			(groups_on_chat[chat_id] == undefined && type == "group")
		){
			var div_chat_id = "chat_"+type+"-"+chat_id;
			
			div_string += "<div class='chat_div' id='"+div_chat_id+"' data-id='"+chat_id+"' data-type='"+type+"'>";
				div_string += "<div class='chat_div-maximize' data-id='"+chat_id+"'>";
						/* title  */
						div_string += "<div class='chat_title'>";
							div_string += "<div class='chat_div-container'>";
								div_string += "<p class='chat_name'>" +name;
									div_string += "<span class='remove_chat_div glyphicon glyphicon-remove' aria-hidden='true'></span>"
								div_string += "</p>";
							div_string += "</div>";
						div_string += "</div>";
						/* messages  */
						div_string += "<div class='chat_messages' data-latest_id='0' data-oldest_id='0' data-latest_date='0000-00-00 00:00:00' data-oldest_message='false' data-has_unread='false'>";
							div_string += "<div class='chat_div-container chat_message_scroll'>";
							div_string += "</div>";
						div_string += "</div>";
						/* create message */
						div_string += "<div class='chat_create'>";
							div_string += "<div class='chat_div-container'>";
								div_string += "<textarea class='my_message' placeholder='Type a message'>"
								div_string += "</textarea>"
							div_string += "</div>";
						div_string += "</div>";
						/* send message */
						div_string += "<div class='chat_send'>";
							div_string += "<div class='chat_div-container'>";
								div_string += "<div class='col-md-4'></div>"
								div_string += "<div class='col-md-4'></div>"
								div_string += "<div class='col-md-4'>";
								div_string += "<input type='button' class='send_user_message_btn btn btn-sm btn-primary' value='Reply'>";
								div_string += "</div>";
							div_string += "</div>";
						div_string += "</div>";
				div_string += "</div>";
				div_string += "<div class='chat_div-minimize hide' data-id='"+chat_id+"'>";
					div_string += "<div class='chat_div-container'>";
					div_string += "<p class='chat_name'>" +name;
						div_string += "<span class='remove_chat_div glyphicon glyphicon-remove' aria-hidden='true'></span>"
					div_string += "</p>";
					div_string += "</div>";
				div_string += "</div>";
			div_string += "</div>";
		
			if(numKeys(users_on_chat) + numKeys(groups_on_chat) == 3){
				var ids = getChatBoxId();
				if(ids.type == "user"){
					delete(users_on_chat[ids.id]);
				}else if(ids.type == "group"){
					delete(groups_on_chat[ids.id]);
				}	
				
				$("#"+ids.chat_box_id).remove();
			}	
			
			$(".div_chats-container").append(div_string);
			if(type == 'user'){
				get_user2user_conversation(chat_id, "latest");	
				
				users_on_chat[chat_id] = chat_id;
				make_selectedChatbox("chat_user-"+chat_id);
			}else if(type == 'group'){
				get_user2group_conversation(chat_id, "latest");	
				groups_on_chat[chat_id] = chat_id;
				make_selectedChatbox("chat_group-"+chat_id);
			}
			bindonEnter_SendMessage("#chat_"+type+"-"+chat_id+ " .my_message");
		}else{
			if($("#chat_"+type+"-"+chat_id+" div.chat_div-maximize").hasClass("hide")){
				$("#chat_"+type+"-"+chat_id+" div.chat_div-maximize").removeClass("hide");
				$("#chat_"+type+"-"+chat_id+" div.chat_div-minimize").addClass("hide");
			}
			
			make_selectedChatbox("chat_"+type+"-"+chat_id);
		}
	}
	
	function numKeys(obj){		
		var count = 0;
		for(var prop in obj){
			count++;
		}
		return count;
	}
	
	function getChatBoxId(){
		var chat_box_id = $(".div_chats-container div.chat_div").last().attr('id');
		var id = $(".div_chats-container div.chat_div").last().attr('data-id');
		var type = $(".div_chats-container div.chat_div").last().attr('data-type');
		var data = {chat_box_id : chat_box_id, id : id, type: type};
		return data;
	}
	
	
	
	
	function load_UnreadMessages () {
		if((numKeys(users_on_chat) > 0) || (numKeys(groups_on_chat) > 0)){
			var url = base_url + "index.php/chat/ajax_get_unread_messages";
			var chat_ids = get_others_chat_data();
			
			$("#ajax-msg").addClass('hide');
			
			var data = {
					chat_ids: chat_ids,
					csrf_gd : Cookies.get('csrf_gd')
				};
			jQuery.ajax({
				type : "post",
				dataType : "json",	
				data : data,				
				url: url, 
				success: function(response){
					if(response.result == "success" ){
						if(response.users['unread_count'] > 0){
							var x = 0;
							while(x < response.users['unread_count']){
								var from_user_id = response.users['unread_messages'][x][0]['from_user_id'];
								if(users_on_chat[from_user_id] != undefined){
									add_message_to_userChatbox(response.users['unread_messages'][x]);	
								}
								x++;
							}
						}
						if(response.groups['unread_count'] > 0){
							var x = 0;
							while(x < response.groups['unread_count']){
								var chat_group_id = response.groups['unread_messages'][x][0]['chat_group_id'];
								if(groups_on_chat[chat_group_id] != undefined){
									add_message_to_groupChatbox(response.groups['unread_messages'][x]);	
								}
								x++;
							}
						}
							
					}
					load_UnreadMessagesCount();
					$("#ajax-msg").removeClass('hide');
				},
				error: function(e){
					$("#ajax-msg").removeClass('hide');
				}
			});
			
		}else{
			load_UnreadMessagesCount();
		}
	}
	
	function load_UnreadMessagesCount() {
		var data = {action:"get_unread_messages_count", csrf_gd : Cookies.get('csrf_gd')};
		var url = base_url + "index.php/chat/ajax_get_unread_messages_count";
		
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data, 
			dataType : "json",		
			url: url, 
			success: function(response){
				if(response.total > 0){
					$("#chat_btn .unread_count").removeClass("badge_zero");
					$("#chat_btn .unread_count").text(response.total);
					
					if(response.users['unread_count'] > 0){
						var users_keys = Object.keys(response.users['unread_messages']);
						var x = 0;
						while(x < response.users['unread_count']){
							var user_id = users_keys[x];
							
							if($("#li-users_list-"+user_id).length > 0 && $("#li-users_list-"+user_id + " .unread_count").length > 0){
								$("#li-users_list-"+user_id + " .unread_count").text(response.users['unread_messages'][user_id]['unread'])
							}else if($("#li-users_list-"+user_id).length > 0 && $("#li-users_list-"+user_id + " .unread_count").length == 0){
								users_string = "<span class='badge unread_count ng-binding margin_left_5'>" +response.users['unread_messages'][user_id]['unread']+ "</span>";	
								$("#li-users_list-"+user_id ).append(users_string);
							}
							x++;
						}
					}
					
					if(response.groups['unread_count'] > 0){
						var groups_keys = Object.keys(response.groups['unread_messages']);
						var x = 0;
						while(x < response.groups['unread_count']){
							var chat_group_id = groups_keys[x];
							
							if($("#li-groups_list-"+chat_group_id).length > 0 && $("#li-groups_list-"+chat_group_id + " .unread_count").length > 0){
								$("#li-groups_list-"+chat_group_id + " .unread_count").text(response.groups['unread_messages'][chat_group_id]['unread'])
							}else if($("#li-groups_list-"+chat_group_id).length > 0 && $("#li-groups_list-"+chat_group_id + " .unread_count").length == 0){
								users_string = "<span class='badge unread_count ng-binding margin_left_5'>" +response.groups['unread_messages'][chat_group_id]['unread']+ "</span>";	
								$("#li-groups_list-"+chat_group_id ).append(users_string);
							}
							x++;
						}
					}
				}else{
					if(!$("#chat_btn .unread_count").hasClass('badge_zero')){
						$("#chat_btn .unread_count").addClass("badge_zero");
						$("#chat_btn .unread_count").text(0);
					}
					
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
		
			
	}
	
	function get_others_chat_data(){
		var users_on_chat_array = objToArray(users_on_chat);
		var groups_on_chat_array = objToArray(groups_on_chat);
		
		var users_data = [];
		var groups_data = [];
		
		var x = 0;
		while(x < numKeys(users_on_chat)){
			var other_user_id = users_on_chat_array[x];
			var last_id = $("#chat_user-"+other_user_id).find(".chat_messages").attr('data-latest_id');
			var last_date = $("#chat_user-"+other_user_id).find(".chat_messages").attr('data-latest_date');
			
			users_data.push({other_user_id: other_user_id, last_id: last_id, last_date: last_date});
			x++;
		}
		
		var z = 0;
		while(z < numKeys(groups_on_chat)){
			var chat_id = groups_on_chat_array[z];
			var last_id = $("#chat_group-"+chat_id).find(".chat_messages").attr('data-latest_id');
			var last_date = $("#chat_group-"+chat_id).find(".chat_messages").attr('data-latest_date');
			
			groups_data.push({chat_group_id: chat_id, last_id: last_id, last_date: last_date});
			z++;
		}
		var data = {
			users : users_data,
			groups: groups_data
		};
		
		return data;
	}
	
	
	function add_message_to_userChatbox(data){
		
		var new_messages_count = data.length;
		var last_chat_id = 0;
		var from_user_id = data[0]['from_user_id'];
		var x = 0;
		var added_new_message = 0;
		while(x < new_messages_count){
			
			var other_user_id = data[x]['from_user_id'];
			var first_name = data[x]['first_name'];
			var message = data[x]['message'];
			var message_id = data[x]['chat_user_message_id'];	
			var send_datetime = data[x]['send_datetime'];	
			var latest_id =  $("#chat_user-"+other_user_id + " .chat_messages").attr('data-latest_id');
			
			if(message_id > latest_id){
				var last_message_con = $("#chat_user-"+other_user_id +" .chat_messages .message_container").last().find("div").first();
				var owner_string = "";
				if(last_message_con.hasClass('message-mine')){
					owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
				}
				
				
				
				var message_html = "<div class='message_container' id='message_container-"+message_id+"'>";
				message_html += owner_string;
				message_html += "<div class='message-other'>"+message+"</div>";
				message_html += "<div style='clear:both;'></div>";
				message_html += "</div>";
				
				$("#chat_user-"+other_user_id).find('.chat_messages').find('.chat_div-container').append(message_html);
				$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-latest_id', message_id);
				$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-latest_date', send_datetime);
							
				var chat_container = $("#chat_user-"+other_user_id).find(".chat_messages").find(".chat_div-container");
				var last_message_id = chat_container.find('.message_container').last().attr('id');
				document.getElementById(last_message_id).scrollIntoView();
				
				seen_message("#chat_user-"+other_user_id, "hide", 0, 0);
				
				last_chat_id = message_id;
				
				added_new_message++;
			}
			x++;
			if(added_new_message > 0){
				if($("#chat_user-"+other_user_id).hasClass('selected_chatbox')){
					read_unread_userMsgs_update("#chat_user-"+other_user_id, other_user_id, last_chat_id);
				}else{
					$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-has_unread', 'true');
					add_message_notif("#chat_user-"+other_user_id, added_new_message);
				}
			}
		}
	}
	
	function add_message_to_groupChatbox(data){
		
		var new_messages_count = data.length;
		var last_chat_id = 0;
		
		var x = 0;
		var added_new_message = 0;
		while(x < new_messages_count){
			
			var chat_id = data[x]['chat_group_id'];
			var from_user_id = data[x]['from_user_id'];
			var first_name = data[x]['first_name'];
			var message = data[x]['message'];
			var message_id = data[x]['chat_group_message_id'];	
			var send_datetime = data[x]['send_datetime'];	
			var latest_id =  $("#chat_group-"+chat_id + " .chat_messages").attr('data-latest_id');
			
			console.log(message_id);
			console.log(latest_id);
			if(message_id > latest_id){
				
				var last_from_user_id = $("#chat_group-"+chat_id +" .chat_messages .message_container").last().attr('data-id');
				var last_message_con = $("#chat_group-"+chat_id +" .chat_messages .message_container").last().find("div").first();
				var owner_string = "";
				if(last_message_con.hasClass('message-mine')){
					owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
				}else{
					if(last_from_user_id != from_user_id){
						owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
					}
				}
					
				var message_html = "<div class='message_container' id='message_container-"+message_id+"' data-id='"+from_user_id+"'>";
				message_html += owner_string;
				message_html += "<div class='message-other'>"+message+"</div>";
				message_html += "<div style='clear:both;'></div>";
				message_html += "</div>";
				
				$("#chat_group-"+chat_id).find('.chat_messages').find('.chat_div-container').append(message_html);
				$("#chat_group-"+chat_id).find('.chat_messages').attr('data-latest_id', message_id);
				$("#chat_group-"+chat_id).find('.chat_messages').attr('data-latest_date', send_datetime);
							
				var chat_container = $("#chat_group-"+chat_id).find(".chat_messages").find(".chat_div-container");
				var last_message_id = chat_container.find('.message_container').last().attr('id');
				document.getElementById(last_message_id).scrollIntoView();
				
				seen_message("#chat_group-"+chat_id, "hide", 0, 0);
				
				last_chat_id = message_id;
				
				added_new_message++;
			}
			x++;
			if(added_new_message > 0){
				if($("#chat_group-"+chat_id).hasClass('selected_chatbox')){
					read_unread_groupMsgs_update("#chat_group-"+chat_id, chat_id, last_chat_id);
				}else{
					$("#chat_group-"+chat_id).find('.chat_messages').attr('data-has_unread', 'true');
					add_message_notif("#chat_group-"+chat_id, added_new_message);
				}
			}
		}
	}
	
	function objToArray(obj){
		return $.map(obj, function(value, index) {
			return [value];
		});
	}

	function make_selectedChatbox(chat_id){
		$(".chat_div").removeClass('selected_chatbox');
		
		var chat_div_id = "#"+chat_id;
		$(chat_div_id).addClass('selected_chatbox');
		$(chat_div_id).find(".my_message").focus();
		
		var user_id = $(chat_div_id).attr("data-id"); 
		var type = $(chat_div_id).attr("data-type"); 
		var has_unread = $(chat_div_id).find(".chat_messages").attr("data-has_unread"); 
		var last_chat_id = $(chat_div_id).find(".chat_messages").attr("data-latest_id"); 
		if(has_unread == 'true'){
			if(type == 'user'){
				console.log('a');
				read_unread_userMsgs_update(chat_div_id, user_id, last_chat_id);	
			}else{
				console.log('b');
				read_unread_groupMsgs_update(chat_div_id, user_id, last_chat_id);
			}
		}
	}
	
	function bindscroll_function(id){
        $(id).on('scroll', function(){
			var scroll_top = $(id).scrollTop();
			var current_height = $(id).height();	
			var message_container = $(id).parent(".chat_messages");
			var has_olders_message = message_container.attr("data-oldest_message");
			var chat_id = $(id).parents('div.chat_div').attr("data-id");
			var type = $(id).parents('div.chat_div').attr("data-type");
			
			if(has_olders_message == 'false'){
				if((scroll_top <= 150 && scroll_top >= 130) || (scroll_top <= 50 && scroll_top >= 30) ){
					var oldest_id = message_container.attr("data-oldest_id");
					if(type == 'user'){
						get_user_older_message(chat_id, oldest_id);	
					}else{
						get_group_older_message(chat_id, oldest_id);	
					}
					
				}	
			}
		});
    }
	
	
	function get_group_older_message(chat_group_id, last_chat_id){
		var url = base_url + "index.php/chat/ajax_get_group_older_messages";
		var data = {
				chat_group_id : chat_group_id, 
				last_chat_id: last_chat_id,
				csrf_gd : Cookies.get('csrf_gd')
			};
		
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				
				if(response.result == "success"){
					var older_message_count = response.older_message_count;
					var messages = response.older_messages;
					if(older_message_count > 0){
						var x = 0;
						while(x < older_message_count){
							var from_user_id = messages[x]['from_user_id'];
							var owner = messages[x]['owner'];
							var message = messages[x]['message'];
							var message_id = messages[x]['chat_group_message_id'];
							var send_datetime = messages[x]['send_datetime'];
							var first_name = messages[x]['first_name'];
							var last_name = messages[x]['last_name'];
							var day_counter = messages[x]['day_counter'];
							var formatted_date = messages[x]['formatted_date'];
							var owner_string = "";
							
							if(x < older_message_count - 1){
								next_message_owner = messages[x+1]['owner'];
								next_message_owner_id = messages[x+1]['from_user_id'];
								next_message_day_counter = messages[x+1]['day_counter'];
								
								if(next_message_owner == "me" && owner == "others"){
									owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
								}else if(next_message_owner == "others" && owner == "me"){
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}else if(next_message_owner == "otehrs" && owner == "others" && from_user_id != next_message_owner_id){
									owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
								}
							}else if(x == older_message_count - 1){
								if(owner == "others"){
									owner_string = "<span class='chat_sender-name'>"+first_name+" :</span>";
								}else{
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}
							} 
							
							var date_string = "";
							if(parseInt(day_counter) != parseInt(next_message_day_counter)){
								switch(parseInt(day_counter)){
									case 0:
									date_string = "<span class='message_group_date'>Today</span>";
									break;
									case 1:
									date_string = "<span class='message_group_date'>Yesterday</span>";
									break;
									default:
									date_string = "<span class='message_group_date'>"+formatted_date+"</span>";
									break;
								}
							}
							
							var date_html = date_string;
							var message_html = "<div class='message_container' id='message_container-"+message_id+"' data-id='"+from_user_id+"'>";
							 message_html += owner_string;
							if(owner == "others"){
								message_html += "<div class='message-other'>"+message+"</div>";
							}else{
								message_html += "<div class='message-mine'>"+message+"</div>";	
							}
							
							message_html += "<div style='clear:both;'></div>";
							message_html += "</div>";
							$("#chat_group-"+chat_group_id).find('.chat_messages').attr('data-oldest_id', message_id);
							$("#chat_group-"+chat_group_id).find('.chat_messages').find('.chat_div-container').prepend(date_html + message_html);
							x++;
						}
					}
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	

	
	function get_user_older_message(user_id, last_chat_id){
		var url = base_url + "index.php/chat/ajax_get_user_older_messages";
		var data = {
				user_id : user_id, 
				last_chat_id: last_chat_id,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				var other_user_id = user_id;
				if(response.result == "success"){
					var older_message_count = response.older_message_count;
					var messages = response.older_messages;
					if(older_message_count > 0){
						var x = 0;
						while(x < older_message_count){
							var from_user_id = messages[x]['from_user_id'];
							var to_user_id = messages[x]['to_user_id'];
							var message = messages[x]['message'];
							var message_id = messages[x]['chat_user_message_id'];
							var send_datetime = messages[x]['send_datetime'];
							var f_fname = messages[x]['f_fname'];
							var t_fname = messages[x]['t_fname'];
							var day_counter = messages[x]['day_counter'];
							var formatted_date = messages[x]['formatted_date'];
							var owner_string = "";
							
							if(x < older_message_count - 1){
								next_message_owner_id = messages[x+1]['from_user_id'];
								next_message_owner_name = messages[x+1]['f_fname'];
								next_message_owner_t_fname = messages[x+1]['t_fname'];
								next_message_day_counter = messages[x+1]['day_counter'];
								
								if(from_user_id == other_user_id && next_message_owner_name != f_fname){
									owner_string = "<span class='chat_sender-name'>"+f_fname+" :</span>";
								}else if(from_user_id != other_user_id && next_message_owner_name != f_fname){
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}
							}else if(x == older_message_count - 1){
								if(from_user_id == other_user_id){
									owner_string = "<span class='chat_sender-name'>"+f_fname+" :</span>";
								}else{
									owner_string = "<span class='chat_receiver-name'>Me :</span>";
								}
							} 
							var date_string = "";
							if(parseInt(day_counter) != parseInt(next_message_day_counter)){
								switch(parseInt(day_counter)){
									case 0:
									date_string = "<span class='message_group_date'>Today</span>";
									break;
									case 1:
									date_string = "<span class='message_group_date'>Yesterday</span>";
									break;
									default:
									date_string = "<span class='message_group_date'>"+formatted_date+"</span>";
									break;
								}
							}
							date_html = date_string;
							var message_html = "<div class='message_container' id='message_container-"+message_id+"'>";
							 message_html += owner_string;
							if(from_user_id == other_user_id){
								message_html += "<div class='message-other'>"+message+"</div>";
							}else{
								message_html += "<div class='message-mine'>"+message+"</div>";	
							}
							
							message_html += "<div style='clear:both;'></div>";
							message_html += "</div>";
							$("#chat_user-"+other_user_id).find('.chat_messages').attr('data-oldest_id', message_id);
							$("#chat_user-"+other_user_id).find('.chat_messages').find('.chat_div-container').prepend(date_html+message_html);
							x++;
						}
					}
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	
	function seen_message(chat_div_id, show, message_id, date){
		if(show == "show"){
			var seen_message = '<span class="seen_message">Seen '+date+'</span>';
			$($chat_div_id).find('.message_container').last().append(seen_message);
		}else{
			$(chat_div_id).find(".seen_message").remove();
		}
	}
	
	function read_unread_userMsgs_update(chat_div_id, user_id, last_chat_id){
		var url = base_url + "index.php/chat/ajax_unread_userMsgs_update";
		var data = {
				user_id : user_id, 
				last_chat_id: last_chat_id,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				var other_user_id = user_id;
				if(response.result == "success"){
					$(chat_div_id).find('.chat_messages').attr('data-has_unread', 'false');
					remove_message_notif(chat_div_id);	
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	function add_message_notif(chat_div_id, message_count){
		var notif_element = '<span class="badge unread_count ng-binding margin_right_5">'+message_count+'</span>';
		
		
		if($(chat_div_id+ " .chat_div-maximize .chat_title .chat_name .unread_count").length == 0){
			$(chat_div_id).find(".chat_div-maximize").find(".chat_title").find(".chat_name").prepend(notif_element);
			$(chat_div_id).find(".chat_div-minimize").find(".chat_name").prepend(notif_element);
		}else{
			var current_count = parseInt($(chat_div_id+ " .chat_div-maximize .chat_title .chat_name .unread_count").text());
			var final_count = current_count + message_count;
			if(final_count > 10){
				final_count = "10+";
			}
			$(chat_div_id+ " .chat_div-maximize .chat_title .chat_name .unread_count").text(final_count);
			$(chat_div_id+ " .chat_div-minimize .chat_name .unread_count").text(final_count);
		}
	}
	
	function read_unread_groupMsgs_update(chat_div_id, chat_id, last_chat_id){
		var url = base_url + "index.php/chat/ajax_unread_groupMsgs_update";
		var data = {
				chat_id : chat_id, 
				last_chat_id: last_chat_id,
				csrf_gd : Cookies.get('csrf_gd')
			};
		$("#ajax-msg").addClass('hide');
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				var chat_id = chat_id;
				if(response.result == "success"){
					$(chat_div_id).find('.chat_messages').attr('data-has_unread', 'false');
					remove_message_notif(chat_div_id);	
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	function remove_message_notif(chat_div_id){
		$(chat_div_id).find(".chat_div-maximize").find(".chat_title").find(".chat_name").find(".unread_count").remove();
		$(chat_div_id).find(".chat_div-minimize").find(".chat_name").find(".unread_count").remove();
		
		var unread_count = parseInt($("#chat_btn .unread_count").text());
		if(unread_count > 0){
			var new_unread_count = unread_count - 1;
			$("#chat_btn .unread_count").text(new_unread_count);
			if(new_unread_count == 0){
				$("#chat_btn .unread_count").addClass("badge_zero");
			}
			
			var id = $(chat_div_id).attr("data-id");
			var type = $(chat_div_id).attr("data-type");
			$("#li-"+type+"s_list-"+id).find(".unread_count").remove();
		}
	}
	
	function check_chat_exist(){
		var url = base_url + "index.php/chat/ajax_chat_group_exist";
		var data = {action:"chat_group_exist", csrf_gd : Cookies.get('csrf_gd')};
		
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	function send_group_message(chat_group_id, message){
		var url = base_url + "index.php/chat/ajax_send_group_message";
		var data = {
				chat_id : chat_group_id, 
				message: message,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$("#ajax-msg").addClass('hide');
		
		jQuery.ajax({
			type : "post",
			data : data,
			dataType : "json",		
			url: url, 
			success: function(response){
				if(response.result == "success"){
					var chat_id = response.message['chat_group_id'];
					var message = response.message['message'];
					var from_user_id = response.message['from_user_id'];
					var message_id = response.message['chat_group_message_id'];
					
					var last_message_con = $("#chat_group-"+chat_id +" .chat_messages .message_container").last().find("div").first();
					var owner_string = "";
					if(last_message_con.hasClass('message-other')){
						owner_string = "<span class='chat_receiver-name'>Me :</span>";
					}
					
					var message_html = "<div class='message_container' id='message_container-"+message_id+"' data-id='"+from_user_id+"'>";
						message_html += owner_string;
						message_html += "<div class='message-mine'>"+message+"</div>";
						message_html += "<div style='clear:both;'></div>";
						message_html += "</div>";
					
					$("#chat_group-"+chat_id).find('.chat_messages').find('.chat_div-container').append(message_html);
					seen_message("#chat_group-"+chat_id, "hide", 0, '');
					$("#chat_group-"+chat_id).find('.chat_messages').attr("data-latest_id",message_id);	
					$("#chat_group-"+chat_id).find('.my_message').val("");
					
					
					var chat_container = $("#chat_group-"+chat_id).find(".chat_messages").find(".chat_div-container");
					var last_id = chat_container.find('.message_container').last().attr('id');
					document.getElementById(last_id).scrollIntoView();
					
					make_selectedChatbox("chat_group-"+chat_id);
					
				}
				$("#ajax-msg").removeClass('hide');
			},
			error: function(e){
				$("#ajax-msg").removeClass('hide');
			}
		});
	}
	
	/* REPEATING EVENTS */
	setInterval(load_UnreadMessages, 3000);    
	
	
	
});


	
