<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$competitors[0]->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
<div id="pitch">
	<h3>Competition</h3>
	<p>Who are your direct competition? What alternatives do your customer have?</p>
	
	<div id="competitors">
		<div id="competitors-load">
			<?php if(count($competitors)): ?>
			<?php foreach ($competitors as $competitor) { 
				if($competitor->name != NULL):
				?>
				<div class="competitor">
					<strong><?php echo $competitor->name; ?>:</strong> <?php echo $competitor->advantage; ?>
					<div class="pull-right">
						<a href="<?php echo site_url('pitch/load_update_competitor/'.$competitor->id); ?>" data-toggle="modal" data-target="#update-competitor"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="bottom" title="Edit competitor"></i></a>&nbsp;
						<a href="#" onclick="delete_competition(<?php echo $competitor->id; ?>);" ><i class="fa fa-trash-o" data-toggle="tooltip" data-placement="bottom" title="Delete competitor"></i></a>
					</div>
				</div>
			<?php endif; } ?>
			<?php else: ?>
			<div class="alert alert-warning">No competitor added yet.</div>
			<?php endif; ?>
		</div>
	</div>

	<br><br>
	<a href="#" data-target="#add-competitor" data-toggle="modal"><i class="fa fa-plus"></i> Add a competitor</a>
	<a href="<?php echo site_url('pitch/edit/funding_needs'); ?>" class="btn btn-success btn-sm pull-right">Continue</a>
</div>


<div class="modal fade" id="add-competitor" tabindex="-1" role="dialog">
	<form id="competitor-data">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Tell us about this competitor</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label">Name or type of competitor:</label>
					<input type="text" name="competitor_name" class="form-control">
					<span id="helpBlock" class="help-block">Such as "John's Coffee Shop" or "Fitness-wellness stores"</span>
				</div>
				<div class="form-group">
					<label class="form-label">What are your advantages over this competitor?</label>
					<input type="text" name="competitor_advantage" class="form-control">
					<span id="helpBlock" class="help-block">Such as "Brand recognition" or "Patented technology" or "Lower price"</span>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="submit-competitor-data"><i class="fa fa-plus"></i> Add competitor</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->

<div class="modal fade" id="update-competitor" tabindex="-1" role="dialog">
	<form id="update-competitor-data">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"></div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->

</div>
<script type="text/javascript">

$(function(){

	$("#submit-competitor-data").click(function(){
		var data = $("#competitor-data").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";


		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/save_competitor'); ?>",
		  data: data
		}).done(function( msg ) {
			var data = JSON.parse(msg);

			if(data.action == 'success'){
				$("#add-competitor").modal('hide');
				$("#competitor-data").trigger( "reset" ); // reset modal textfields
				$('#competitors').load(location.href + ' #competitors-load');
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
			  data: {plan_id: id, table:'pitch_competition', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_competition', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is visible from the view');
				}

			});
	    }
	});

})

function delete_competition(id){
	var del = confirm("Confirm delete action.");

	if(del)
	{
		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/delete_competitor'); ?>",
		  data: {id: id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
		}).done(function( msg ) {
			var data = JSON.parse(msg);

			if(data.action == 'success'){
				$("#add-competitor").modal('hide');
				$('#competitors').load(location.href + ' #competitors-load');
			}

		});
	}
}

</script>

<?php $this->load->view('includes/footer'); ?>