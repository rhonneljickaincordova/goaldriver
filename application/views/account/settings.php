<?php $this->load->view('admin/includes/header'); ?>
<div class="bg-white-wrapper clearfix">
<?php echo form_open('account/settings', array('class' => 'form-horizontal')); ?>
<div class="col-sm-12">

	<?php echo $this->session->flashdata('alert_msg'); ?>

	<div class="form-group">
		<label for="account_name" class="col-sm-3 control-label">Account name</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="account_name" id="account_name" placeholder="Account name" value="<?php echo $account_name; ?>">
		</div>
	</div>

	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">Email</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="email" id="email" placeholder="Your Email" value="<?php echo $email; ?>">
			<span class="help-block alert-danger"><?php echo form_error('email'); ?></span>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">&nbsp;</label>
		<div class="col-sm-9">
			<input type="submit" class="btn btn-primary" value="Save">
		</div>
	</div>
</div>
<?php echo form_close(); ?>


</div>


<?php $this->load->view('admin/includes/footer'); ?>