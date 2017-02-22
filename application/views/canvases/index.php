<?php $this->load->view('includes/header'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />
<script type="text/javascript">
    $(document).ready(function(){
        var rights = $('.check_rights').val();

        if(rights == "disabled")
        {
            $('button[data-toggle=modal]').remove();
			$('a[data-toggle=modal]').prop("disabled", true);
			$('input[type=button]').prop("disabled", true);
			$('button[type=button]').prop("disabled", true);
			$('a.delete').remove();
			$('a').attr('disabled', 'disabled');
			$('td > a').removeAttr('href');
        }
    
    });
</script>

<div id="content" class="bg-white-wrapper">
<a class="btn btn-primary btn-sm" href="<?php echo site_url('canvases/create'); ?>"><i class="fa fa-plus" aria-hidden="true"></i> New canvas</a>

<?php 
if(count($canvases)): ?>
<table class="table" id="canvas-lists">
	<thead>
		<th>Name</th>
		<th>Type</th>
		<th>Created by</th>
		<th>Date created</th>
		<th>Last update</th>
		<th>Last update by</th>
		<th></th>
	</thead>
<?php foreach ($canvases as $canvas): 
	switch ($canvas->type) {
		case 'canvas_business_model':
			$type = 'business';
			$type_text = 'Business Model';
			break;
		
		case 'canvas_lean':
			$type = 'lean';
			$type_text = 'Lean';
			break;

		case 'canvas_personal_goals':
			$type = 'personal';
			$type_text = 'Personal Goals';
			break;

		case 'canvas_kpi':
			$type = 'kpi';
			$type_text = 'KPI';
			break;
	}

?>
	<tr>
		<td><a href="<?php echo site_url('canvases/edit_canvas/'.$type.'/'.encrypt($canvas->id)); ?>"><?php echo $canvas->name; ?></a></td>
		<td><?php echo $type_text; ?></td>
		<td><?php echo user_info('first_name', $canvas->entered_by).' '.user_info('last_name', $canvas->entered_by); ?></td>
		<td><?php echo gd_date($canvas->entered); ?></td>
		<td><?php echo gd_date($canvas->updated); ?></td>
		<td><?php echo user_info('first_name', $canvas->updated_by).' '.user_info('last_name', $canvas->updated_by); ?></td>
		<td>
			<a href="<?php echo site_url('canvases/edit_canvas/'.$type.'/'.encrypt($canvas->id)); ?>" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
			<a href="<?php echo site_url('canvases/delete_canvas/'.$type.'/'.encrypt($canvas->id)); ?>" class="delete" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

</div>

<script type="text/javascript">

$(function(){
	$('a.delete').confirm({
		title: 'Delete?',
    	content: 'Are you sure you want to delete this canvas?',
    	confirmButtonClass: 'btn-danger',
    	cancelButtonClass: 'btn-default',
    	confirmButton: 'Delete',
    	cancelButton: 'Cancel'
	});

	$('#canvas-lists').dataTable({
		columnDefs: [ // remove the last column from ordering
		   { orderable: false, targets: -1 }
		],
		"sDom": 'p', 

	});
})

</script>

<?php $this->load->view('includes/footer'); ?>