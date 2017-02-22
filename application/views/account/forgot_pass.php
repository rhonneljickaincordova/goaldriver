<?php $this->load->view('includes/header'); ?>

<?php echo form_open('account/forgot_password', array('class' => 'form_horizontal')); ?>

<div class="bg-white-wrapper">
<div class="form-group">
	<?php echo $msg; ?>
	<label class="control-label col-sm-2">Email Address</label>
	<div class="col-sm-4">
		<input type="text" class="form-control" name="email" placeholder="Enter your email address here..." value="<?php echo set_value('email'); ?>">
	</div>
	<input type="submit" class="btn btn-primary" name="submit" value="Recover my password">
</div>
</div>
<?php echo form_close(); ?>

<?php $this->load->view('includes/footer'); ?>