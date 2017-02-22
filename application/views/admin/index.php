<?php $this->load->view('admin/includes/admin-header'); ?>


	<h2>Stats</h2>
<table class="table table-bordered">
	<thead>
		<th>Description</th>
		<th>Total</th>
		<th>Last 24hrs</th>
		<th>Last 7days</th>
		<th>Last month</th>
		<th>Last 3 months</th>
	</thead>
	<?php foreach($stats as $stat): ?>
	<tr>
		<td><?php echo $stat->description; ?></td>
		<td><?php echo $stat->value1; ?></td>
		<td><?php echo $stat->value2; ?></td>
		<td><?php echo $stat->value3; ?></td>
		<td><?php echo $stat->value4; ?></td>
		<td><?php echo $stat->value5; ?></td>
	</tr>
	<?php endforeach; ?>
</table>


<?php $this->load->view('admin/includes/admin-footer'); ?>