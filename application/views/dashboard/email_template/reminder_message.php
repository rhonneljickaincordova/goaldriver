<body>
	<p>Hi <?php echo $participant_name; ?>, </p>
	
	<p  style="margin-left:30px;margin-bottom:30px">
	
		Please be reminded that you have a 
		<strong><?php  echo $meeting_title; ?></strong>
		meeting from <?php  echo $meeting_time_duration; ?>.

	</p>
	<br/>
	<p>Thank you </p>
	<p><?php echo $sender_name; ?> </p>
	<small><p><?php echo $sender_mail; ?></p></small>
</body>
