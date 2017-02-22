<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body style="background:#7f90a4;font-family:sans-serif;font-size:14px;">
	<div style="width:400px;margin:0 auto;padding:30px;background:#fff;">
		<div style="margin-bottom:2em;"><a href="#" style="width:250px;margin:0 auto;"><img src="<?php echo base_url().'public/images/GoalDriver_logo_250.png'; ?>"></a></div>
		<p>Hi <?php echo $user_to_info->first_name; ?>,</p> <br />
		<p>Welcome to your new planner, you can login anytime to the site here:</p>
		<p><a href="<?php echo $site_url; ?>"><?php echo $site_url; ?></a></p>
		 
		<?php if($is_exist == 0) :?>
		<table>
			<tr>
				<td width="90">Email:</td>
				<td><?php echo $user_to_info->email; ?></td>
			</tr>
			<tr>
				<td width="90">Password:</td>
				<td><?php echo $user_to_info->password; ?></td>
			</tr>
		</table> 
		<?php endif;?>
	</div>
</body>
</html>