<?php $this->load->view('includes/header'); ?>
	<h1>Teams</h1>

	<div class="container">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#users" aria-controls="home" role="tab" data-toggle="tab">Users</a></li>
			<li role="presentation"><a href="#teams" aria-controls="profile" role="tab" data-toggle="tab">Teams</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="users">
			<table class="table table-bordered">
				<thead>
					<th>Name</th>
					<th>Company</th>
					<th>Telephone</th>
					<th>Job</th>
				</thead>
				<?php foreach($users as $user): ?>
				<tr>
					<td><a href="#"><?php echo $user->first_name.' '.$user->last_name; ?></a></td>
					<td><?php echo $user->company; ?></td>
					<td><?php echo $user->tel_number; ?></td>
					<td><?php echo $user->job_title; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="teams">
				<table class="table table-bordered">
					<thead>
						<th>Team Name</th>
						<th>Description</th>
						<th>Manager</th>
						<th>Memebers</th>
					</thead>
					<?php foreach($teams as $team): ?>
					<tr>
						<td><?php echo $team->name; ?></td>
						<td><?php echo $team->description; ?></td>
						<td><?php echo user_info('first_name', $team->manager_id).' '.user_info('last_name', $team->manager_id); ?></td>
						<td>-</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<a href="#newteam" data-toggle="modal" class="btn btn-primary">New Team</a>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="newteam" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">New Team</h4>
					</div>
					<div class="modal-body">
						
							<div id="response-msg"></div>
							<div class="form-group">
								<label for="job" class="control-label">Team Name</label>
								<input type="text" class="form-control" id="team">
							</div>
							<div class="form-group">
								<label for="description" class="control-label">Description</label>
								<textarea id="description" class="form-control"></textarea>
							</div>
							<div class="form-group">
								<label for="job" class="control-label">Manager</label>
								<select class="form-control" id="manager">
									<option value="">-- Select Manager --</option>
									<?php foreach($users as $user): ?>
									<option value="<?php echo $user->user_id; ?>"><?php echo $user->first_name.' '.$user->last_name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="clearfix">
								<div class="col-sm-6">
									<fieldset>
										Non members
									</fieldset>
								</div>
								<div class="col-sm-6">
									<fieldset>
										Members
									</fieldset>
								</div>
							</div>
					</div>
					<div class="modal-footer">
						<button type="button" id="create-team" class="btn btn-primary">Save changes</button>
					</div>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">

	$(function(){
		$("#create-team").click(function(){
			var team_name = $("#team").val();
			var manager = $("#manager").val();
			var desc = $("#description").val();

			if(team_name == ''){
				$("#team").parent(".form-group").addClass('has-error');
				return false;
			}
			else{
				$("#team").parent(".form-group").removeClass('has-error');	
			}

			if(manager == ''){
				$("#manager").parent(".form-group").addClass('has-error');
				return false;
			}
			else{
				$("#manager").parent(".form-group").removeClass('has-error');
			}


			$.ajax({
				method: "POST",
				url: "<?php echo site_url('teams/new_team'); ?>",
				data: { team: team_name, manager: manager, description: desc }
			})
			.done(function( msg ) {
				$("#team").val("");
				$("#manager").val("");
				$("#description").val("");
				$('#newteam').modal('hide');
				location.reload();
			});		

		})
	})

</script>	
	
<?php $this->load->view('includes/footer'); ?>