<link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/jquery-confirm/dist/jquery-confirm.min.css" />

<script src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
<script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>public/script.js"></script>
<script src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
<script src="<?php echo base_url() ?>public/chosen/chosen.jquery.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-confirm/dist/jquery-confirm.min.js"></script>

<style type="text/css">
	.modal-header .close
	{
		font-size:40px;
		margin-top: -10px !important;
	}
</style>

<?php 
	$organ_id = $this->session->userdata('organ_id');
	$meeting_logo = get_organ_logo($organ_id);

	if(!empty($meeting_logo))
	{
		$logo = $meeting_logo[0];
	}
?>

<?php echo form_open_multipart(base_url("index.php/meeting/save_logo_image"), array("id"=>"save_logo_image")) ;?>

<div class="row">
	<div class="col-sm-12">

		<div>
			<label class="">Image</label>
		</div>

		<div>
			<input type="file" class="form-control" name="userfile">
		</div>

		<input type="hidden" name="user_id" value="<?php echo encrypt($user_id) ?>" />
		<input type="hidden" name="organ_id" value="<?php echo encrypt($organ_id) ?>" />

		<div>
			<button type="submit" name="submit" class="btn btn-default btn-sm pull-right btn-save-logo-image" style="margin-top:5px"><i class="fa fa-download"></i> Upload company logo</button>
		</div>

	</div>
</div>


<div class="row">
	<div class="col-sm-12">
		<div class="company_logo_cont">
			<div>
				<label class="">Company Logo</label>
			</div>
			<div>
				<?php if(!empty($logo)) :?>
					<img class="img-responsive" width="150" src="<?php echo base_url() ?>uploads/user_logo_images/<?php echo $logo['image_name'] ?>" />
				
				<?php else:?>
					<p>Not yet set.</p>	

				<?php endif;?>
			</div>
		</div>
	</div>
</div>

<?php echo form_close() ;?>
