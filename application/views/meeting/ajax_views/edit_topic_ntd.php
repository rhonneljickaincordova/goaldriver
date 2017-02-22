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
	$ntd = $ntds[0];
?>


<?php echo form_open("", array("id"=>"update-topic-ntd-information")) ;?>

<div class="form-group">

	<div class="topic-title-cont">
		<div>
			<label class="">Name</label>
		</div>

		<div>
			<input type="text" class="form-control no_enter" id="topic-ntd-text" value="<?php echo $ntd['text'] ?>" name="ntd_text">
		</div>
	</div>

	<div class="save-action-toolbar">
		<div>
			<button type="button" class="btn btn-primary pull-right btn-save-updated-topic-ntd" data-id="<?php echo $ntd['id'] ?>" style="margin-top:3px"> Update</button>
		</div>
	</div>

</div>

<?php echo form_close() ;?>

