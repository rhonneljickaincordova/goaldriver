<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body style="background:#7f90a4;font-family:sans-serif;font-size:14px;">
	<div style="width:400px;margin:0 auto;padding:30px;background:#fff;">
		<div style="margin-bottom:2em;"><a href="#" style="width:250px;margin:0 auto;"><img src="<?php echo base_url().'public/images/GoalDriver_logo_250.png'; ?>"></a></div>
		<p>Hi <?php echo $your_name; ?>,</p> <br />
		<p>Welcome to your new planner, you can login anytime to the site here:</p>
		<p><a href="<?php echo $site_url; ?>"><?php echo $site_url; ?></a></p>
		 
		
		<table>
			<tr>
				<td width="90">Email:</td>
				<td><?php echo $your_email; ?></td>
			</tr>
			<tr>
				<td width="90">Password:</td>
				<td><?php echo $your_password; ?></td>
			</tr>
		</table> 
		
	</div>
</body>
</html>