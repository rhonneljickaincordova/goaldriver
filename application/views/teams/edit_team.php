<?php $this->load->view('includes/header'); ?>

<?php echo form_open('teams/edit_team/'.encrypt($team_id)."/".encrypt($team_organ_id), array('class' => 'form-horizontal')) ;?>
<div class="bg-white-wrapper clearfix">
	<div class="col-sm-12">
		<?php echo $resp_msg; ?>

		<div class="form-group ">
			<label for="" class="col-sm-2 control-label"></label>
			<div class="col-sm-8">
				<div id="status_cont"></div>
			</div>
		</div>

		<div class="form-group">
			<label for="job" class="col-sm-2 control-label">Team Name</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="team" name="team" value="<?php echo $name; ?>">
				<span class="help-block alert-danger"><?php echo form_error('team'); ?></span>
			</div>
		</div>
		<div class="form-group">
			<label for="description" class="col-sm-2 control-label">Description</label>
			<div class="col-sm-8">
				<textarea id="description" class="form-control" name="description"><?php echo $description; ?></textarea>
				<span class="help-block alert-danger"><?php echo form_error('description'); ?></span>
			</div>
		</div>
		<div class="form-group">
			<label for="job" class="col-sm-2 control-label">Manager</label>
			<div class="col-sm-8">
				<select class="form-control" id="manager" name="manager">
					<option value="">-- Select Manager --</option>
					<?php 
					$managers = $this->Team_users_model->get_team_managers($user_id, $organ_id);
					foreach($managers as $manager): ?>
					<option <?php echo $manager->user_id == $manager_id ? 'selected' : ''; ?> value="<?php echo $manager->user_id; ?>"><?php echo $manager->first_name.' '.$manager->last_name; ?></option>
					<?php endforeach; ?>
				</select>
				<span class="help-block alert-danger"><?php echo form_error('manager'); ?></span>
			</div>
		</div>

		<div class="clearfix">
			<label for="job" class="col-sm-2 control-label">&nbsp;</label>
			<div id="members-area" class="col-sm-10 row">
				<div id="refresh">
					<div class="col-sm-5">
						<h4>Non members</h4>
						<div id="non-members" class="connectedSortable">
						<?php 
						$non_members = $this->Team_users_model->get_non_team_members($user_id, $team_id, $organ_id);
						foreach($non_members as $non_member): ?>
							<div id="<?php echo $non_member->user_id; ?>"><?php echo $non_member->first_name.' '.$non_member->last_name; ?></div>
						<?php endforeach; ?>
						</div>
					</div>
					<div class="col-sm-5">
						<h4>Members</h4>
						<div id="members" class="connectedSortable">
							<?php 
							$team_members = $this->Team_users_model->get_team_members($user_id, $team_id);
							foreach($team_members as $team_member): ?>
								<div id="<?php echo $team_member->user_id; ?>"><?php echo $team_member->first_name.' '.$team_member->last_name; ?></div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="form-group">
			<label class="col-sm-2 control-label">&nbsp;</label>
			<div class="col-sm-8">
				<input type="submit" class="btn btn-primary" value="Save"> or <a href="<?php echo site_url('teams'); ?>">Discard</a>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
	
<script type="text/javascript">
	$(function(){
		$( "#non-members, #members" ).sortable({
	      connectWith: ".connectedSortable",
	      receive: function(e, ui){
	      	var user_id = ui.item[0].id;
	      	var response = ui.item[0].parentElement;

	      	if($(response).attr("id") == 'members'){
	      		// add to this team
	      		$.ajax({
					method: "POST",
					url: "<?php echo site_url('teams/add_team_user'); ?>",
					data: { user_id: user_id, team_id: <?php echo $team_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				})
				.done(function( msg ) {
					$("#status_cont").show().html(msg);
					setTimeout(function() {
				        $("#status_cont").hide();
				    }, 1000);
				});	
	      	}
	      	else{
	      		// remove to this team
	      		$.ajax({
					method: "POST",
					url: "<?php echo site_url('teams/remove_team_user'); ?>",
					data: { user_id: user_id, team_id: <?php echo $team_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				})
				.done(function( msg ) {
					$("#status_cont").show().html(msg);
					setTimeout(function() {
				       $("#status_cont").hide();
				    }, 1000);
				});	
	      	}
	      }
	    }).disableSelection();	
	})
</script>

<?php $this->load->view('includes/footer'); ?>