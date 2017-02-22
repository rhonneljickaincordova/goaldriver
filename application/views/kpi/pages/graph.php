<?php
$hide = ($kpi_permission_name == "readwrite") ? "" : "hide";
?>
<div class="form-group col-sm-6 col-md-6">
	<div class="btn-toolbar">
		<button type="button" class="btn btn-primary <?php echo $hide; ?>" id="new_graph_btn"><i class="fa fa-plus"></i> New Graph</button>
	</div>
</div>	
<table class="table table-hover dataTable no-footer" id="graph_table" role="grid">
	<thead>
		<tr>
			<th>graph id</th>
			<th>Name</th>
			<th>Description</th>
			<th>graph type id</th>
			<th>Type</th>
			<th>KPI Id</th>
			<th>KPI</th>
			<th>Entered by</th>
			<th>Entered</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>