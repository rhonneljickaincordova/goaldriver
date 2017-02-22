<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title><?php echo $title; ?></title></head>
<body style="background:#7f90a4;font-family:sans-serif;font-size:14px;">
	<div style="width:400px;margin:0 auto;padding:30px;background:#fff;">
		<div style="margin-bottom:2em;"><a href="#" style="width:250px;margin:0 auto;"><img src="<?php echo base_url().'public/images/GoalDriver_logo_250.png'; ?>"></a></div>
		<p>This task is updated by <?php echo $creator; ?></p>
		<div>Your task details:</div>
		<table>
			<tr>
				<td width="90">Name:</td>
				<td><?php echo $task_name; ?></td>
			</tr>
			<tr>
				<td width="90">Milestone:</td>
				<td><?php echo $milestone; ?></td>
			</tr>
			<tr>
				<td width="90">Start Date:</td>
				<td><?php echo $start_date; ?></td>
			</tr>
			<tr>
				<td width="90">Due Date:</td>
				<td><?php echo $due_date; ?></td>
			</tr>
		</table>
		<p><a href="<?php echo $task_url ?>"><?php echo $task_url ?></a></p>
	</div>
</body>
</html>