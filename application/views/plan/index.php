<?php $this->load->view('includes/header'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />


<script type="text/javascript">
	$(document).ready(function(){
		var rights = $('.check_rights').val();

		if(rights == "disabled")
		{
			$('#plan-menu li a').hide();
			$('a[data-toggle=modal]').hide();
		}
	});
</script>

<div class="container">
	<?php $this->load->view('plan/includes/menu'); ?>
	<?php $this->load->view('includes/section-sidebar'); ?>
	<div class="col-sm-9">
	Content here and there
	</div>
	<?php $this->load->view('includes/section-footer'); ?>
</div>

<?php $this->load->view('includes/footer'); ?>