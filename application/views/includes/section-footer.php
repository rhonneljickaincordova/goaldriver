	<!-- NEW CHAPTER -->
	<div class="modal fade" id="new-chapter">
		<form id="add-chapter-form">
		<div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    <h4 class="modal-title">Add chapter</h4>
			  </div>
			  <div class="modal-body">
			    <div class="form-group row">
					<div class="col-sm-12 form-group">
						<label for="first-name" class="control-label">Chapter Name</label>
						<input type="text" class="form-control" name="title">
					</div>
				</div>
			  </div>
			  <div class="modal-footer">
			    <button type="button" class="btn btn-primary" id="save-chapter">Add Chapter</button>
			    <input type="hidden" name="plan_id" value="<?php echo $this->session->userdata('plan_id'); ?>">
			  </div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</form>
	</div><!-- /.modal -->


	<!-- EDIT CHAPTER -->
	<div class="modal fade" id="edit-chapter">
		<form id="edit-chapter-form">
		<div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    <h4 class="modal-title">Edit Chapter</h4>
			  </div>
			  <div class="modal-body">
			    <div class="form-group row">
					<div class="col-sm-12 form-group">
						<label for="first-name" class="control-label">Chapter Name</label>
						<input type="text" class="form-control" name="name" id="chapter-name">
					</div>
				</div>
			  </div>
			  <div class="modal-footer">
			    <button type="button" class="btn btn-primary" id="save-chapter-name">Save changes</button>
			    <input type="hidden" name="chapter_id" value="">
			    <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
			    <input type="hidden" name="plan_id" value="<?php echo $this->session->userdata('plan_id'); ?>">
			  </div>
			</div><!-- /.modal-content -->
			</form>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- NEW SECTION -->
	<div class="modal fade" id="new-section">
		<form id="add-section-form">
		<div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    <h4 class="modal-title">Add section</h4>
			  </div>
			  <div class="modal-body">
			    <div class="form-group row">
					<div class="col-sm-12 form-group">
						<label for="first-name" class="control-label">Section Name</label>
						<input type="text" class="form-control" name="title">
					</div>
				</div>
			  </div>
			  <div class="modal-footer">
			    <button type="button" class="btn btn-primary" id="add-section">Add Section</button>
			    <input type="hidden" name="plan_id" value="<?php echo $this->session->userdata('plan_id'); ?>">
			    <input type="hidden" name="chapter_id" value="<?php echo $chapter_id; ?>">
			  </div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</form>
	</div><!-- /.modal -->

	<!-- EDIT SECTION -->
	<div class="modal fade" id="edit-section">
		<form id="edit-section-form">
		<div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    <h4 class="modal-title">Edit section</h4>
			  </div>
			  <div class="modal-body">
			    <div class="form-group row">
					<div class="col-sm-12 form-group">
						<label for="first-name" class="control-label">Section Name</label>
						<input type="text" class="form-control" name="title" id="section-title">
					</div>
				</div>
			  </div>
			  <div class="modal-footer">
			    <button type="button" class="btn btn-primary" id="save-section">Save changes</button>
			    <input type="hidden" name="section_id" value="">
			    <input type="hidden" name="chapter_id" value="<?php echo $chapter_id; ?>">
			  </div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</form>
	</div><!-- /.modal -->

	<script type="text/javascript">

	$(function(){

		$(".edit-chapter").each(function(){
			$(this).click(function(){
				var chapter_id = $(this).attr('chapter');
				var chapter_title = $(this).attr('chapter_title');
				$("input[name='chapter_id']").val(chapter_id);
				$("input#chapter-name").val(chapter_title);
			});
		});

		
		$(".edit-section").each(function(){
			$(this).click(function(){
				var section_id = $(this).attr('section');
				var section_title = $(this).attr('section_title');
				$("input[name=section_id]").val(section_id);
				$("input#section-title").val(section_title);
			})
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
						url: "<?php echo site_url('plan/delete_plan_chapter'); ?>",
						data: {chapter_id: chapter_id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>"},
					})
					.done(function( msg ) {
						var res = $.parseJSON(msg);

						if(res.action == 'success'){
							$.alert({
							    title: 'Deleted',
							    content: chapter_title + ' chapter has been deleted',
							    confirm: function(){
							        location.href = "<?php echo site_url('plan'); ?>";
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
				url: "<?php echo site_url('plan/edit_plan_chapter'); ?>",
				data: $("#edit-chapter-form").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>",
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
				url: "<?php echo site_url('plan/add_plan_chapter'); ?>",
				data: $("form#add-chapter-form").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>",
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
			var data = $("form#add-section-form").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

			$.ajax({
				method: "POST",
				url: "<?php echo site_url('plan/add_section'); ?>",
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

		$(".edit-section").each(function(){
			$(this).click(function(){
				var section_title = $(this).attr('section_title');
				var section_id = $(this).attr('section');

				console.log(section_title);
				$("#edit-section-form #section-title").val(section_title);
				$("input[name='section_id']").val(section_id);

			})
		});

		// Update Section
		$("#save-section").click(function(){
			var data = $("form#edit-section-form").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
			
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('plan/update_section'); ?>",
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
						url: "<?php echo site_url('plan/delete_section'); ?>",
						data: {section_id: section_id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>"},
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


sort_section();
sort_chapter();
sort_subsection();
sort_section_subsection();
click_sort();

}); // end function ready
	
// Sort Section
function sort_section()
{
	// Sort Section position
	$("#sections").sortable({
	    axis: 'y',
	    handle: '.move-position',
	    update: function (event, ui) {
	        var data = $("#sections").sortable('serialize')+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
	        
	        // POST to server using $.post or $.ajax
	        $.ajax({
	            data: data,
	            type: 'POST',
	            url: "<?php echo site_url('plan/sort_section'); ?>"
	        }).done(function(){
	        	$("#content-section").load(location.href+" #content-section", function(){
	        		$.getScript(base_url+'public/script.js');
					sort_section_subsection();
					sort_subsection();
					sort_chapter();
					sort_section();
					click_sort();
					tinymce_init();
				});
				$("#sidebar").load(window.location.href+' #plan', function(){
					sort_section_subsection();
					sort_subsection();
					sort_chapter();
					sort_section();
					click_sort();
					tinymce_init();
				});


				
			});
	    }
	});
}
	
// Sort Chapter
function sort_chapter()
{
	// Sort chapter / section -----------------------------------------------------------------------
	$("#plan").sortable({
	    axis: 'y',
	    handle: '.sort-chapter',
	    update: function (event, ui) {
	        var data = $("#plan").sortable('serialize')+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

	        // POST to server using $.post or $.ajax
	        $.ajax({
	            data: data,
	            type: 'POST',
	            url: "<?php echo site_url('plan/sort_chapter'); ?>"
	        }).done(function(){
	        	$("#content-section").load(location.href+" #content-section", function(){
					sort_section_subsection();
					sort_subsection();
					sort_chapter();
					sort_section();
					click_sort();
					tinymce_init();
				});
				$("#sidebar").load(window.location.href+' #plan', function(){
					sort_section_subsection();
					sort_subsection();
					sort_chapter();
					sort_section();
					click_sort();
					tinymce_init();
				});
			});
	    }
	});
}

// Sort Subsection
function sort_subsection()
{
	$('#sub-sections-lists').sortable({
  		axis:'y',
  		handle: '.move-position',
  		update: function (event, ui)
  		{
	        var data = $(this).sortable('serialize')+"&csrf_gd="+Cookies.get('csrf_gd');
	        //POST to server using $.post or $.ajax
	        $.ajax({
	            data: data,
	            type: 'POST',
	            url: base_url+"index.php/plan/sort_subsection",
	        }).done(function(){
	        	$("#content-section").load(location.href+" #content-section", function(){
					sort_section_subsection();
					sort_subsection();
					sort_chapter();
					sort_section();
					click_sort();
					tinymce_init();
				});
				$("#sidebar").load(window.location.href+' #plan', function(){
					sort_section_subsection();
					sort_subsection();
					sort_chapter();
					sort_section();
					click_sort();
					tinymce_init();
				});
	        });
	    },
  	});
}
	
// Section & Subsection sort (Sidebar)
function sort_section_subsection()
{
	// Sort section / sub-section -----------------------------------------------------------------------
	$("#plan li.list-group-item").each(function(){
		var _this = $(this);

		_this.find('ul#section').sortable({
		    axis: 'y',
		    handle: '.sort-section',
		    update: function (event, ui) {
		        var data = _this.find('ul#section').sortable('serialize')+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
		        
		        // POST to server using $.post or $.ajax
		        $.ajax({
		            data: data,
		            type: 'POST',
		            url: "<?php echo site_url('plan/sort_section'); ?>"
		        }).done(function() {
		        	<?php if(! $section_id): ?>
						$.get("<?php echo site_url('plan/load_chapter/'.$chapter_id.'/'.$plan_id.'/'.$section_id); ?>", function( data ) {
					  		$("#plan-content-section").html(data);
					  		sort_section();
							sort_chapter();
							sort_subsection();
							sort_section_subsection();
							click_sort();
							tinymce_init();
						});
					<?php else: ?>
						$.get("<?php echo site_url('plan/load_section/'.$chapter_id.'/'.$plan_id.'/'.$section_id); ?>", function(data) {
					 		$( "#plan-content-section" ).html( data );
					 		sort_section();
							sort_chapter();
							sort_subsection();
							sort_section_subsection();
							click_sort();
							tinymce_init();
					 	});
					<?php endif; ?>
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
			        var data = _this2.find('ul.subsection').sortable('serialize')+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
			        
			        $.ajax({
			            data: data,
			            type: 'POST',
			            url: "<?php echo site_url('plan/sort_subsection'); ?>"
			        }).done(function() {
			        	<?php if(! $section_id): ?>
						$.get( "<?php echo site_url('plan/load_chapter/'.$chapter_id.'/'.$plan_id.'/'.$section_id); ?>", function( data ) {
							$( "#plan-content-section" ).html( data );
							sort_section();
							sort_chapter();
							sort_subsection();
							sort_section_subsection();
							click_sort();
							tinymce_init();
						});
					<?php else: ?>
						$.get( "<?php echo site_url('plan/load_section/'.$chapter_id.'/'.$plan_id.'/'.$section_id); ?>", function( data ) {
							$( "#plan-content-section" ).html( data );
							sort_section();
							sort_chapter();
							sort_subsection();
							sort_section_subsection();
							click_sort();
							tinymce_init();
						});
					<?php endif; ?>
					});
			    }
			});
		})
	}) 
}

// Click sort up/down
function click_sort()
{
	$(".plan-section").each(function(i){
			var _this = $(this);

		    $("#subsection-move-up-"+i).click(function(){
		      var subsec_id = $(this).attr('data-id');
		      var subsec_position = $(this).attr('data-position');
		      var section_id = $(this).attr('data-section-id');

		      $.ajax({
		        method: "POST",
		        url: base_url+"index.php/plan/sub_section_reorder/up",
		        data: {subsection_id: subsec_id, section_id:section_id, position: subsec_position, csrf_gd: Cookies.get('csrf_gd')}
		      }).done(function( msg ) {
		        var data = JSON.parse(msg);

		        if(data.status == 'success'){
			        $("#content-section").load(location.href+" #content-section", function(){
			        	sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});

					$("#sidebar").load(window.location.href+' #plan', function(){
						sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});
				}
		      });
		    });


		    $("#subsection-move-down-"+i).click(function(){
		      var subsec_id = $(this).attr('data-id');
		      var subsec_position = $(this).attr('data-position');
		      var section_id = $(this).attr('data-section-id');

		      $.ajax({
		        method: "POST",
		        url:  base_url+"index.php/plan/sub_section_reorder/down",
		        data: {subsection_id: subsec_id, section_id:section_id, position: subsec_position, csrf_gd: Cookies.get('csrf_gd')}
		      }).done(function( msg ) {
		        var data = JSON.parse(msg);

		        if(data.status == 'success'){
					$("#content-section").load(location.href+" #content-section", function(){
						sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});
					$("#sidebar").load(window.location.href+' #plan', function(){
						sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});
				}
		    });
		  })


		    // SECTION //
		    $("#section-move-up-"+i).click(function(){
		      var sec_position = $(this).attr('data-position');
		      var section_id = $(this).attr('data-section-id');

		      $.ajax({
		        method: "POST",
		        url: base_url+"index.php/plan/section_reorder/up",
		        data: {section_id:section_id, position: sec_position, csrf_gd: Cookies.get('csrf_gd')}
		      }).done(function( msg ) {
		        var data = JSON.parse(msg);

		        if(data.status == 'success'){
			        $("#content-section").load(location.href+" #content-section", function(){
			        	sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});

					$("#sidebar").load(window.location.href+' #plan', function(){
						sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});
				}
		      });
		    });


		    $("#section-move-down-"+i).click(function(){
		      var sec_position = $(this).attr('data-position');
		      var section_id = $(this).attr('data-section-id');

		      $.ajax({
		        method: "POST",
		        url:  base_url+"index.php/plan/section_reorder/down",
		        data: {section_id:section_id, position: sec_position, csrf_gd: Cookies.get('csrf_gd')}
		      }).done(function( msg ) {
		        var data = JSON.parse(msg);

		        if(data.status == 'success'){
					$("#content-section").load(location.href+" #content-section", function(){
						sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});
					$("#sidebar").load(window.location.href+' #plan', function(){
						sort_section_subsection();
						sort_subsection();
						sort_chapter();
						sort_section();
						click_sort();
						tinymce_init();
					});
				}
		    });
		})
	})
}

function tinymce_init()
{
	$.getScript(base_url+'public/tinymce/tinymce.min.js', function(){
		if (typeof(tinyMCE) != "undefined") {
			tinymce.init({
			  selector: '.editable',
			  inline: true,
			  toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
			  menubar: false,
			});
		}
	});

	 $.getScript(base_url+'public/sub_section_position.js');
}
</script>