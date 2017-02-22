<?php $this->load->view('admin/includes/header'); ?>
<div class="bg-white-wrapper clearfix">
	    <?php 
	    echo $alert;

	    $hidden = array('save_profile' => TRUE);
	    echo form_open_multipart('account/profile', array('class' => 'form-horizontal', 'id' => 'profile-form'), $hidden); ?>
	  	<div class="col-sm-8">
	  		<div class="form-group">
				<label for="profile_pic" class="col-sm-3 control-label">Profile Picture</label>
				<div class="col-sm-9">
					<div class="pic">
						<?php if('' == $profile_pic):?>
						<img class="thumbnail" src="<?php echo base_url(); ?>/public/images/nophoto.jpg" alt="Profile Picture" width="150">
						<div class"upload-file">
							<div class="fileUpload btn btn-primary">
							    <span>Upload file</span>
							    <input type="file" class="upload" onchange="readURL(this);" name="profile_pic" id="profile_pic" />
							</div>
						</div>
						<?php else: ?>
						<img class="thumbnail" src="<?php echo profile_pic($user_id, $profile_pic); ?>" alt="Profile Picture" width="150">
						<a href="#" onclick="delete_thumb();">Remove</a>
						<?php endif; ?>
					</div>
					
				</div>
			</div>

		  	<div class="form-group">
				<label for="first-name" class="col-sm-3 control-label">Name</label>
				<div class="row col-sm-9">
					<div class="col-sm-6">
					<input type="text" class="form-control" name="firstname" id="first-name" placeholder="Your First name" value="<?php echo $firstname; ?>">
					<span class="help-block alert-danger"><?php echo form_error('firstname'); ?></span>
					</div>
					<div class="col-sm-6">
					<input type="text" class="form-control" name="lastname" id="last-name" placeholder="Your Last name" value="<?php echo $lastname; ?>">
					<span class="help-block alert-danger"><?php echo form_error('lastname'); ?></span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="email" class="col-sm-3 control-label">Email</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>">
					<span class="help-block alert-danger"><?php echo form_error('email'); ?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="tel" class="col-sm-3 control-label">Phone</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="tel" id="tel" placeholder="Your Telephone" value="<?php echo $tel; ?>">
					<span class="help-block alert-danger"><?php echo form_error('tel'); ?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="job" class="col-sm-3 control-label">Job Title</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="job" id="job" placeholder="Your Job" value="<?php echo $job; ?>">
					<span class="help-block alert-danger"><?php echo form_error('job'); ?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="about_me" class="col-sm-3 control-label">A bit about me</label>
				<div class="col-sm-9">
					<textarea name="about_me" class="form-control" rows="5" id="about_me" placeholder="More about yourself...."><?php echo $about_me; ?></textarea>
					<span class="help-block alert-danger"><?php echo form_error('about_me'); ?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="password" class="col-sm-3 control-label">Password</label>
				<div class="col-sm-9">
					<a href="#changepass" data-toggle="modal" data-target="#changepass">Change password</a>
				</div>
			</div>

			<div class="form-group">
				<label for="timezone" class="col-sm-3 control-label">Timezone</label>
				<div class="col-sm-9">
					<?php echo timezone_menu_gd($timezone, 'form-control', 'timezone'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label">&nbsp;</label>
				<div class="col-sm-9">
					<input type="submit" class="btn btn-primary" value="Save">
				</div>
			</div>

		<?php echo form_close(); ?>
	</div>
</div>

<!-- Change password Modal -->
<div class="modal fade" id="changepass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Change password</h4>
			</div>
			<div class="modal-body">
				<div id="response-msg"></div>
				<div class="form-group">
					<label for="job" class="control-label">Current password</label>
					<input type="password" class="form-control" id="old_password" placeholder="Enter your current password">
				</div>
				<div class="form-group">
					<label for="job" class="control-label">Your new password</label>
					<input type="password" class="form-control" id="new_password" placeholder="Enter your new password">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="savepass" class="btn btn-default">Save changes</button>
			</div>
		</div>
	</div>
</div>

	<script type="text/javascript">

	$(function(){

		$("#savepass").click(function(){
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('account/change_password'); ?>",
				data: { oldpass: $('#old_password').val(), newpass: $('#new_password').val() }
			})
			.done(function( msg ) {
				var myObj = $.parseJSON(msg);

				if(myObj.action == 'success'){
					$.alert(myObj.msg, 'Success');
					$('#changepass').modal('hide');
				}
				else if(myObj.action == 'failed'){
					$.alert(myObj.msg, 'Failed');	
				}
			});
		})
	})

	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
			var filename = input.files[0].name;

            var extension = filename.substr( (filename.lastIndexOf('.') +1) );

            if(extension == 'jpg' || extension == 'jpeg' || extension == 'gif' || extension == 'png'){
	           	reader.onload = function (e) {
	                $('.thumbnail')
	                    .attr('src', e.target.result)
	                    .width(150)
	            };

	            reader.readAsDataURL(input.files[0]);
			}
            else{
            	$("#ajax-msg").show().html('<div class="alert alert-danger"><i class="fa fa-info-circle"></i> Error, Please select an image file.</div>');

            	setTimeout(function(){
					$("#ajax-msg").hide();            		
            	}, 2000)
            }
		}
    }

    function delete_thumb(){
    	$.confirm({
			title: 'Confirm',
			content: 'Are you sure you want to remove your profile picture?',
			confirmButtonClass: 'btn-success',
    		cancelButtonClass: 'btn-danger',
			confirm: function(){
				$.ajax({
					method: "POST",
					url: "<?php echo site_url('account/remove_profile_photo'); ?>",
					data: { user_id: <?php echo $user_id; ?> }
				})
				.done(function( msg ) {
					var myObj = $.parseJSON(msg);
					if(myObj.action == 'success'){
						$.alert('Profile picture has been removed.', 'Success');
						location.reload();
					}
					else if(myObj.action == 'failed'){
						$.alert('Theres was an error deleting your profile picture.', 'Failed');
					}
				});
			},
			cancel: function(){
				//$.alert('Canceled!')
			}
		});

    }

	</script>

<?php $this->load->view('admin/includes/footer'); ?>