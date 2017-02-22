$(function(){


	$(".edit-chapter").click(function(){
		var chapter_id = $(this).attr('chapter');
		var chapter_title = $(this).attr('chapter_title');
		$("input[name='chapter_id']").val(chapter_id);
		$("input#chapter-name").val(chapter_title);
	});

	$(".edit-section").click(function(){
		var section_id = $(this).attr('section');
		var section_title = $(this).attr('section_title');
		$("input[name=section_id]").val(section_id);
		$("input#section-title").val(section_title);
	})

	// Delete Chapter
	$(".delete-chapter").click(function(){
		var chapter_id = $(this).attr('chapter');
		var chapter_title = $(this).attr('chapter_title');

		$.confirm({
		    title: 'Confirm delete',
		    content: 'Are you sure you want to delete "'+chapter_title+'" chapter?',
		    confirmButtonClass: 'btn-danger',
			cancelButtonClass: 'btn-primary',
			confirmButton: 'Delete',
			cancelButton: 'Cancel',
		    confirm: function(){
		        $.ajax({
					method: "POST",
					url: base_url+"index.php/plan/delete_plan_chapter",
					data: {chapter_id: chapter_id, csrf_gd: Cookies.get('csrf_gd')},
				})
				.done(function( msg ) {
					var res = $.parseJSON(msg);

					if(res.action == 'success'){
						$.alert({
						    title: 'Deleted',
						    content: chapter_title + ' chapter has been deleted',
						    confirm: function(){
						        location.href = base_url+"index.php/plan";
						    }
						});
					}
				});	
		    },
		    
		});

		
	});

	// Update Chapter Name
	$("#save-chapter-name").click(function(){
		$.ajax({
			method: "POST",
			url: base_url+"index.php/plan/edit_plan_chapter",
			data: $("#edit-chapter-form").serialize()+"&csrf_gd="+Cookies.get('csrf_gd'),
		})
		.done(function( msg ) {
			var res = $.parseJSON(msg);

			

			if(res.action == 'success'){
				$.alert({
				    title: 'Success',
				    content: 'Chapter name has been updated',
				    confirmButtonClass: 'btn-info',
					cancelButtonClass: 'btn-danger',
				    confirm: function(){
				        location.reload();
				    }
				});
			}
		});		
	})


	// New chapter name
	$("#save-chapter").click(function(){
		
		$.ajax({
			method: "POST",
			url: base_url+"index.php/plan/add_plan_chapter",
			data: $("form#add-chapter-form").serialize()+"&csrf_gd="+Cookies.get('csrf_gd'),
		})
		.done(function( msg ) {
			var res = $.parseJSON(msg);

			if(res.action == 'success'){
				$.alert({
				    title: 'Chapter added',
				    content: res.msg,
				    confirmButtonClass: 'btn-info',
					cancelButtonClass: 'btn-danger',
				    confirm: function(){
				        location.reload();
				    }
				});	
			}
			else{
				$.alert({
				    title: 'Error',
				    content: res.msg,
				});
			}
		});
	});

	// New Section
	$("#add-section").click(function(){
		var data = $("form#add-section-form").serialize()+"&csrf_gd="+Cookies.get('csrf_gd');

		$.ajax({
			method: "POST",
			url: base_url+"index.php/plan/add_section",
			data: data,
		})
		.done(function( msg ) {
			var res = $.parseJSON(msg);

			if(res.action == 'success'){
				$.alert({
				    title: 'Success',
				    content: 'Section has been added',
				    confirmButtonClass: 'btn-info',
					cancelButtonClass: 'btn-danger',
				    confirm: function(){
				        location.reload();
				    }
				});
			}
			else{
				$.alert({
				    title: 'Error',
				    content: 'Please enter section name',
				    confirmButtonClass: 'btn-info',
					cancelButtonClass: 'btn-danger',
				});
						
			}

			
		});
	});

	// Update Section
	$("#save-section").click(function(){
		var data = $("form#edit-section-form").serialize()+"&csrf_gd="+Cookies.get('csrf_gd');
		
		$.ajax({
			method: "POST",
			url: base_url+"index.php/plan/update_section",
			data: data,
		})
		.done(function( msg ) {
			var res = $.parseJSON(msg);

			if(res.action == 'success'){
				$.alert({
				    title: 'Success',
				    content: 'Section name has been updated',
				    confirmButtonClass: 'btn-info',
					cancelButtonClass: 'btn-danger',
				    confirm: function(){
				        location.reload();
				    }
				});
			}

		});
	});

	// Delete Section
	$(".delete-section").click(function(){
		var section_id = $(this).attr('section');
		var section_title = $(this).attr('section_title');


		$.confirm({
		    title: 'Confirm delete',
		    content: 'Are you sure you want to delete "'+section_title+'" section?',
		    confirmButtonClass: 'btn-danger',
			cancelButtonClass: 'btn-primary',
			confirmButton: 'Delete',
			cancelButton: 'Cancel',
		    confirm: function(){
		    	$.ajax({
					method: "POST",
					url: base_url+"index.php/plan/delete_section",
					data: {section_id: section_id, csrf_gd:Cookies.get('csrf_gd')},
				})
				.done(function( msg ) {
					var res = $.parseJSON(msg);

					if(res.action == 'success'){
						$.alert({
						    title: 'Deleted',
						    content: section_title + ' section has been deleted',
						    confirmButtonClass: 'btn-info',
							cancelButtonClass: 'btn-danger',
						    confirm: function(){
						        location.reload();
						    }
						});
						
					}
				});	
		    },
		});

		
	});

	// Sort Section position
	$("#sections").sortable({
	    axis: 'y',
	    update: function (event, ui) {
	        var data = $("#sections").sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');
	        
	        // POST to server using $.post or $.ajax
	        $.ajax({
	            data: data,
	            type: 'POST',
	            url: base_url+"index.php/plan/sort_section"
	        });
	    }
	});


	// Sortable section & subsection in sidebar
	$("#plan li.list-group-item").each(function(){
		var _this = $(this);

		_this.find('ul#section').sortable({
		    axis: 'y',
		    handle: '.sort-section',
		    update: function (event, ui) {
		        var data = _this.find('ul#section').sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');
		        
		        // POST to server using $.post or $.ajax
		        $.ajax({
		            data: data,
		            type: 'POST',
		            url: base_url+"index.php/plan/sort_section"
		        }).done(function() {
					$("#content-section").load(window.location.href+" #content-section");
					$.getScript(base_url+'public/script.js');
					$.getScript(base_url+'public/plan_sortable_sidebar.js');
				});
		    }
		});

		_this.find("#section .list-item").each(function(){
			var _this2 = $(this);
			var item_count = _this2.length;

			_this2.find('ul.subsection').sortable({
			    axis: 'y',
			    handle: '.sort-subsection',
			    update: function (event, ui) {
			        var data = _this2.find('ul.subsection').sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');
			        
			        // POST to server using $.post or $.ajax
			        $.ajax({
			            data: data,
			            type: 'POST',
			            url: base_url+"index.php/plan/sort_subsection"
			        }).done(function() {
						$("#content-section").load(window.location.href+" #content-section");
						$.getScript(base_url+'public/script.js');
						$.getScript(base_url+'public/plan_sortable_sidebar.js');
					});
			    }
			});

		})
	});

	// Sort chapter position
	$("#plan").sortable({
	    axis: 'y',
	    handle: '.sort-chapter',
	    update: function (event, ui) {
	        var data = $("#plan").sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');

	        // POST to server using $.post or $.ajax
	        $.ajax({
	            data: data,
	            type: 'POST',
	            url: base_url+"index.php/plan/sort_chapter"
	        });
	    }
	});
})





