<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">New Team</h4>
	</div>
	<div class="modal-body">
		<div id="response-msg"></div>
		<div class="form-group">
			<label for="job" class="control-label">Team Name</label>
			<input type="text" class="form-control" name="team" id="team">
		</div>
	</div>
<div class="modal-footer">
	<button type="button" id="create-team" class="btn btn-default">Save changes</button>
</div>
<script type="text/javascript">
$(function(){
	$("#create-team").click(function(){
		var team_name = $("#team").val();

		if(team_name == ''){
			$("#team").parent(".form-group").addClass('has-error');
			return false;
		}
		else{
			$("#team").parent(".form-group").removeClass('has-error');	
		}


		$.ajax({
			method: "POST",
			url: "<?php echo site_url('teams/new_team'); ?>",
			data: $("form#new-team").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>",
		})
		.done(function( msg ) {
			var data = JSON.parse(msg);

			if(data.action == 'success')
			{
				location.href="<?php echo site_url('teams/edit_team'); ?>/"+data.team_id+"/"+data.team_organ_id;
			}
			
		});		
	})
})
</script>