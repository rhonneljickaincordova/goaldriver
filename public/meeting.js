$(document).ready(function(){
	// $('.edit-subtopic-link').bind('click', function(){
 //    	var subtopic_id = $(this).attr('data-edit-subtopic-id');

 //    	$.confirm({
	// 	    content: "URL:"+base_url+"index.php/meeting/edit_subtopic_information/"+subtopic_id,
	// 	    title: "Edit Subtopic Title",
	// 	    confirmButton: false, // hides the confirm button.
	// 	    cancelButtonClass: "btn-danger close_ajax_modal",
	// 	    theme: 'material'
	// 	});
 //    });

   $('.edit-subtopic-link').bind('click', function(){
      var subtopic_id = $(this).attr('data-edit-subtopic-id');

      $.ajax({
        type:"POST",
        url:base_url+"index.php/meeting/query_subtopic_title/"+subtopic_id,
        cache:false,
        success:function(response)
        {
          $(".inline-edit-subtopic"+subtopic_id).val(response);
        }
      });

      $('.inline-edit-subtopic'+subtopic_id).animate({
        height: "toggle",
          'padding-top': 'toggle',
          'padding-bottom': 'toggle',
          opacity: "toggle"
      }, "fast");

    });

   $('.edit-subtopic-title').bind('blur', function(){
     var subtopic_id = $(this).attr('data-subtopic-id');
     var title = $(this).val();

     $.ajax({
      type:"POST",
      url:base_url+"index.php/meeting/inline_edit_subtopic_title",
      data: { subtopic_id:subtopic_id, title:title , csrf_gd: Cookies.get('csrf_gd') },
      success:function(response)
      {
        var obj = $.parseJSON(response);
        if(obj['error'] == 0)
        {
          $.alert({
              title: 'Success!',
              content: obj['message'],
              confirmButtonClass: 'btn-success',
          });
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

     });

   });

	$('.delete-subtopic-link').bind('click', function(){
		var _this = $(this);
    	var subtopic_id = $(this).attr('data-delete-subtopic-id');

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
			    		url:base_url+"index.php/meeting/delete_subtopic",
			    		data: {subtopic_id : subtopic_id, csrf_gd: Cookies.get('csrf_gd')},
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

    $('.subtopic-input-field').bind('keyup',function(){
  		var subtopic_id = $(this).attr('data-subtopic-input-field');

  		if($(this).val().length >= 1)
  		{
  			$('.subtopic-items-cont-input'+subtopic_id).hide();
  			$('.subtopic-items-cont'+subtopic_id).show();
  			$('.subtopic-textarea-field'+subtopic_id).val($(this).val());
  			$('.subtopic-textarea-field'+subtopic_id).focus();
  		}
  	});


    $('.btn-save-saveas-actions-subtopic').bind('click', function(){
  		var subtopic_id = $(this).attr('data-subtopic-id');
		var form_data = $('#add-subtopic-note-task-decision'+subtopic_id).serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

		$.post(base_url+"index.php/meeting/save_meeting_note_subtopic", form_data, function(response){
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

    
});