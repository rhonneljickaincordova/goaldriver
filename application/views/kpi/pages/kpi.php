<?php
$hide = ($kpi_permission_name == "readwrite") ? "" : "hide";
?>
<div class="form-group col-sm-6 col-md-6">
	<div class="btn-toolbar">
		<button type="button" class="btn btn-primary <?php echo $hide; ?>" id="new_goal_btn"><i class="fa fa-plus"></i> New KPI</button>
	</div>
</div>

<table class="table table-hover dataTable no-footer" id="kpi_table" role="grid">
	<thead>
		<tr>
			<th>KPI</th>
			<th>KPI id</th>
			<th>Icon</th>
			<th>Description</th>
			<th>Frequency</th>
			<th>Format</th>
			<th>Best Direction</th>
			<th>Target</th>
			<th>Rag 1</th>
			<th>Rag 2</th>
			<th>Rag 3</th>
			<th>Rag 4</th>
			<th>Aggregate</th>
			<th>KPI Format Id</th>
			<th>Assigned</th>
			<th>islocked</th>
			<th></th>
			<th>order</th>
			<th>kpi_days</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if($kpis !== false){
		foreach($kpis as $kpi){
			?>
			<tr id="kpi_id-<?php echo $kpi->kpi_id; ?>">	
				<td><?php echo $kpi->name; ?> </td>
				<td><?php echo $kpi->kpi_id; ?></td>
				<td><?php echo $kpi->icon; ?> </td>
				<td><?php echo $kpi->description; ?> </td>
				<td><?php echo ucfirst($kpi->frequency); ?> </td>
				<td><?php echo $kpi->format; ?> </td>
				<td><?php echo $kpi->best_direction; ?> </td>
				<td><?php echo $kpi->target; ?> </td>
				<td><?php echo $kpi->rag_1; ?> </td>
				<td><?php echo $kpi->rag_2; ?> </td>
				<td><?php echo $kpi->rag_3; ?> </td>
				<td><?php echo $kpi->rag_4; ?> </td>
				<td><?php echo $kpi->agg_type; ?> </td>
				<td><?php echo $kpi->kpi_format_id; ?> </td>
				<td><?php echo $kpi->assignedUsers; ?> </td>
				<td><?php echo $kpi->islocked; ?> </td>
				<td></td>
				<td><?php echo $kpi->name; ?> </td>
				<td><?php echo implode(",", $kpi->kpi_days); ?> </td>
			</tr>
			<?php
		}	
		
	}
	?>
	</tbody>
</table>
