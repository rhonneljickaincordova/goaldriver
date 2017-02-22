<?php $this->load->view('admin/includes/header'); ?>
<div id="main-content">
	<div <?php if($is_owner == true){echo 'ng-controller="Organisations"'; }?>>

	<?php 
		if($this->session->userdata('error_message') != null)
		{
				?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->userdata('error_message'); ?>
				</div>
				<?php
				$this->session->unset_userdata('error_message');
		}
	?>
	<?php 
		if($is_owner == true)
		{
			?>
			<div class="form-group col-sm-6 col-md-6">
				<div class="btn-toolbar">
					<button type="button" class="btn btn-primary" ng-click="open_addOrganisation()" data-toggle="modal"><i class="fa fa-plus"></i> New Organisation</button>
				</div>
			</div>
			<!--response message here-->
			<div id="alert_message" class="col-md-12" ></div>
			<!--response message end-->
			<?php
		}
	?>
	<table class="table table-hover dataTable no-footer" id="organisation_table" role="grid">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Last Viewed</th>
				<th>Access</th>
				<th><i class="fa fa-pencil" style="font-size:15px; margin-right:10px;"></i><i class="fa fa-trash-o" style="font-size:15px"></i></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if($organisations !== false){
			foreach($organisations as $organisation){
				?>
				<tr id="organ_id-<?php echo $organisation->organ_id; ?>">	
					<td>
						<?php echo $organisation->organ_id; ?>
					</td>
					<td style="cursor:pointer;color:#13b5ea;" onclick="location.href='<?php echo base_url("index.php/user-settings/organisations/change_organisation_main/".encrypt($organisation->organ_id)) ?>'">
						<?php echo $organisation->name ?>
					</td>
					<td>
						<?php 
							$last_loggedin = get_org_last_logged_in($organisation->organ_id);

							if(!empty($last_loggedin))
							{
								$data = $last_loggedin[0];
							}

							echo date("F d, Y", strtotime($data->last_logged_in)) . " by ". user_info("first_name", $data->user_id) . " ". user_info("last_name", $data->user_id);
						?>
					</td>
					
					<?php /*
					<td>
						<?php echo date("F d, Y", strtotime($organisation->updated)) . " by ".$organisation->first_name . " ". $organisation->last_name; ?>
					</td>
					*/
					?>
					
					<td>
						<?php 
						$access = "Member";
						if($is_owner)
						{
							if($user_id == $organisation->owner_id)
							{
								$access = "Admin";
							}	
						}
						
						echo $access;
						?>
					</td>
					<td>
						
					</td>
				</tr>
				<?php
			}	
			
		}
		?>
		</tbody>
	</table>	
	<?php
	if($is_owner == true)
	{
		$this->load->view("admin/organisations/modal/add.php"); 
		$this->load->view("admin/organisations/modal/edit.php"); 
		$this->load->view("admin/organisations/modal/delete.php"); 
	}
	?>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
