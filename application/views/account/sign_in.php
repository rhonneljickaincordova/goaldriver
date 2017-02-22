<?php $this->load->view('includes/header'); ?>


	<div class="bg-white-wrapper clearfix">
		<div id="login" class="col-sm-6 col-sm-offset-3">
			
			<?php if('' != $error): ?>
			<div class="alert alert-danger"><i class="fa fa-info-circle"></i> <?php echo $error; ?></div>
			<?php endif; ?>

			<?php echo form_open('account/sign_in', array('class' => 'form-horizontal')); ?>
				<!-- <div class="form-group">
					<label for="username" class="col-sm-2 control-label">Username</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php echo set_value('username'); ?>">
						<span class="help-block alert-danger"><?php echo form_error('username'); ?></span>
					</div>
				</div> -->

				<div class="form-group">
					<label for="email" class="col-sm-2 control-label">Email</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo set_value('email'); ?>">
						<span class="help-block alert-danger"><?php echo form_error('email'); ?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="password" class="col-sm-2 control-label">Password</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" name="password" id="password" placeholder="Password">
						<span class="help-block alert-danger"><?php echo form_error('password'); ?></span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">&nbsp;</label>
					<div class="col-sm-10">
						<label class="remember-me"><input type="checkbox" name="remember_me" value="1"> Remember me</label>
						<input type="submit" class="btn btn-primary pull-right" value="Sign In">
						<br><br>
						<span class="help-block"><a href="<?php echo site_url('account/forgot_password'); ?>">Forgotten password, cant login?</a></span>
					</div>
				</div>
				<hr>
				<div class="signup-section">
					<p>Dont have an account?</p>
					<a href="<?php echo site_url('account/sign_up'); ?>">Sign up here</a>
				</div>
			<?php echo form_close(); ?>
		</div>
	</div> <!-- .bg-white-wrapper -->


<?php $this->load->view('includes/footer'); ?>