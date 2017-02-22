$(document).ready(function() {

	$("#plan li").each(function(){
		$(this).hover(function(){
			$(this).find('.chapter-option').show();
		}, function(){
			$(this).find('.chapter-option').hide();
		});
	});

	$(document).ajaxSend(function() {
        /* $("#ajax-msg").show(); */
        $("#ajax-msg").removeClass('hide');
    });
    $('document').ajaxStop(function() {
        /* $("#ajax-msg").hide(); */
		$("#ajax-msg").addClass('hide');
    });


    /** Prevent enter key when submitting form **/
    $('.no_enter').keydown(function(event){
	    if(event.keyCode == 13) 
	    {
	      event.preventDefault();
	      return false;
	    }
	});
	/** END **/

	/** Re order via click up and down **/
    $('.move-up').click(function(){
      var topic_id = $(this).attr("data-topic-id-tomoved");
      var position = $(this).attr("data-position");
      var meeting_id = $(this).attr("data-topic-meeting-id");

      $(this).parents('.topic-list-parent-cont').insertBefore($(this).parents('.topic-list-parent-cont').prev());
      
      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/move_topic_by_up_arrow",
      	data: {topic_id : topic_id, position : position , meeting_id : meeting_id, csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		//location.reload();
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });

    $('.move-down').click(function(){
      var topic_id = $(this).attr("data-topic-id-tomoved");
      var position = $(this).attr("data-position");
      var meeting_id = $(this).attr("data-topic-meeting-id");

      $(this).parents('.topic-list-parent-cont').insertAfter($(this).parents('.topic-list-parent-cont').next());

      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/move_topic_by_down_arrow",
      	data: {topic_id : topic_id, position : position , meeting_id : meeting_id, csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });


    $('.s_move_up').click(function(){
      var topic_id = $(this).attr("data-topicid");
      var position = $(this).attr("data-position");
      var subtopic_id = $(this).attr("data-subtopicid");

      $(this).parents('.subtopic-list-parent-cont').insertBefore($(this).parents('.subtopic-list-parent-cont').prev());
      
      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/move_subtopic_by_up_arrow",
      	success:function(response)
      	{
      		//location.reload();
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
              $.getScript(base_url+'public/meeting.js');
          	});
      	},
      	data: {topic_id : topic_id, position : position , subtopic_id : subtopic_id, csrf_gd: Cookies.get('csrf_gd') },
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });


    $('.s_move_down').click(function(){
      var topic_id = $(this).attr("data-topicid");
      var position = $(this).attr("data-position");
      var subtopic_id = $(this).attr("data-subtopicid");

      $(this).parents('.subtopic-list-parent-cont').insertAfter($(this).parents('.subtopic-list-parent-cont').next());
      
      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/move_subtopic_by_down_arrow",
      	data: {topic_id : topic_id, position : position , subtopic_id : subtopic_id, csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		//location.reload();
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
              $.getScript(base_url+'public/meeting.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });
    
    $('.topic_ntd_move_up').click(function(){
      var topic_id = $(this).attr("data-topic-id");
      var position = $(this).attr("data-position");
      var meeting_id = $(this).attr("data-ntd-meeting-id");
      var note_id = $(this).attr('data-noteid');

      $(this).parents('.ntd-topic-list-parent-cont').insertBefore($(this).parents('.ntd-topic-list-parent-cont').prev());
      
      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/move_topic_ntd_by_up_arrow",
      	data: {topic_id : topic_id, position : position , meeting_id : meeting_id, note_id : note_id ,csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}
      });
    });


    $('.topic_ntd_move_down').click(function(){
      var topic_id = $(this).attr("data-topic-id");
      var position = $(this).attr("data-position");
      var meeting_id = $(this).attr("data-ntd-meeting-id");
      var note_id = $(this).attr('data-noteid');

      $(this).parents('.ntd-topic-list-parent-cont').insertAfter($(this).parents('.ntd-topic-list-parent-cont').next());
      
      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/move_topic_ntd_by_down_arrow",
      	data: {topic_id : topic_id, position : position , meeting_id : meeting_id, note_id : note_id ,csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });


    $('.subtopic_ntd_up').click(function(){
      var topic_id = $(this).attr("data-topic-id");
      var position = $(this).attr("data-position");
      var meeting_id = $(this).attr("data-ntd-meeting-id");
      var subtopic_id = $(this).attr('data-subtopic-id');
      var note_id = $(this).attr('data-noteid');

      $(this).parents('.ntd-subtopic-list-parent-cont').insertBefore($(this).parents('.ntd-subtopic-list-parent-cont').prev());
      
      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/subtopic_ntd_reposition_up",
      	data: {topic_id : topic_id, position : position , meeting_id : meeting_id, note_id : note_id , subtopic_id: subtopic_id ,csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
              $.getScript(base_url+'public/meeting.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });

    $('.subtopic_ntd_down').click(function(){
      var topic_id = $(this).attr("data-topic-id");
      var position = $(this).attr("data-position");
      var meeting_id = $(this).attr("data-ntd-meeting-id");
      var subtopic_id = $(this).attr('data-subtopic-id');
      var note_id = $(this).attr('data-noteid');

      $(this).parents('.ntd-subtopic-list-parent-cont').insertAfter($(this).parents('.ntd-subtopic-list-parent-cont').next());

      $.ajax({
      	method:"POST",
      	url:base_url+"index.php/meeting/subtopic_ntd_reposition_down",
      	data: {topic_id : topic_id, position : position , meeting_id : meeting_id, note_id : note_id , subtopic_id: subtopic_id ,csrf_gd: Cookies.get('csrf_gd') },
      	success:function(response)
      	{
      		$('#meeting_topics').load(location.href + ' #meeting_topics', function(){
              $.getScript(base_url+'public/script.js');
              $.getScript(base_url+'public/meeting.js');
          	});
      	},
      	error:function(xhr)
      	{
      		console.log(xhr);
      	}

      });
    });



    // toggle sections on sidebar
    $("span.toggle-sections").each(function(){
    	var _this = $(this);
    	$(this).click(function(e){
    		e.preventDefault();
    		_this.parents('.list-group-item').find('ul.sub-menu').slideToggle();

    		$("i",_this).toggleClass("fa fa-plus fa fa-minus");
    	})
    });


    /** Submit ajax form when saving subtopic **/
    $('.submit-subtopic-form').submit(function(e){
      var topic_id = $(this).attr('data-form-id');
      var form_data = $('#save-subtopic'+topic_id).serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

      $.post(base_url+"index.php/meeting/save_meeting_subtopic", form_data, function(response){
        var obj = $.parseJSON(response);

        if(obj['error'] == 0)
        {
          	$('#item-'+topic_id+' #subtopic-item-'+topic_id).load(location.href + ' #item-'+topic_id+' #subtopic-item-'+topic_id, function(){
	            $.getScript(base_url+'public/meeting.js');
            });
            $('input[name=subtopic_title]').val("");
        }
        else
        {
          $.alert({
              title: 'Error!',
              content: obj['message'],
              confirmButtonClass: 'btn-danger',
          });
        }

      });
      	e.preventDefault();
     });



    
   
	// $('.btn-save-topic').bind('click', function(){
	// 	var form_data = $('#save_created_topic').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

	// 	$.post(base_url+"index.php/meeting/save_meeting_topic", form_data, function(response){
	// 		var obj = $.parseJSON(response);
	// 		var current_page = window.location.href;

	// 		if(obj['error'] == 0)
	// 		{
	// 			location.reload();
	// 			//$('#meeting_topics').load(current_page + " #meeting_topics, scripts");
	// 		}
	// 		else
	// 		{
	// 			$.alert({
	// 			    title: 'Error!',
	// 			    content: obj['message'],
	// 			    confirmButtonClass: 'btn-danger',
	// 			});
	// 		}
	// 	});
	// });



	// var loc = window.location.hash.substr(1);
	// if(loc){
	// 	$("ul#"+loc).parent("li.list-group-item").addClass("open");
	// 	$("ul#"+loc).show();

	// 	$("a[href=#"+loc+"]").find("i").toggleClass('fa fa-plus-square-o fa fa-minus-square-o');
	// }

	// // Plan Menu Toggle
	// $("#plan").find("li.list-group-item").each(function(){
	// 	var _this = $(this);

	// 	$(this).click(function(){

	// 		if($(this).find("ul.child").length){
	// 			$(this).toggleClass("open");

	// 			if ($(this).hasClass("open") ) {
	// 				_this.find('i').toggleClass('fa fa-plus-square-o fa fa-minus-square-o');

	// 			}
	// 			else{
	// 				_this.find('i').toggleClass('fa fa-minus-square-o fa fa-plus-square-o');

	// 			}
	// 		}
	// 	})
	// });

	// -- Plan Menu Toggle



	/** Meeting Module Scripts **/
	$('.show-hide-toggle').bind('click', function(){
		$(this).html("Hide Optional and CC");

		$('.show-hide-optional-cc').show();
		$('.show-hide-optional-cc').addClass('toggle-showing');

		if($('.show-hide-optional-cc').hasClass("toggle-showing"))
		{
			$('.show-hide-toggle').bind('click', function(){
				$(this).html("Add Optional and CC");
				$('.show-hide-optional-cc').hide();
				$('.show-hide-optional-cc').addClass('toggle-hidden');
				$('.show-hide-optional-cc').removeClass('toggle-showing');
			});
		}
		if($('.show-hide-optional-cc').hasClass("toggle-hidden"))
		{
			$('.show-hide-toggle').bind('click', function(){
				$(this).html("Hide Optional and CC");
				$('.show-hide-optional-cc').show();
				$('.show-hide-optional-cc').addClass('toggle-showing');
				$('.show-hide-optional-cc').removeClass('toggle-hidden');
			});
		}
	});

	$('.btn-add-topic').bind('click', function(){
		$('.topic-form-cont').slideDown("fast");
		$('.load-template-action').hide();
		$(this).hide();
		$('.input-topic-field').focus();
	});

	$('.toggle-hide-topic-cont').bind('click', function(){
		$('.topic-form-cont').slideUp('fast');
		$('.btn-add-topic').show();
		$('.load-template-action').show();
	});

	$('.show-topic').bind('click', function(){
		var element_id = $(this).next().attr("class");
		var subtopic_flag = $(this).attr("data-subtopic-count");
		$("."+element_id).show();
		$(".subtopic-title-input-field"+subtopic_flag).focus();
	});

	$('.btn-hide-subtopic').bind('click', function(){
		var element_id = $(this).attr("id");
		$(".subtopic-form-cont"+element_id).hide();
	});


	$('.btn-save-meeting').bind('click', function(){
		var form_data = $('#save_meeting_info').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

		$.post(base_url+"index.php/meeting/save_meeting_info", form_data, function(response){
			var obj = $.parseJSON(response);
			var last_inserted_id = obj['last_inserted_id'];
			var meeting_organ_id = obj['meeting_organ_id'];

			if(obj['error'] == 0)
			{
				$.alert({
				    title: 'Success!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-success',
				});
				setTimeout(function(){
					window.location.href = base_url+"index.php/meeting/workspace/"+last_inserted_id+"/"+meeting_organ_id;
				},1000);
			}
			else
			{
				$.alert({
				    title: 'Error!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-danger',
				});
			}
		});
	});


	$('.btn-update-meeting').bind('click', function(){
		var meeting_id = $(this).attr('data-btn-meeting-id');
		var form_data = $('#save_meeting_info').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

		$.post(base_url+"index.php/meeting/update_meeting_info/"+meeting_id, form_data, function(response){
			var obj = $.parseJSON(response);

			if(obj['error'] == 0)
			{
				$.alert({
				    title: 'Success!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-success',
				});
				// setTimeout(function(){
				// 	location.reload();
				// },1000);
			}
			else
			{
				$.alert({
				    title: 'Error!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-danger',
				});
			}
		});
	});


	// $('.btn-save-subtopic').bind('click', function(){
	// 	var topic_id = $(this).attr('data-topic-id');
	// 	var form_data = $('#save-subtopic'+topic_id).serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

	// 	$.post(base_url+"index.php/meeting/save_meeting_subtopic", form_data, function(response){
	// 		var obj = $.parseJSON(response);

	// 		if(obj['error'] == 0)
	// 		{
				
	// 			location.reload();
	// 		}
	// 		else
	// 		{
	// 			$.alert({
	// 			    title: 'Error!',
	// 			    content: obj['message'],
	// 			    confirmButtonClass: 'btn-danger',
	// 			});
	// 		}
	// 	});
	// });


	$('.btn-save-saveas-actions').bind('click', function(){
		var topic_id = $(this).attr('data-topic-id');
		var form_data = $('#add-topic-note-task-decision'+topic_id).serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

		$.post(base_url+"index.php/meeting/save_meeting_note", form_data, function(response){
			var obj = $.parseJSON(response);

			if(obj['error'] == 0)
			{
				location.reload();
			}
			else
			{
				$.alert({
				    title: 'Error!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-danger',
				});
			}
		});
	});


	$('.btn-delete-meeting').bind('click', function(){
		var _this = $(this);
    	var meeting_id = $(this).attr('data-id');

    	$.confirm({
    		title: 'Confirmation',
    		content: 'This will be permanently deleted. Proceed?',
    		confirmButtonClass: 'btn-success',
    		cancelButtonClass: 'btn-danger',
    		onAction: function(action){
		        if(action === "confirm")
		        {
		        	$.ajax({
			    		type:"POST",
			    		url:base_url+"index.php/meeting/delete_meeting",
			    		data: {meeting_id : meeting_id, csrf_gd: Cookies.get('csrf_gd') },
			    		cache:false,
			    		success:function(response)
			    		{
			    			var obj = $.parseJSON(response);
			    			var current_page = window.location.href;


			    			if(obj['error'] == 0)
			    			{
			    				_this.parent().parent().parent().fadeOut(300);
			    			}
			    			else
			    			{
			    				$.alert({
								    title: 'Error!',
								    content: obj['message'],
								    confirmButtonClass: 'btn-danger',
								});
			    			}
			    		},
			    		error:function(error)
			    		{
			    			console.log(error);
			    		},
			    	});
		        }
		    }
    	});
    });


    $('.delete-topic-link').bind('click', function(){
		var _this = $(this);
    	var topic_id = $(this).attr('data-delete-topic-id');

    	$.confirm({
    		title: 'Confirmation',
    		content: 'This will be permanently deleted. Proceed?',
    		confirmButtonClass: 'btn-success',
    		cancelButtonClass: 'btn-danger',
    		onAction: function(action){
		        if(action === "confirm")
		        {
		        	$.ajax({
			    		type:"POST",
			    		url:base_url+"index.php/meeting/delete_topic",
			    		data: {topic_id : topic_id, csrf_gd: Cookies.get('csrf_gd')},
			    		cache:false,
			    		success:function(response)
			    		{
			    			var obj = $.parseJSON(response);
			    			if(obj['error'] == 0)
			    			{
			    				location.reload();
			    			}
			    			else
			    			{
			    				$.alert({
								    title: 'Error!',
								    content: obj['message'],
								    confirmButtonClass: 'btn-danger',
								});
			    			}
			    		},
			    		error:function(error)
			    		{
			    			console.log(error);
			    		},
			    	});
		        }
		    }

	    });
    });


    $('.btn-save-updated-topic-info').bind('click', function(){
    	var form_data = $('#update-topic-information').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

    	$.post(base_url+"index.php/meeting/update_topic_information", form_data, function(response){
			var obj = $.parseJSON(response);

			if(obj['error'] == 0)
			{
				location.reload();
			}
			else
			{
				$.alert({
				    title: 'Error!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-danger',
				});
			}
		});
    });

    $('.btn-save-updated-subtopic-info').bind('click', function(){
    	var form_data = $('#update-subtopic-information').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

    	$.post(base_url+"index.php/meeting/update_subtopic_information", form_data, function(response){
			var obj = $.parseJSON(response);

			if(obj['error'] == 0)
			{
				location.reload();
			}
			else
			{
				$.alert({
				    title: 'Error!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-danger',
				});
			}
		});
    });


	$('.btn-view-meeting-info').bind('click', function(){
		var _this = $(this);
    	var meeting_id = $(this).attr('data-id');

    	$.ajax({
    		type:"POST",
    		url:base_url+"index.php/meeting/get_meeting_info",
    		data: {meeting_id : meeting_id, csrf_gd: Cookies.get('csrf_gd')},
    		cache:false,
    		success:function(response)
    		{
    			_this.attr("data-content", response);

    		},
    		error:function(error)
    		{
    			console.log(error);
    		},
    	});

	});


	/** Global modals **/
	$('#open_meeting_email').bind('click', function(){
		var meeting_id = $(this).attr('data-meeting-id');
        
        $.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_email_tab/"+meeting_id,
		    title: "Email Agenda/Minutes",
		    confirmButton: false, // hides the confirm button.
		    cancelButton: false,
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_email_tab/'+meeting_id,{
        //     header:"Email Agenda/Minutes",
        //     button: false,
        // });
        // return false;
    });

    $('#open_meeting_print').bind('click', function(){
    	var meeting_id = $(this).attr('data-meeting-id');
        
        $.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_print_tab/"+meeting_id,
		    title: "Minutes Options",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_print_tab/'+meeting_id,{
        //     header:"Minutes Options",
        //     button: false,
        // });
        // return false;
    });

    $('#open_meeting_download').bind('click', function(){
    	var meeting_id = $(this).attr('data-meeting-id');
        
        $.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_download_tab/"+meeting_id,
		    title: "Download",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_download_tab/'+meeting_id,{
        //     header:"Download",
        //     button: false,
        // });
        // return false;
    });

    $('#open_meeting_attendance').bind('click', function(){
    	var meeting_id = $(this).attr('data-meeting-id');

    	$.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_attendance_tab/"+meeting_id,
		    title: "Attendance",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_attendance_tab/'+meeting_id,{
        //     header:"Attendance",
        //     button: false,
        // });
        // return false;
    });

    $('#open_meeting_templates').bind('click', function(){
    	var meeting_id = $(this).attr('data-meeting-id');
        
        $.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_template_tab/"+meeting_id,
		    title: "Templates",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_template_tab/'+meeting_id,{
        //     header:"Templates",
        //     button: false,
        // });
        // return false;
    });

    $('#open_meeting_templates_below').bind('click', function(){
    	var meeting_id = $(this).attr('data-meeting-id');
       	
       	$.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_template_tab/"+meeting_id,
		    title: "Templates",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_template_tab/'+meeting_id,{
        //     header:"Templates",
        //     button: false,
        // });
        // return false;
    });


    $('#open_meeting_followup').bind('click', function(){
    	var meeting_id = $(this).attr('data-meeting-id');

    	$.confirm({
		    content: "URL:"+base_url+"index.php/meeting/open_followup_tab/"+meeting_id,
		    title: "Follow-up Meeting Settings",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		});

        // modalbox(base_url + 'index.php/meeting/open_followup_tab/'+meeting_id,{
        //     header:"Follow-up Meeting Settings",
        //     button: false,
        // });
        // return false;
    });

    $('.edit-topic-link').bind('click', function(){
    	var topic_id = $(this).attr('data-edit-topic-id');
    	var meeting_id = $(this).attr('data-topic-meeting-id');

    	$.confirm({
		    content: "URL:"+base_url+"index.php/meeting/edit_topic_information/"+topic_id+"/"+meeting_id,
		    title: "Edit Topic Title",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger close_ajax_modal",
		    theme: 'material'
		  });
    });


	$('#upload_logo_image').bind('click', function(){
		$.confirm({
		    content: "URL:"+base_url+"index.php/meeting/setup_logo_image/",
		    title: "Set organization logo",
		    confirmButton: false, // hides the confirm button.
		    cancelButtonClass: "btn-danger",
		    theme: 'material'
		});

		// modalbox(base_url + 'index.php/meeting/setup_logo_image/',{
	 //        header:"Set organization logo",
	 //  		button: false,
	 //    });
	 //    return false;
	});


	$(".input1").keyup(function (e) {
	    if (e.keyCode == 13) {
	        // Do something
	    }
	});


    /** TOPICS Functionalities **/
    $('[data-toggle="popover"]').popover({
		html: true,
		trigger:"manual"
	});

	$('.show-task-actions').bind('click', function(){
		var topic_id = $(this).attr('data-topic-id');
		var id = $(this).attr('data-ntd-id');
		var text = $(this).attr('data-ntd-value');
		var $this = $(this);

		modalbox(base_url + 'index.php/meeting/open_create_task/'+id+"/"+encodeURI(text),{
        header:"<i class='fa fa-plus edit-task-header'></i> ",
        button: false,
    });
    return false;
	});

	$('.show-task-actions-subtopic').bind('click', function(){
		var subtopic_id = $(this).attr('data-subtopic-id');
		var id = $(this).attr('data-subtopic-ntd-id');
		var $this = $(this);

		$('[data-toggle="popover"]').popover("show");

		$.ajax({
			type:"POST",
			url:base_url+"index.php/meeting/load_task_assignto_due_view_subtopic/",
			data: {subtopic_id:subtopic_id, ntd_id:id, csrf_gd: Cookies.get('csrf_gd')},
			cache:false,
			success:function(response)
			{
				$this.attr('data-content', response);
				return false;
			}
		});
	});


	$('.close-popover-btn').on('click', function(){
  		$('.popover').hide();
  	});


    /** SUBTOPICS Functionalities **/
    
  	$('.show-subtopic-ntd').bind('click', function(){
       var subtopic_id = $(this).attr("data-subtopic-ntd-id");
       var $target = $(this).parent().next('.ntd-subtopic-container'+subtopic_id);

       $.ajax({
        type:"POST",
        url:base_url+"index.php/meeting/get_subtopic_ntd/"+subtopic_id,
        cache:false,
        success:function(response)
        {
          if(response == "")
          {
            $('.ntd-subtopic-container'+subtopic_id).html("<h5>Nothing to show.</h5>");
          }
          else
          {
            $('.ntd-subtopic-container'+subtopic_id).html(response);
          }
        }
      });

      return false;

    });




	  	$('.topic-input-field').bind('keyup',function(){
	  		var topic_id = $(this).attr('data-topic-input-field');

	  		if($(this).val().length >= 1)
	  		{
	  			$('.topic-items-cont-input'+topic_id).hide();
	  			$('.topic-items-cont'+topic_id).show();
	  			$('.topic-textarea-field'+topic_id).val($(this).val());
	  			$('.topic-textarea-field'+topic_id).focus();
	  		}
	  	});

	  	


	  	$('.btn-delete-topic-ntd').bind('click', function(){
	  		var _this = $(this);
	  		var id = $(this).attr("data-delete-topic-ntd");

	    	$.ajax({
	    		type:"POST",
	    		url:base_url+"index.php/meeting/delete_topic_ntd",
	    		data: {id : id, csrf_gd: Cookies.get('csrf_gd')},
	    		cache:false,
	    		success:function(response)
	    		{
	    			var obj = $.parseJSON(response);
	    			if(obj['error'] == 0)
	    			{
	    				_this.parent().parent().fadeOut(500);
	    			}
	    			else
	    			{
	    			}
	    		},
	    		error:function(error)
	    		{
	    			console.log(error);
	    		},
	    	});

	  	});

	  	$('.btn-delete-subtopic-ntd').bind('click', function(){
	  		var _this = $(this);
	  		var id = $(this).attr("data-delete-subtopic-ntd");

	    	$.ajax({
	    		type:"POST",
	    		url:base_url+"index.php/meeting/delete_subtopic_ntd",
	    		data: {id : id, csrf_gd: Cookies.get('csrf_gd')},
	    		cache:false,
	    		success:function(response)
	    		{
	    			var obj = $.parseJSON(response);
	    			if(obj['error'] == 0)
	    			{
	    				_this.parent().parent().fadeOut(500);
	    			}
	    			else
	    			{
	    			}
	    		},
	    		error:function(error)
	    		{
	    			console.log(error);
	    		},
	    	});

	  	});


	  	$('#meeting_topics').sortable({
	  		connectWith: 'ul#meeting_topics',
	  		axis:'y',
	  		beforeStop: function(ev, ui)
	  		{
            	if($(ui.item).hasClass('hasItems') && $(ui.placeholder).parent()[0] != this)
            	{
                	$(this).sortable('cancel');
		        }
		    },
	  		update: function (event, ui)
	  		{
		        var data = $(this).sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');

		        // POST to server using $.post or $.ajax
		        $.ajax({
		            data: data,
		            type: 'POST',
		            url: base_url+"index.php/meeting/sort_meeting_topics",
		        });
		    },
	  	});

	  	$('.meeting_subtopic_list').sortable({
	  		connectWith: 'ul.meeting_subtopic_list',
	  		axis:'y',
	  		update: function (event, ui)
	  		{
		        var data = $(this).sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');

		        // POST to server using $.post or $.ajax
		        $.ajax({
		            data: data,
		            type: 'POST',
		            url: base_url+"index.php/meeting/sort_meeting_subtopics",
		        });
		    },
	  	});


	  	$('.meeting_topics_ntds').sortable({
	  		connectWith: 'ul.meeting_topics_ntds',
	  		axis:'y',
	  		update: function (event, ui)
	  		{
		        var data = $(this).sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');

		        // POST to server using $.post or $.ajax
		        $.ajax({
		            data: data,
		            type: 'POST',
		            url: base_url+"index.php/meeting/sort_meeting_ntd",
		        });
		    },
	  	});

	  	$('.meeting_subtopics_ntds').sortable({
	  		connectWith: 'ul.meeting_subtopics_ntds',
	  		axis:'y',
	  		update: function (event, ui)
	  		{
		        var data = $(this).sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');

		        // POST to server using $.post or $.ajax
		        $.ajax({
		            data: data,
		            type: 'POST',
		            url: base_url+"index.php/meeting/sort_meeting_ntd",
		        });
		    },
	  	});




	  	$('.btn-create-new-meeting-agenda').bind('click', function(){
	  		var form_data = $('#agenda_create_new_meeting').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');
	  		var meeting_id = $(this).attr("data-meeting-id");
	  		var organ_id = $(this).attr("data-organ-id");

	  		$.post(base_url+"index.php/meeting/duplicate_meeting_entry/"+meeting_id+"/"+organ_id, form_data, function(response){
	  			//console.log(response);
          window.location.href = base_url+"index.php/meeting/workspace/"+response+"/"+organ_id+"?followup=yes";
	  		});

	  	});

	  	$('.btn-send-agenda').bind('click', function(){
	  		var form_data = $('#form_email_agenda').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

	  		$.post(base_url+"index.php/meeting/email_agenda/", form_data, function(response){
	  			$.alert({
				    title: 'Email Sent',
				    content: 'Successfully emailed the agenda!',
				    confirmButtonClass: 'btn-success',
				});
	  			location.reload();
	  		});

	  	});

	  	$('.btn-save-template').bind('click', function(){
	  		var form_data = $('#save_template_form').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

	  		$.post(base_url+"index.php/meeting/save_meeting_template/", form_data, function(response){
	  			$.alert({
				    title: 'Success',
				    content: 'Template successfully saved!',
				    confirmButtonClass: 'btn-success',
				});
	  			location.reload();
	  		});
	  	});

	  	$('.btn-load-template').bind('click', function(){
	  		var form_data = $('#load_meeting_template').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

	  		$.post(base_url+"index.php/meeting/load_save_template/", form_data, function(response){
	  			//console.log(response);
	  			location.reload();
	  		});
	  	});


	  	$('.delete-saved-template').bind('click', function(){
	  		var _this = $(this);
	    	var template_id = $(this).attr('data-template-id');

	    	$.confirm({
	    		title: 'Confirmation',
	    		content: 'This will be permanently deleted. Proceed?',
	    		confirmButtonClass: 'btn-success',
	    		cancelButtonClass: 'btn-danger',
	    		onAction: function(action){
			        if(action === "confirm")
			        {
			        	$.ajax({
				    		type:"POST",
				    		url:base_url+"index.php/meeting/delete_template",
				    		data: {template_id : template_id, csrf_gd: Cookies.get('csrf_gd')},
				    		cache:false,
				    		success:function(response)
				    		{
				    			var obj = $.parseJSON(response);

				    			if(obj['error'] == 0)
				    			{
				    				//jAlert(obj['message'], "Success");
				    				_this.parent().parent().fadeOut(300);
				    			}
				    			else
				    			{
				    				$.alert({
									    title: 'Error!',
									    content: obj['message'],
									    confirmButtonClass: 'btn-danger',
									});
				    			}
				    		},
				    		error:function(error)
				    		{
				    			console.log(error);
				    		},
				    	});
			        }

			    }
			});
	  	});

	  	$('.btn-edit-topic-ntd').bind('click', function(){
        var id = $(this).attr('data-edit-topic-ntd');
        var _this = $(this);

        $.ajax({
          type:"POST",
          url:base_url+"index.php/meeting/query_topic_ntd/"+id,
          cache:false,
          success:function(response)
          {
            $(".topic-ntd-textarea-field"+id).val(response);
          }
        });

        $('.topics-ntd-cont'+id).animate({
          height: "toggle",
            'padding-top': 'toggle',
            'padding-bottom': 'toggle',
            opacity: "toggle"
        }, "moderate");

      });

      $('.close-edit-topic-ntd').bind('click', function(){
        var id = $(this).attr('data-id');
        var _this = $(this);

        $('.topics-ntd-cont'+id).animate({
          height: "toggle",
            'padding-top': 'toggle',
            'padding-bottom': 'toggle',
            opacity: "toggle"
        }, "moderate");
      });


      $('.btn-edit-subtopic-ntd').bind('click', function(){
        var id = $(this).attr('data-edit-subtopic-ntd');
        var _this = $(this);

        $.ajax({
          type:"POST",
          url:base_url+"index.php/meeting/query_subtopic_ntd/"+id,
          cache:false,
          success:function(response)
          {
            $(".subtopic-ntd-textarea-field"+id).val(response);
          }
        });

        $('.subtopics-ntd-cont'+id).animate({
          height: "toggle",
            'padding-top': 'toggle',
            'padding-bottom': 'toggle',
            opacity: "toggle"
        }, "moderate");

      });

      $('.close-edit-subtopic-ntd').bind('click', function(){
        var id = $(this).attr('data-id');
        var _this = $(this);

        $('.subtopics-ntd-cont'+id).animate({
          height: "toggle",
            'padding-top': 'toggle',
            'padding-bottom': 'toggle',
            opacity: "toggle"
        }, "moderate");
      });

      $('.btn-save-updated-topic-ntd').bind('click', function(){
          var form_data = $('#update-topic-ntd-information').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');
          var id = $(this).attr('data-id');
          var new_text = $(".topic-ntd-textarea-field"+id).val();

          $.ajax({
            type:"POST",
            url:base_url+"index.php/meeting/update_topic_ntd_information",
            data: {id : id, ntd_text: new_text ,  csrf_gd: Cookies.get('csrf_gd')},
            cache:false,
            success:function(response)
            {
                var obj = $.parseJSON(response);
                
                if(obj['error'] == 0)
                {
                    location.reload();
                }
                else
                {
                  $.alert({
                       title: 'Error!',
                       content: obj['message'],
                       confirmButtonClass: 'btn-danger',
                   });
                }
            }

          });
      });

        $('.btn-save-updated-subtopic-ntd').bind('click', function(){
        var form_data = $('#update-subtopic-ntd-information').serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');
        var id = $(this).attr('data-id');
        var new_text = $(".subtopic-ntd-textarea-field"+id).val();

        $.ajax({
            type:"POST",
            url:base_url+"index.php/meeting/update_subtopic_ntd_information",
            data: {id : id, ntd_text: new_text ,  csrf_gd: Cookies.get('csrf_gd')},
            cache:false,
            success:function(response)
            {
                var obj = $.parseJSON(response);
                
                if(obj['error'] == 0)
                {
                    location.reload();
                }
                else
                {
                  $.alert({
                       title: 'Error!',
                       content: obj['message'],
                       confirmButtonClass: 'btn-danger',
                   });
                }
            }
          });
      });

	  	$('.close_ajax_modal').bind('click', function(){
	  		location.reload();
	  	});


		$('a.logout').confirm({
			icon: 'fa fa-warning',
			title: 'Confirm logout!',
	    	content: 'Are you sure you want to logout?',
	    	confirmButtonClass: 'btn-success',
	    	cancelButtonClass: 'btn-danger'
		});

});
