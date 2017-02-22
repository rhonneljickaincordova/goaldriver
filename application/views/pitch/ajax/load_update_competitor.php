
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Tell us about this competitor</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="form-label">Name or type of competitor:</label>
			<input type="text" name="competitor_name" value="<?php echo $competitor->name; ?>" class="form-control">
			<span id="helpBlock" class="help-block">Such as "John's Coffee Shop" or "Fitness-wellness stores"</span>
		</div>
		<div class="form-group">
			<label class="form-label">What are your advantages over this competitor?</label>
			<input type="text" name="competitor_advantage" value="<?php echo $competitor->advantage; ?>" class="form-control" maxlength="15">
			<span id="helpBlock" class="help-block">Such as "Brand recognition" or "Patented technology" or "Lower price"</span>
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo $competitor->id; ?>">
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="update-competitor-data-submit"><i class="fa fa-floppy-o"></i> Save changes</button>
	</div>
		
<script type="text/javascript">

$(function(){
	$("#update-competitor-data-submit").click(function(){
		var data = $("form#update-competitor-data").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/update_competitor'); ?>",
		  data: data
		}).done(function( msg ) {
			var data = JSON.parse(msg);

			if(data.action == 'success'){
				$("#update-competitor").modal('hide');
				$("#update-competitor-data").trigger( "reset" ); // reset modal textfields
				$('#competitors').load(location.href + ' #competitors-load');
			}

			
			
		});

	})
})

</script>