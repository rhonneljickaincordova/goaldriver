<?php $this->load->view('includes/header'); ?>


<!-- <div class="col-sm-12">
	<p><input type="checkbox" id="checkAll_permission" /> Check all</p>	
</div> -->

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />
<script type="text/javascript">
    $(document).ready(function(){
        var rights = $('.check_rights').val();

        if(rights == "disabled")
        {
            $('.btn-toolbar a').hide();
            $('.actions-toolbar a').hide();
            $('.action_header').hide();
            $('a.user_actions').hide();
            $('td > a').removeAttr('href');
            $('button').hide();
            $('div > p').hide();
            $('input[type=radio]').attr("disabled", "disabled");
        }
    
    });
</script>



<?php echo form_open("", array("id"=>"set_permission_form")) ;?>

<div class="bg-white-wrapper clearfix">

	<div class="col-sm-12">
		<table class="table table-hover table-bordered dataTable no-footer" id="organisation_table" role="grid">
			
			<thead>
				<tr>
						<th>Organisation</th>

					<?php if(!empty($tabs)) :?>
						<?php foreach($tabs as $tab) :?>
							<th><?php echo ucfirst($tab["tab_name"]) ?></th>
						<?php endforeach;?>
					<?php endif;?>
				
				</tr>
			</thead>

			<tbody>
			<?php
			if($organisations !== false){
				foreach($organisations as $organisation){
					?>
					<tr>	

						<?php 
							$encrypted = encrypt($user_id);
							$org_id = $organisation->organ_id;
						?>

						<td>
							<?php echo $organisation->name; ?>
						</td>

						<?php if(!empty($tabs)) :?>
							<?php foreach($tabs as $tab) :?>

								<?php 

									$check_hidden = "";
									$check_readonly = "";
									$check_readwrite = "";
									$check_readwrite_default = "";

									$hidden = check_hidden_permission($user_id, $org_id, $tab['id']);
									$readonly = check_readonly_permission($user_id, $org_id, $tab['id']);
									$readwrite = check_readwrite_permission($user_id, $org_id, $tab['id']);

									if(!empty($hidden))
									{
										if($hidden[0]['hidden'] == 1)
										{
											$check_hidden = "checked";
										}
										if($hidden[0]['hidden'] == 0)
										{
											$check_hidden = "";
										}
									}

									if(!empty($readonly))
									{
										if($readonly[0]['readonly'] == 1)
										{
											$check_readonly = "checked";
										}
										if($readonly[0]['readonly'] == 0)
										{
											$check_readonly = "";
										}
									}

									if(!empty($readwrite))
									{
										if($readonly[0]['readwrite'] == 1)
										{
											$check_readwrite = "checked";
										}

										if($readonly[0]['readwrite'] == 0)
										{
											$check_readwrite = "";
										}
									}

									else
									{
										$check_readwrite_default = "checked";
									}
									
								?>

								<td>
									
									<input type="radio" class="perm_checkbox" <?php echo $check_hidden ?>  name="permission[<?php echo $org_id ?>][<?php echo $tab['id'] ?>][right]" value="hidden" /> Hidden <br />
									
									<input type="radio" class="perm_checkbox" <?php echo $check_readonly ?>  name="permission[<?php echo $org_id ?>][<?php echo $tab['id'] ?>][right]" value="readonly" /> Read-only <br />
																	
									<input type="radio" <?php echo $check_readwrite_default ?> class="perm_checkbox" <?php echo $check_readwrite ?> name="permission[<?php echo $org_id ?>][<?php echo $tab['id'] ?>][right]" value="readwrite" /> Read/Write

								</td>
							<?php endforeach;?>
						<?php endif;?>
						
					</tr>

					<?php
				}	
				
			}
			?>
			</tbody>
		</table>	

		<input type="hidden" name="user_id" value="<?php echo $encrypted ?>" />

		<div class="col-sm-12">
			<a href="<?php echo site_url('teams/user'); ?>" class="pull-right" style="margin-top: 7px;margin-left: 10px;"> Go back</a>
			<p class="pull-right" style="margin-top: 7px;margin-left: 10px;">OR</p>
			<button type="button" id="save_user_permission" class="btn btn-primary pull-right">Save</button>  
		</div>

	</div>

</div>

<?php echo form_close() ;?>

<?php $this->load->view('includes/footer'); ?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#checkAll_permission').bind('click', function(){
			$("input:checkbox").prop('checked', $(this).prop("checked"));
		});

	
		/** Saving permission **/
		$('#save_user_permission').bind('click', function(){
			var form_data = $('#set_permission_form').serialize();

			$.post(base_url+"index.php/permission/save_user_permission", form_data, function(response){
				var obj = $.parseJSON(response);
				//jAlert(obj['message'],"Permission set");
				$.alert({
				    title: 'Permission has been set!',
				    content: obj['message'],
				    confirmButtonClass: 'btn-success',
				});
				setTimeout(function(){
					window.location.href = base_url + "index.php/teams/user";
				},1000);
			});
		});

	});
</script>