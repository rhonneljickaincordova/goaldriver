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
	$topic = $topics[0];

	if(!empty($meetings))
  	{
  		$meeting = $meetings[0];
  		
  		if(!empty($meeting['meeting_participants']))
	    {
	      $participants = unserialize($meeting['meeting_participants']);
	    }
  	}
?>


<?php echo form_open("", array("id"=>"update-topic-information")) ;?>

<div class="form-group">
	
	<input type="hidden" name="hdnTopicID" value="<?php echo $topic['topic_id'] ?>" />

	<div class="topic-title-cont">
		<div>
			<label class="">Topic Title</label>
		</div>

		<div>
			<input type="text" class="form-control" id="" value="<?php echo $topic['topic_title'] ?>" name="topic_title">
		</div>
	</div>

	<div class="topic-presenter-cont">
		<div>
			<label class="">Presenter</label>
		</div>

		<div>
			<select class="form-control" name="presenter">
				<?php if(!empty($topic['presenter'])) :?>
					<?php if(!empty($meetings)) :?>
						<?php if(!empty($meetings)) :?>
							<?php if(!empty($participants)) :?>
				                <?php foreach($participants as $par) :?>
				                    <option value="<?php echo $par ?>" <?php if($topic['presenter'] == $par) echo "selected"  ?> > <?php echo user_info("email", $par) ?></option>
				                <?php endforeach ;?>
				            <?php endif;?>
	                  	<?php endif;?>
                  	<?php endif;?>
				
				<?php else:?>
					<option value="">[-- Select Presenter --]</option>
					<?php if(!empty($meetings)) :?>
						<?php if(!empty($participants)) :?>
			                <?php foreach($participants as $par) :?>
			                    <option value="<?php echo $par ?>"><?php echo user_info("email", $par) ?></option>
			                <?php endforeach ;?>
			            <?php endif;?>
                  	<?php endif;?>
				<?php endif;?>

			</select>
		</div>
	</div>


	<div class="topic-duration-cont">
		<div>
			<label class="">Duration</label>
		</div>

		<div>
			<input type="text" class="form-control no_enter" id="" value="<?php echo $topic['time'] ?>" name="time">
		</div>
	</div>

	<div class="save-action-toolbar">
		<div>
			<button type="button" class="btn btn-primary pull-right btn-save-updated-topic-info" style="margin-top:3px"> Update</button>
		</div>
	</div>

</div>

<?php echo form_close() ;?>

