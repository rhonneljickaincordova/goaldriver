<?php $this->load->view('includes/header'); ?>
<div ng-controller="kpi">	
	<h1>New KPI</h1>

	<?php echo form_open('kpi/new_kpi', array('class' => 'form-horizontal')); ?>

	<div class="form-group">
		<label for="name">Name</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="KPI name">
		</div>
	</div>

	<?php echo form_close(); ?>
</div>	
<?php $this->load->view('includes/footer'); ?>
