$(function(){

		$('.modal').on('hidden.bs.modal', function(){
			if($(this).find('form').length > 0){
				$(this).find('form')[0].reset();	
			}
		});

		

        $(".comment-box").each(function(index, value){
			var _this = $(this);
			
			_this.find("#submit-comment").click(function(e){
				e.preventDefault();
				var subsection_id = $(this).attr('subsectionid');
				var comment = $.trim(_this.find("#comment-field").val());
				
				if(comment.length > 1){
					$.ajax({
					  method: "POST",
					  url: base_url+"index.php/plan/submit_subsection_comment",
					  data: { subsection_id: subsection_id, comment: comment, csrf_gd: Cookies.get('csrf_gd') }
					}).done(function( msg ) {
						var data = JSON.parse(msg);
						if(data.status == 'success'){
							_this.find( "#comment" + index ).load( location.href+" #comment" +index );
							_this.find("#comment-field").val('').focus();
							
							var comment_count = parseInt($("#comment-box"+index).find("#total-comment").text());
							$("#comment-box"+index).find("#total-comment").text(comment_count+1);	
						}
					});
			  
				}
				else{
					_this.find("#comment-field").val('').focus();
				}
			})	
		});
		
		
		
		// toggle comment box
		$(".toggle-comment-box").each(function(){
			var _this = $(this);
			$(this).click(function(e){
				e.preventDefault();
				_this.next(".comment-box .comment-area").toggle();	
			})
		})
		
		
		// change whats on this section
		$("#change-section").click(function(e){
			e.preventDefault();
			$.ajax({
			  method: "POST",
			  url: base_url+"index.php/plan/update_sections",
			  data: { section_id: g_section_id, csrf_gd: Cookies.get('csrf_gd') }
			}).done(function( msg ) {
			    $("#content-section").html(msg);
			  });
		});
		
		// editable section
		$(".editable-section").each(function(){
			var _this = $(this);
			
			$(this).focusout(function(){
				var id = _this.attr("id");
				var field = _this.attr("field");
				var content = _this.html();
			
				$.ajax({
				  method: "POST",
				  url: base_url+"index.php/plan/update_section_field",
				  data: { section_id: id, field: field, content: content, csrf_gd: Cookies.get('csrf_gd') }
				}).done(function( msg ) {
					
				});
			})
		})
		
		// editable sub-section
		$(".editable-subsection").each(function(){
			var _this = $(this);
			
			$(this).focusout(function(){
				var id = _this.attr("subid");
				var field = _this.attr("field");
				var content = _this.html();
			
				$.ajax({
				  method: "POST",
				  url: base_url+"index.php/plan/update_subsection_data",
				  data: { subsection_id: id, field: field, content: content, csrf_gd: Cookies.get('csrf_gd') }
				}).done(function( msg ) {
					
				});
			})
		});
		
		// edit section form
		$("#submit-section-title-edit").click(function(){
			var new_title = $("#section_edit #section-title").val();
			var field = $("#section_edit #field").val();
			var id = $("#section_edit #section_id").val(); 
			
			$.ajax({
			  method: "POST",
			  url: base_url+"index.php/plan/update_section_field",
			  data: { section_id: id, field: field, content: new_title, csrf_gd: Cookies.get('csrf_gd') }
			}).done(function( msg ) {
				$(".preview-content h3 span").text(new_title);
				$('#section_edit').modal('hide');
			});
		});
		
		// edit sub section form
		$("#submit-subsection-title-edit").click(function(){
			var new_title = $("#subsection_edit #subsection-title").val();
			var field = $("#subsection_edit #field").val();
			var id = $("#subsection_edit #subsection_id").val(); 
			
			$.ajax({
			  method: "POST",
			  url: base_url+"index.php/plan/update_subsection_data",
			  data: { subsection_id: id, field: field, content: new_title, csrf_gd: Cookies.get('csrf_gd') }
			}).done(function( msg ) {
				$("#section-title-"+id).find("span.subsection-title").text(new_title);
				$("a#"+id).attr("title", new_title);
				$('#subsection_edit').modal('hide');
			});
		});

		// display example and instruction navbar
		$(".editable").each(function(){
			var _this = $(this);
			$(this).click(function(){
				//_this.prev(".example-instruction").find('.example-instruction-wrapper').show();
				_this.prev(".example-instruction").find('i.toggle-instruction').toggleClass("up down");	
			})
		});		

		// toggle instruction-example div show/hide
		$(".toggle-in-ex").each(function(){
			var _this = $(this);
			$(this).click(function(e){
				e.preventDefault();
				$(this).toggleClass("up down");
				_this.next('div').toggle();
				// if(_this.text() === "Close"){
				// 	_this.next('div').hide();
				// 	_this.html('<i class="fa fa-chevron-up"></i>');
				// }
				// else{
				// 	_this.next('div').show();
				// 	_this.html('<i class="fa fa-chevron-down"></i>');
				// }
			})
		});

		$(".example-instruction").each(function(){
			
			$(this).find('.instruction-edit').click(function(){
				var sec_id 	= $(this).attr('id');
				var table 	= $(this).attr('table');
				var uid 	= $(this).attr('uid');
				var instruct = $("#"+uid).find('#instructions-content').html();
				//$("#instructions textarea#instructions_field").val(instruct);
				$(tinymce.get('instructions_field').getBody()).html(instruct);

				$("#instructions").modal('show');

				//console.log(instruct);

				$("input[name='sec_id']").val(sec_id);
				$("input[name='table']").val(table);
				$("input[name='uid']").val(uid);
				
			});

			$(this).find('.example-edit').click(function(){
				var sec_id = $(this).attr('id');
				var table = $(this).attr('table');
				var uid = $(this).attr('uid');
				var examp = $("#"+uid).find('#example-content').html();
				//$("#example textarea#example_field").val(examp);
				$(tinymce.get('example_field').getBody()).html(examp);

				$("#example").modal('show');

				//console.log(examp);

				$("input[name='sec_id']").val(sec_id);
				$("input[name='uid']").val(uid);
				$("input[name='table']").val(table);
				
			})
		});

		// submit instruction content
		$("#submit-instruction-content").click(function(){
			var content = $('#instructions-data').serialize()+"&csrf_gd="+Cookies.get('csrf_gd');
			
			$.ajax({
			  method: "POST",
			  url: base_url+"index.php/plan/submit_instruction",
			  data: content
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.status == 'success'){
					$("#"+data.uid).find("#instructions-content").html(data.text);
					$("#instructions-data").trigger( "reset" );
					$("#instructions").modal('hide');

				}
			});
		})

		// submit example content
		$("#submit-example-content").click(function(){
			var content = $('#example-data').serialize()+"&csrf_gd="+Cookies.get('csrf_gd');
			
			$.ajax({
			  method: "POST",
			  url: base_url+"index.php/plan/submit_example",
			  data: content
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.status == 'success'){
					$("#"+data.uid).find("#example-content").html(data.text);
					$("#example-data").trigger( "reset" );
					$("#example").modal('hide');
				}
			});
		});

		// select a chart
		$(".no-chart-selected").each(function(){
			var _this = $(this);

			_this.click(function(){
				_this.hide();
				_this.next('.chart-types').show();
			});
		})

		$(document).on('hidden.bs.modal', function (e) {
		    $(e.target).removeData('bs.modal');
		});




		

		
	});
	
	// delete comment
	function delete_my_comment(_this, ctr){
		$.confirm({
			title: 'Confirm delete',
			content: 'Are you sure you want to delete your comment?',
			confirmButtonClass: 'btn-danger',
			cancelButtonClass: 'btn-primary',
			confirmButton: 'Delete',
			cancelButton: 'Cancel',
			confirm: function(){
				$.ajax({
				  method: "POST",
				  url: base_url+"index.php/plan/delete_my_comment",
				  data: { comment_id: _this.id, csrf_gd: Cookies.get('csrf_gd')  }
				}).done(function( msg ) {
					var data = JSON.parse(msg);
					if(data.status == 'success'){
						$.alert('Your comment has been deleted.', 'Deleted');

						_this.closest("li").remove();
						
						var comment_count = $("#comment-box"+ctr).find("#total-comment").text();
						$("#comment-box"+ctr).find("#total-comment").text(parseInt(comment_count-1));
					}
				});
			}
		});

	}
	
	// edit comment
	function edit_comment(comment_id){
		$("#edit_comment").modal('show');
	}
	
	
	// update section title
	function update_section_title(_this){
		$("#section_edit").modal('show');
		$("#section_edit #section-title").val(_this.title);
		$("#section_edit #section_id").val(_this.id);	
	}
	
	// update subsection info
	function update_subsection(_this, title){
		$('#subsection_edit').modal('show');
		console.log(title);
		$("#subsection_edit #subsection-title").val(title);
		$("#subsection_edit #subsection_id").val(_this.id);	
	}


	// tinymce.init({
	// 	menubar:false,
	//     selector: ".editor",
	//     setup: function (editor) {
 //        	editor.on('change', function () {
 //            	editor.save();
 //        	});
 //    	}
	// });

	// for inline editor
	tinymce.init({
		selector: '.editable, .editor',
		inline: true,
		toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
		menubar: false,
		setup: function (editor) {
			editor.on('change', function () {
		    	editor.save();
			});
		}
	});



	

