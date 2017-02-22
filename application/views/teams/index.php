<?php $this->load->view('includes/header'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />
<script type="text/javascript">
    $(document).ready(function(){
        var rights = $('.check_rights').val();

        if(rights == "disabled")
        {
            $('button[data-toggle=modal]').remove();
            $('a[data-toggle=modal]').remove();
            $('.actions a').remove();
            $('.action_header').remove();
            $('a.user_actions').remove();
            $('td > a').removeAttr('href');
        }
    
    });
</script>

<div class="bg-white-wrapper">
	<div class="btn-group btn-group-sm" role="group" aria-label="">
		<a href="<?php echo site_url('teams/user'); ?>" class="btn btn-default"><i class="fa fa-male"></i> Users</a>&nbsp;&nbsp;
		<a href="<?php echo site_url('teams'); ?>" class="btn btn-default active"><i class="fa fa-users"></i> Teams</a>
	</div>
	<a href="<?php echo site_url('teams/ajax_new_team'); ?>" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newteam"><i class="fa fa-plus"></i> New Team</a>
	
	<br><br>	
	<table class="table table-hover" id="teams">
		<thead>
			<th>Team Name</th>
			<th>Description</th>
			<th>Manager</th>
			<th>Members</th>
			<th class="action_header">Action</th>
		</thead>
		<?php 
		$teams = $this->Team_model->get_teams($user_id, $organ_id);

		//print_r($teams);
			
		if(count($teams)):
		foreach($teams as $team): 
			$count_member = $this->Team_users_model->get_team_members($team->manager_id, $team->team_id);
			?>
		<tr>
			<td><a href="<?php echo site_url('teams/edit_team/'.encrypt($team->team_id)."/".encrypt($team->organ_id)); ?>"><?php echo $team->name; ?></a></td>
			<td><?php echo $team->description; ?></td>
			<td><?php echo $team->first_name.' '.$team->last_name; ?></td>
			<td><?php echo count($count_member); ?></td>
			<td style="text-align:right;width:180px;" class="actions">
				<a href="<?php echo site_url('teams/edit_team/'.encrypt($team->team_id)."/".encrypt($team->organ_id)); ?>"><i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a> 
				&nbsp;&nbsp;<a href="#" onclick="delete_team(<?php echo $team->team_id; ?>)"><i class="fa fa-trash-o" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>
			</td>
		</tr>
		<?php endforeach; else: ?>
		<tr><td colspan="5">You have no teams setup at the moment, click the add team button to get started</td></tr>
		<?php endif; ?>
	</table>	
</div>		

<!-- Modal -->
<div class="modal fade" id="newteam" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<form id="new-team">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">

	$(function(){
		

	})

	function delete_team(id){
		var result = confirm('Are you sure you want to delete?');

		if(result){
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('teams/delete_team'); ?>",
				data: { team_id: id, user_id: <?php echo $user_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			})
			.done(function( msg ) {
				location.reload();
			});		
		}
	}

</script>	
	
<?php $this->load->view('includes/footer'); ?>