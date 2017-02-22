<style type="text/css">
	#sub-page
	{
		font-size:17px;
		margin-bottom: 2%;
		padding: 0;
	}
	#sub-page li{
		display: inline-block;
		padding: 0 10px;
	}
	#sub-page li a{
		color: #777;
	}
	.borderless
	{
		border:0px !important;
	}
</style>

<?php
	$meeting_id = decrypt($this->uri->segment(3));
	if(!empty($meetings))
	{
		$meeting_title = $meetings[0]['meeting_title'];
	}
	else
	{
		$meeting_title = "";
	}
?>

<div class="bg-white-wrapper clearfix">

	<?php if($from_dashboard == true){
		?>
			<div class="col-sm-12 row">
				<h1><a href="<?php echo site_url('dashboard'); ?>">Dashboard</a> <i class="fa fa-angle-double-right" aria-hidden="true"></i> <span><?php echo $meeting_title ?></span> </h1><br>
			</div>
		<?php 
	}else if(!empty($meetings)){
		?>
			<div class="col-sm-12 row">
				<h1><a href="<?php echo site_url('meeting'); ?>">Meetings</a> <i class="fa fa-angle-double-right" aria-hidden="true"></i> <span><?php echo $meeting_title ?></span> </h1><br>
			</div>
		<?php 
	}
	?>

	<div class="">
		<ul id="sub-page" class=" borderless">
			<li style="<?php echo (!empty($meeting_id)) ? "" :"display:none;" ?>"><a href="javascript:void(0)" id="open_meeting_email" data-meeting-id="<?php echo $meeting_id ?>"><i class="fa fa-envelope-o" data-toggle='tooltip' data-placement='bottom' title='Email' ></i> </a></li>
			<li style="<?php echo (!empty($meeting_id)) ? "" :"display:none;" ?>"><a href="javascript:void(0)" id="open_meeting_print" data-meeting-id="<?php echo $meeting_id ?>"><i class="fa fa-print" data-toggle='tooltip' data-placement='bottom' title='Print' ></i> </a></li>
			<li style="<?php echo (!empty($meeting_id)) ? "" :"display:none;" ?>"><a href="javascript:void(0)" id="open_meeting_download" data-meeting-id="<?php echo $meeting_id ?>"><i class="fa fa-download" data-toggle='tooltip' data-placement='bottom' title='Download' ></i> </a></li>
			<li style="<?php echo (!empty($meeting_id)) ? "" :"display:none;" ?>"><a href="javascript:void(0)" id="open_meeting_attendance" data-meeting-id="<?php echo $meeting_id ?>"><i class="fa fa-users" data-toggle='tooltip' data-placement='bottom' title='Attendance' ></i> </a></li>
			<li style="<?php echo (!empty($meeting_id)) ? "" :"display:none;" ?>"><a href="javascript:void(0)" id="open_meeting_templates" data-meeting-id="<?php echo $meeting_id ?>"><i class="fa fa-bars" data-toggle='tooltip' data-placement='bottom' title='Templates' ></i> </a></li>
			<li style="<?php echo (!empty($meeting_id)) ? "" :"display:none;" ?>"><a href="javascript:void(0)" id="open_meeting_followup" data-meeting-id="<?php echo $meeting_id ?>"><i class="fa fa-share" data-toggle='tooltip' data-placement='bottom' title='Follow-up'></i> </a></li>
			<li ><a href="<?php echo base_url('index.php/meeting/manual') ?>" id="" data-meeting-id="<?php echo $meeting_id ?>" target="_blank"><i class="fa fa-question" data-toggle='tooltip' data-placement='bottom' title='Help'></i> </a></li>
		</ul>
	</div>
