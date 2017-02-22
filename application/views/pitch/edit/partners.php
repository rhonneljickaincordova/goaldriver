<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$partners[0]->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<h3>Partners and resources</h3>
		<p>Do you have any key partners? These could be suppliers that are crucial to the viability of the business? What resources are vital to keep the business operating?</p>

		<br>
		<div id="partners-sources">
			<div id="partners-sources-load">
				<?php if(count($partners) && $partners[0]->name != NULL): ?>
				<table class="table">
					<thead>
						<th>Logo</th>
						<th>Name</th>
						<th>Description</th>
						<th>Action</th>
					</thead>
				<?php 
				foreach ($partners as $partner) {
					echo '<tr>';
					echo '<td><img src="'.base_url('uploads/'.$partner->logo).'" alt="'.$partner->name.'" width="150" /></td>';
					echo '<td>'.$partner->name.'</td>';
					echo '<td>'.$partner->description.'</td>';
					echo '<td><a href="'.site_url('pitch/load_update_resource/'.$partner->id).'" title="Edit resource" data-toggle="modal" data-target="#update-partner"><i class="fa fa-pencil"></i></a>&nbsp;
					<a href="javascript:;" onclick="return delete_partner('.$partner->id.')" title="Delete resource"><i class="fa fa-trash"></i></a></td>';
					echo '</tr>';
				}
				?>
				</table>
				<?php else: ?>
				<div class="alert alert-warning">
				No resources yet.
				</div>
				<?php endif; ?>
			</div>
		</div>

		<br />
		<a href="javascript:;" data-target="#add-partner-resource" data-toggle="modal"><i class="fa fa-plus" aria-hidden="true"></i> Add a resource</a>
	</div>
</div>

<div class="modal fade" id="add-partner-resource" tabindex="-1" role="dialog">
	
	<div class="modal-dialog">
		<?php echo form_open_multipart('pitch/edit/partners'); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Tell us about this resource</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label">What do you want to call this resource</label>
					<input type="text" name="resource_name" class="form-control">
				</div>
				<div class="form-group">
					<label class="form-label">Description (Optional)</label>
					<textarea class="form-control" name="resource_description"></textarea>
				</div>
				<div class="form-group">
					<label class="form-label">Upload an image</label>
					<input type="file" class="form-control" name="resource_logo" onchange="readURL(this)">
				</div>
				<div id="upload-preview"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" name="submit" value="Add resource" id="submit-resource-data" />
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
  
</div><!-- /.modal -->

<div class="modal fade" id="update-partner" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content"></div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

	$(function(){

		$("#submit-resource-data").click(function(e){
			if($.trim($("input[name='resource_name']").val()) == '')
			{
				alert('Resource name is required.');
				$("input[name='resource_name']").focus();
				return false;
			}
		})

		//clear modal cache, so that new content can be loaded
		$('body').on('hidden.bs.modal', '.modal', function () {
	        $(this).removeData('bs.modal');
		});


		// toggle hide/show of this section in the view
		$("#hide_show").change(function(){
			var id = $(this).val();
			if ($(this).is(':checked')) {
		        //alert('checked');

		        $.ajax({
				  method: "POST",
				  url: "<?php echo site_url('pitch/hide_view'); ?>",
				  data: {plan_id: id, table:'pitch_partners', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
					var data = JSON.parse(msg);

					if(data.action == 'success'){
						//alert('This section is hidden from the view');
					}

				});
		    }
		    else{
		    	$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('pitch/hide_view'); ?>",
				  data: {plan_id: id, table:'pitch_partners', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
					var data = JSON.parse(msg);

					if(data.action == 'success'){
						//alert('This section is visible from the view');
					}

				});
		    }
		});

	})

	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
			var filename = input.files[0].name;

            var extension = filename.substr( (filename.lastIndexOf('.') +1) );

            if(extension == 'jpg' || extension == 'jpeg' || extension == 'gif' || extension == 'png'){
	           	reader.onload = function (e) {
	           		var img = $('<img>'); 
					img.attr('src', e.target.result);
					img.attr('width', '200');
					img.appendTo('#upload-preview');
				};

	            reader.readAsDataURL(input.files[0]);
			}
            else{
            	//alert('Please select a valid image file.');
            	$("#ajax-msg").show().html('<div class="alert alert-danger"><i class="fa fa-info-circle"></i> Error, Please select a valid image file.</div>');

            	setTimeout(function(){
					$("#ajax-msg").hide();            		
            	}, 5000)
            }
		}
    }

    function delete_partner(id)
    {
    	var conf = confirm("Delete this resource?");

    	if(conf)
    	{
    		$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('pitch/delete_resource'); ?>",
			  data: {id: id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);
				
				if(data.action == 'success'){
					$("#ajax-msg").show().html(data.msg);
					$('#partners-sources').load(location.href + ' #partners-sources-load');
				}

				setTimeout(function(){
					$("#ajax-msg").hide();            		
	        	}, 5000)
			});
    	}
    }
</script>

<?php $this->load->view('includes/footer'); ?>