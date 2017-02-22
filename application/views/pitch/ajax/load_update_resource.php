<?php echo form_open_multipart('pitch/update_resource'); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">Tell us about this resource</h4>
</div>
<div class="modal-body">
	<div class="form-group">
		<label class="form-label">What do you want to call this resource</label>
		<input type="text" name="resource_name" class="form-control" value="<?php echo $resource->name; ?>">
	</div>
	<div class="form-group">
		<label class="form-label">Description (Optional)</label>
		<textarea class="form-control" name="resource_description"><?php echo $resource->description; ?></textarea>
	</div>
	<div class="form-group">
		<label class="form-label">Upload an image</label>
		<input type="file" class="form-control" name="resource_logo" onchange="readURL(this)">
	</div>
	<div id="upload-preview-update">
		<?php if($resource->logo != ''): ?>
		<img src="<?php echo base_url('uploads/'.$resource->logo); ?>" width="100">
		<?php endif; ?>
	</div>
</div>
<div class="modal-footer">
	<input type="hidden" name="id" value="<?php echo $resource->id; ?>">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<input type="submit" class="btn btn-primary" name="submit" value="Save changes" id="update-resource-data" />
</div>
<?php echo form_close(); ?>
		
<script type="text/javascript">

$(function(){
		$("#update-resource-data").click(function(e){
			if($.trim($("#update-partner input[name='resource_name']").val()) == '')
			{
				alert('Resource name is required.');
				$("#update-partner input[name='resource_name']").focus();
				return false;
			}
		})
})

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
		var filename = input.files[0].name;

        var extension = filename.substr( (filename.lastIndexOf('.') +1) );

        if(extension == 'jpg' || extension == 'jpeg' || extension == 'gif' || extension == 'png'){
           	reader.onload = function (e) {
           		$("#upload-preview-update img").attr('src', e.target.result);
    			//var img = $('<img>'); 
				// img.attr('src', e.target.result);
				// img.attr('width', '200');
				// img.appendTo('#upload-preview-update');
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

</script>