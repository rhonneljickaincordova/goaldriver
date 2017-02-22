<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?php echo $title; ?></title></head>
<body style="background:#7f90a4;font-family:sans-serif;font-size:14px;">
	<div style="width:400px;margin:0 auto;padding:30px;background:#fff;">
		<div style="margin-bottom:2em;"><a href="#" style="width:250px;margin:0 auto;"><img src="<?php echo base_url().'public/images/GoalDriver_logo_250.png'; ?>"></a></div>
		<p>Your strategy is now published.</p>
		<p>Published by: <?php echo $creator; ?></p>
		<div>Secret URL: <a href="<?php echo $secret_url ?>"><?php echo $secret_url ?></a></div>
		
	</div>
</body>
</html>