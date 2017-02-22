<?php 
$data = unserialize($segment_info->data);

?>

<form id="update-market-segment-data">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Tell us about this market segment</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="form-label">What do you want to call this segment?</label>
			<input type="text" name="name_segment" value="<?php echo $data['name_segment']; ?>" class="form-control">
		</div>
		<div class="form-group">
			<label class="form-label">How many prospects do you think are in this segment?</label>
			<input type="text" name="prospect_segment" value="<?php echo $data['prospect_segment']; ?>" class="form-control" maxlength="15">
		</div>
		<div class="form-group">
			<label class="form-label">How much does each prospect spend annually on the problem you solve?</label>
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon1">&pound;</span>
				<input type="text" name="annual_prospect" value="<?php echo $data['annual_prospect']; ?>" class="form-control" maxlength="9">
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="update-market-segment-btn">Save changes</button>
	</div>
	<input type="hidden" name="id" value="<?php echo $segment_info->id; ?>">
</form>

<script type="text/javascript">

$(function(){
	$("#update-market-segment-btn").click(function(){
		var data = $("form#update-market-segment-data").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
		
		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/update_market_segment'); ?>",
		  data: data
		}).done(function( msg ) {
			var data = JSON.parse(msg);
			
			if(data.action == 'success')
			{
				$("#update-market-segment").modal('hide');
				$("#update-market-segment-data").trigger( "reset" ); // reset modal textfields
				$('#market-segments').load(location.href + ' #market-segments-load');
			}
		});
	})

	
})

</script>