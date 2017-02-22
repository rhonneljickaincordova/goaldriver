<?php $this->load->view('includes/header'); ?>

<div id="content" class="bg-white-wrapper clearfix">
	<div class="col-sm-6">
		<?php echo form_open('canvases/create', array('class' => 'form-horizontal')); ?>
		<div class="form-group">
			<label class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="name" value="<?php echo set_value('name'); ?>">
				<small class="error"><?php echo form_error('name'); ?></small>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Type</label>

			<div class="col-sm-10">
			<?php 
			$type = array(
				'' => '--Select--',
				'canvas_business_model' => 'Business Model',
				'canvas_personal_goals' => 'Personal Goals',
				'canvas_lean' => 'Lean',
				'canvas_kpi' => 'KPI'
				);

			echo form_dropdown('type', $type, set_value('type'), 'class="form-control"'); ?>
			<small class="error"><?php echo form_error('type'); ?></small>
			</div>
		</div>	
		<div class="form-group">
			<div class="pull-right col-sm-10">
				<input type="submit" value="Create canvas" class="btn btn-primary btn-sm">
				<a href="<?php echo site_url('canvases'); ?>">Cancel</a>
			</div>
		</div>
	
		<?php echo form_close(); ?>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>