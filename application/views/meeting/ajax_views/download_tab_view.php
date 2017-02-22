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

<?php echo form_open(base_url()."index.php/meeting/download-meeting-minutes", array("method"=>"GET", "target"=>"_blank")) ?>

<input type="hidden" name="meetingID" value="<?php echo encrypt($meeting_id) ?>" />

<div class="form-group">
	<label class=""><input id="" name="download_type" type="radio" value="pdf" checked="checked"> Download PDF</label>
</div>

<hr/>

<div class="form-group">
	<label class="">
		<input id="" name="show_boxes" type="checkbox" value="yes"> Include notes area for printing?
	</label>
</div>

<!-- 
<div class="form-group">
	<label class=""><input id="" name="meeting_link" type="checkbox" value="yes"> Include link to meeting workspace</label>
</div> -->

<div class="form-group">
	<label class=""></label>
	<button type="submit" class="btn btn-primary pull-right btn-download-meeting">Download</button>
</div>

<?php echo form_close() ?>