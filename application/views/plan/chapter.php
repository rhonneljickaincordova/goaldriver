<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('plan/includes/menu'); ?>
<?php $this->load->view('includes/section-sidebar'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />


<script type="text/javascript">
	$(document).ready(function(){
		var rights = $('.check_rights').val();

		if(rights == "disabled")
		{
			// $('#plan-menu li a').hide();
			$('#plan-menu li:nth-child(2), #plan-menu li:nth-child(3)').remove();
			$("#content-section a.edit-section-title").remove();
			$("#content-section .plan-section-content div").removeClass("editable").removeAttr('data-toggle');
			$(".instruction-edit, .example-edit").remove();

			$(".chapter-option").remove();

			$(".comment-box").each(function(){
				$(this).find('textarea').remove();
				$(this).find('input[type=submit]').remove();
			});

			$("#sections li .pull-left").remove();
			
			$("#change-section").remove();

			$("#sections .button-group").remove();
			$('a[data-toggle=modal]').hide();
			$('i[data-toggle=modal]').hide();
			$('i[data-toggle=tooltip]').hide();
			// $('div > a').hide();
		}
	});
</script>

<div id="plan-content-section">
<?php 
if(isset($section_info)){
	$this->load->view('includes/section'); 	
}
else{
	$this->load->view('includes/chapter'); 
}
?>
</div>

<?php 
$data['chapter_id'] = $chapter_id;
$data['plan_id'] = $this->session->userdata('plan_id');
$data['section_id'] = $section_id;

$this->load->view('includes/section-footer', $data); ?>
<?php $this->load->view('includes/footer'); ?>