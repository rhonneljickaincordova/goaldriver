<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?php echo $title; ?></title></head>
<body style="background:#7f90a4;font-family:sans-serif;font-size:14px;">
	<div style="width:400px;margin:0 auto;padding:30px;background:#fff;">
		<div style="margin-bottom:2em;"><a href="#" style="width:250px;margin:0 auto;"><img src="<?php echo base_url().'public/images/GoalDriver_logo_250.png'; ?>"></a></div>
		<p>Hi <?php echo $first_name; ?></p>
		<p>You recently requested a password reset.</p>
		<p>To change your GoalDriver password, click <a href="<?php echo base_url('index.php/account/reset_pass/'.$token); ?>">here</a> or paste the following link into your browser:</p>
		<p><?php echo base_url('index.php/account/reset_pass/'.$token); ?></p>
		<br>
		<p>Thanks for using GoalDriver!</p>
		<p>The GoalDriver Team</p>
	</div>
</body>
</html>