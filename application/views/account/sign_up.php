<?php $this->load->view('includes/header'); ?>

<div class="bg-white-wrapper clearfix">
<div class="col-sm-6 col-sm-offset-3">
	
	<?php if('' != $error): ?>
	<div class="alert alert-danger"><i class="fa fa-info-circle"></i> <?php echo $error; ?></div>
	<?php endif; ?>


	<?php echo form_open('account/sign_up', array('class' => 'form-horizontal')); ?>
		<div class="form-group">
			<label for="first-name" class="col-sm-3 control-label">First name</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="firstname" id="first-name" placeholder="Your first name" value="<?php echo set_value('firstname'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('firstname'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="last-name" class="col-sm-3 control-label">Last name</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="lastname" id="last-name" placeholder="Your last name" value="<?php echo set_value('lastname'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('lastname'); ?></span>
			</div>
		</div>

		<!-- <div class="form-group">
			<label for="username" class="col-sm-3 control-label">Username</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php echo set_value('username'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('username'); ?></span>
			</div>
		</div> -->

		<div class="form-group">
			<label for="company_name" class="col-sm-3 control-label">Company</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="company" id="company_name" placeholder="Company" value="<?php echo set_value('company'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('company'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="job-title" class="col-sm-3 control-label">Job Title</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="job" id="job-title" placeholder="Job title" value="<?php echo set_value('job'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('job'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="email" class="col-sm-3 control-label">Email</label>
			<div class="col-sm-9">
				<input type="email" class="form-control" name="email" id="email" placeholder="Your email" value="<?php echo set_value('email'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('email'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="password" class="col-sm-3 control-label">Password</label>
			<div class="col-sm-9">
				<input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
				<span class="help-block alert-danger"><?php echo form_error('password'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="re-password" class="col-sm-3 control-label">Confirm password</label>
			<div class="col-sm-9">
				<input type="password" class="form-control" name="password2" id="re-password" placeholder="Enter Password again">
				<span class="help-block alert-danger"><?php echo form_error('password2'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="tel" class="col-sm-3 control-label">Telephone</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="tel" id="tel" placeholder="Your telephone" value="<?php echo set_value('tel'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('tel'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="postcode" class="col-sm-3 control-label">Postcode</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="postcode" id="postcode" placeholder="Postcode" value="<?php echo set_value('postcode'); ?>">
				<span class="help-block alert-danger"><?php echo form_error('postcode'); ?></span>
			</div>
		</div>

		<div class="form-group">
			<label for="timezone" class="col-sm-3 control-label">Timezone</label>
			<div class="col-sm-9">
				<?php echo timezone_menu_gd('Europe/London', 'form-control', 'timezone'); ?>
			</div>
			<span class="help-block alert-danger"><?php echo form_error('timezone'); ?></span>
		</div>

		<div class="form-group">
			<label for="employees" class="col-sm-3 control-label">Employees</label>
			<div class="col-sm-9">
				<select id="employees" name="employees" class="form-control">
					<option value="1-5">1-5</option>
					<option value="6-9">6-9</option>
					<option value="10-14">10-14</option>
					<option value="15-20">15-20</option>
					<option value="20+">20+</option>
				</select>
			</div>
			<?php echo form_error('employees'); ?>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
	          <div class="checkbox">
	            <label>
	              <input type="checkbox" name="terms" checked> Agree Terms and Conditions
	            </label>
	          </div>
	          <span class="help-block alert-danger"><?php echo form_error('terms'); ?></span>
	        </div>
	        
	    </div>

		<div class="form-group">
			<label class="col-sm-3 control-label">&nbsp;</label>
			<div class="col-sm-9">
				<input type="submit" class="btn btn-primary" value="Register">
				Already have an account? <a href="<?php echo site_url('account/sign_in'); ?>">Sign in here</a>
			</div>
		</div>
	<?php echo form_close(); ?>
</div>
</div>
<?php $this->load->view('includes/footer'); ?>