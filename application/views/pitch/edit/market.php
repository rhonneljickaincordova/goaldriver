<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$segments[0]->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<h3>Target market</h3>
		<p>What are the specific segments you are going to target? Who are your early adopters who will pay more? Who is you main market and are there smaller lucrative segments worth targeting?</p>

		<div id="market-segments">
			<div id="market-segments-load">
				<?php 
				if(count($segments)): ?>

				<?php 
				foreach ($segments as $segment) {
					if($segment->data != NULL):
					$target_market = unserialize($segment->data); ?>

					<div class="segment clearfix">
						<div style="float:left">
							<div><strong>Segment name:</strong> <?php echo $target_market['name_segment']; ?></div>
							<div><strong>No. of prospects:</strong> <?php echo $target_market['prospect_segment']; ?></div>
							<div><strong>How much does each prospect spend annually:</strong> &pound;<?php echo $target_market['annual_prospect']; ?></div>
						</div>
						<div class="pull-right">
							<a href="<?php echo site_url('pitch/load_update_market_segment/'.$segment->id); ?>" data-toggle="modal" data-target="#update-market-segment"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="bottom" title="Edit segment"></i></a>&nbsp;
							<a href="javascript:;" onclick="return delete_market_segment(<?php echo $segment->id; ?>);"><i class="fa fa-trash-o" data-toggle="tooltip" data-placement="bottom" title="Delete segment"></i></a>
						</div>
					</div>

				<?php  endif; } ?>

				<?php else: ?>
				<div class="alert alert-warning">No Target market segment yet.</div>
				<?php endif; ?>
			</div>
		</div>
		<br><br>
		<a href="#" data-target="#add-market-segment" data-toggle="modal"><i class="fa fa-plus" aria-hidden="true"></i> Add a market segment</a>
		<a href="<?php echo site_url('pitch/edit/competition'); ?>" class="btn btn-success btn-sm pull-right">Continue</a>
	</div>
</div>

<div class="modal fade" id="add-market-segment" tabindex="-1" role="dialog">
	<form id="market-segment-data">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Tell us about this market segment</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label">What do you want to call this segment?</label>
					<input type="text" name="name_segment" class="form-control">
				</div>
				<div class="form-group">
					<label class="form-label">How many prospects do you think are in this segment?</label>
					<input type="text" name="prospect_segment" class="form-control" maxlength="15">
				</div>
				<div class="form-group">
					<label class="form-label">How much does each prospect spend annually on the problem you solve?</label>
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon1">&pound;</span>
						<input type="text" name="annual_prospect" class="form-control" maxlength="9">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="submit-market-segment-data">Save changes</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->

<div class="modal fade" id="update-market-segment" tabindex="-1" role="dialog">
	<form id="market-segment-data">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"></div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->




<script type="text/javascript">

$(function(){
	$("#submit-market-segment-data").click(function(){
		var data = $("#market-segment-data").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/save_market_segment'); ?>",
		  data: data
		}).done(function( msg ) {
			var data = JSON.parse(msg);

			if(data.action == 'success'){
				$("#add-market-segment").modal('hide');
				$("#market-segment-data").trigger( "reset" ); // reset modal textfields
				$('#market-segments').load(location.href + ' #market-segments-load');
			}

		});
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
			  data: {plan_id: id, table:'pitch_target_market', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_target_market', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is visible from the view');
				}

			});
	    }
	});
})


function delete_market_segment(id)
{
	var del = confirm('Delete market segment?');

	if(del){
		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/delete_market_segment'); ?>",
		  data: {id: id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
		}).done(function( msg ) {
			var data = JSON.parse(msg);
			
			if(data.action == 'success'){	
				$('#market-segments').load(location.href + ' #market-segments-load');
			}
		});
	}
}

</script>

<?php $this->load->view('includes/footer'); ?>