<div class="bg-white-wrapper">
	<h4>Section Setup</h4>
	<div class="form-group">
		Section Name: <input type="text" name="title" class="form-control" value="<?php echo $info->title; ?>" />
	</div>

	<div class="panel panel-default">
	  <div class="panel-body">
	    Please verify that these are the items you want to include here. The contents of your plan are up to you. Feel free to add, remove, and rename items as you wish.
	  </div>
	</div>

	<div class="form-group">
		<p><strong>Currently included in this section:</strong></p>
	</div>

	<div class="in-this-section" id="section-items">
		<?php 
	// 	TODO: query subsection here
		?> 
		<ul>
			<?php 
			if(count($items)): ?>
				<?php foreach($items as $item): ?>
				<li>
					<?php if($item->type == 'text'): ?>
					<i class="fa fa-file-text-o"></i>&nbsp;
					<?php else: ?>
					<i class="fa fa-pie-chart"></i>&nbsp;
					<?php endif; ?>
					<input type="text" name="name" class="custom_textfield" value="<?php echo $item->title; ?>" id="<?php echo $item->subsection_id; ?>"/> <a href="#" onclick="return delete_item(<?php echo $item->subsection_id; ?>)">Delete</a>
				</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		
	</div>

	<div class="add-items">
		<a href="#" data-toggle="modal" id="add-item" data-target="#add-custom-text-item" class="btn btn-primary btn-sm">Add item</a>
		<!-- <a href="#" class="btn btn-primary btn-sm" id="add-chart">Add chart</a> -->
		<a href="javascript:;" onclick="location.reload();">Go back</a>
	</div>
</div>

<div class="modal fade" id="add-custom-text-item">
	<form id="form-custom-item">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Add item</h4>
	      </div>
	      <div class="modal-body">
	        <p>What would you like to name your item?</p>
	        <input type="text" name="item_name" id="item_name" class="form-control" />
	      </div>
	      <input type="hidden" name="section_id" value="<?php echo $info->section_id; ?>" />
	      <input type="hidden" name="type" value="text" />
	      <input type="hidden" name="plan_id" value="<?php echo $info->plan_id; ?>" />
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" id="add-custom-item" class="btn btn-primary">Save changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->

<script type="text/javascript">
	
	$(function(){

		// add focus event
		$('#add-custom-text-item').on('shown.bs.modal', function () {
		    $('#item_name').focus();
		});

		// 
		$("form#form-custom-item").keypress(function(e) {
			if(e.which == 13) {
				if($.trim($("#item_name").val()) != '')
				{
					var new_custom_item = $("form#form-custom-item").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

					$.ajax({
					  method: "POST",
					  url: "<?php echo site_url('plan/add_section_item'); ?>",
					  data: new_custom_item
					}).done(function( msg ) {
						var data = JSON.parse(msg);
						$('.modal').hide();
						$('.modal-backdrop.in').hide();
						
						console.log(data);
						
						if(data.status == 'success'){
							$.ajax({
							  method: "POST",
							  url: "<?php echo site_url('plan/update_sections'); ?>",
							  data: { section_id: <?php echo $info->section_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
							}).done(function( msg ) {
							    $(".content-section").html(msg);
							  });
						}
						
					});
				}
				
				return false;
		    }
		});

		// Add custom item
		$("#add-custom-item").click(function(){

			if($.trim($("#item_name").val()) != '')
			{
				var new_custom_item = $("form#form-custom-item").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

				$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/add_section_item'); ?>",
				  data: new_custom_item
				}).done(function( msg ) {
					var data = JSON.parse(msg);
					$('.modal').hide();
					$('.modal-backdrop.in').hide();
					
					console.log(data);
					
					if(data.status == 'success'){
						$.ajax({
						  method: "POST",
						  url: "<?php echo site_url('plan/update_sections'); ?>",
						  data: { section_id: <?php echo $info->section_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
						}).done(function( msg ) {
						    $(".content-section").html(msg);
						  });
					}
				});
			}
		});
		
		// Add Chart
		$("#add-chart").click(function(){
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/add_section_item/chart'); ?>",
			  data: { section_id:<?php echo $info->section_id; ?>, plan_id:<?php echo $info->plan_id; ?>, type: 'chart', <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);
				
				if(data.status == 'success'){
					$.ajax({
					  method: "POST",
					  url: "<?php echo site_url('plan/update_sections'); ?>",
					  data: { section_id: <?php echo $info->section_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
					}).done(function( msg ) {
					    $(".content-section").html(msg);
					  });
				}
				
			});
		});
		
		// Update custom text item
		$(".custom_textfield").each(function(){
			var _this = $(this);

			$(this).on('input', function(){
				var subsec_id = _this.attr('id');
				var subsec_val = _this.val();
				
				$("a#add-item").text("Loading...");
				$("a#add-item").unbind("click");

				$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/update_subsections'); ?>",
				  data: { subsection_id: subsec_id, title: subsec_val, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
				    var data = JSON.parse(msg);
				    
				    if(data.status == 'success'){
						$.ajax({
						  method: "POST",
						  url: "<?php echo site_url('plan/update_sections'); ?>",
						  data: { section_id: <?php echo $info->section_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
						}).done(function( msg ) {
							$(".content-section").html(msg);
							$("a#add-item").text("Add item");
							$("a#add-item").bind("click");
						});
					}
				});
			})
		})
	});
	
	function delete_item(item_id){
		$.confirm({
			title: 'Confirm delete',
			content: 'Are you sure you want to delete this item?',
			confirmButtonClass: 'btn-danger',
			cancelButtonClass: 'btn-primary',
			confirmButton: 'Delete',
			cancelButton: 'Cancel',
			confirm: function(){
				$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/delete_sections_item'); ?>",
				  data: { subsection_id: item_id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
					var data = JSON.parse(msg);
				   	if(data.status == 'success'){
						$.ajax({
						  method: "POST",
						  url: "<?php echo site_url('plan/update_sections'); ?>",
						  data: { section_id: <?php echo $info->section_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
						}).done(function( msg ) {
						    $(".content-section").html(msg);
						  });
					} 
				});
			}
		});


	}
</script>