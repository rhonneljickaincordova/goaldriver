<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?php echo $title; ?></title></head>
<body style="background:#7f90a4;font-family:sans-serif;font-size:14px;">
	<div style="width:400px;margin:0 auto;padding:30px;background:#fff;">
		<div style="margin-bottom:2em;"><a href="#" style="width:250px;margin:0 auto;"><img src="<?php echo base_url().'public/images/GoalDriver_logo_250.png'; ?>"></a></div>
		
		<p style="text-align:center">
			<?php if( $status == 1) :?>	
				<img class="img-responsive" width="40" src="<?php echo base_url("uploads/footer/happy.png");?>" style="text-align:center"  />
			<?php endif;?>
			
			<?php if( $status == 2) :?>	
				<img class="img-responsive" width="40" src="<?php echo base_url("uploads/footer/ok.png");?>" style="text-align:center"  />
			<?php endif;?>
		
			<?php if( $status == 3) :?>	
				<img class="img-responsive" width="40" src="<?php echo base_url("uploads/footer/sad.png");?>" style="text-align:center"  />
			<?php endif;?>
			
			<br />
		</p>

		<p style="text-align:center"><strong><?php echo date("Y/m/d")?></strong></p>
		

		<p><strong>From: <?php echo user_info("first_name", $user_id)." ".user_info("last_name", $user_id)." - [ ".$email_add." ]" ;?> </strong></p>
		<p><strong>Message:<?php echo $feedback; ?></strong></p>
		
	</div>
</body>
</html>