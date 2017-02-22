<?php $this->load->view('includes/header'); ?>

<div ng-controller="resetPasswordCtrl" ng-init="onGetUserID('<?php echo $password_token_id;?>','<?php echo base_url(); ?>')">

	<div class="bg-white-wrapper clearfix">
	<div class="alert alert-danger" ng-show="showError">{{errorMessage}}</div>
	<div class="alert alert-success" ng-show="showSuccess">{{successMessage}} <a href="<?php echo base_url(); ?>" > click here to login...</a></div>
		<div class="row">
			
			<div class="form-group">
				<label class="control-label col-sm-5">Enter new password <span class="text-danger">(required)</span></label>
				<div class="col-sm-12">
					<input type="password" class="form-control" name="password" ng-model="password" >
				</div>
			</div>
			<div class="form-group" >
				<label class="control-label col-sm-5">Repeat password <span class="text-danger">(required)</span></label>
				<div class="col-sm-12">
					<input type="password" class="form-control" name="repeatpassword"  ng-model="repeatpassword">
				</div>
			</div>
		</div>	
		<br>
		<button type="button" class="btn btn-primary" ng-click="onClickSave('<?php echo $user_id; ?>','<?php echo $token_id; ?>','<?php echo base_url(); ?>')">Save</button>

	</div>
</div>
<?php $this->load->view('includes/footer'); ?>