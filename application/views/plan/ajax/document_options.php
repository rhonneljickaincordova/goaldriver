<?php 
if(count($print)){
	$options = unserialize($print->print_options);
	if($options != NULL)
	{
		foreach ($options as $o => $v) {
			$$o = $v;
		}		
	}
	
}

?>
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" id="myModalLabel">Document Options</h4>
</div>
<div class="modal-body">
	<form id="document-setup">
	<div class="setting">
		<h4>Paper size</h4>
		<label><input type="radio" name="paper_size" <?php echo (isset($paper_size) AND $paper_size == 'letter') ? 'checked' : ''; ?> value="letter"> Letter (8.5 x 11 in)</label>
		<label><input type="radio" name="paper_size" <?php echo (isset($paper_size) AND $paper_size == 'a4') ? 'checked' : ''; ?> value="a4"> A4 (210mm x 297mm)</label>
	</div>
	<div class="setting">
		<h4>Line spacing</h4>
		<label><input type="radio" name="spacing" <?php echo (isset($spacing) AND $spacing == 1) ? 'checked' : ''; ?> value="1"> Single spaced</label>
		<label><input type="radio" name="spacing" <?php echo (isset($spacing) AND $spacing == 1.5) ? 'checked' : ''; ?> value="1.5"> 1.5 spacing</label>
		<label><input type="radio" name="spacing" <?php echo (isset($spacing) AND $spacing == 2) ? 'checked' : ''; ?> value="2"> Double spaced</label>
	</div>
	<div class="setting">
		<h4>Headers and footers</h4>
		<label><input type="checkbox" <?php echo (isset($is_plan_title) AND $is_plan_title == 1) ? 'checked' : ''; ?> name="is_plan_title" value="1"> Show the plan title on each page</label> <br>
		<label><input type="checkbox" <?php echo (isset($is_paging) AND $is_paging == 1) ? 'checked' : ''; ?> name="is_paging" value="1"> Show page numbers</label> <br>
			<div id="page-number-options">
				<label><input type="radio" <?php echo (isset($page) AND $page == '1') ? 'checked' : ''; ?> name="page" value="1"> 1</label>
				<label><input type="radio" <?php echo (isset($page) AND $page == 'p1') ? 'checked' : ''; ?> name="page" value="p1"> Page 1</label>
				<label><input type="radio" <?php echo (isset($page) AND $page == '1-10') ? 'checked' : ''; ?> name="page" value="1-10"> 1 of 10</label>
				<label><input type="radio" <?php echo (isset($page) AND $page == 'p1-p10') ? 'checked' : ''; ?> name="page" value="p1-p10"> Page 1 of 10 </label><br>
			</div>
		<label><input type="checkbox" <?php echo (isset($is_confidential_msg) AND $is_confidential_msg == 1) ? 'checked' : ''; ?> name="is_confidential_msg" value="1"> Show a confidentiality message on each page</label> <br>	
		<textarea name="confidentiality_msg" class="form-control"><?php echo isset($confidentiality_msg) ? $confidentiality_msg : ''; ?></textarea>
	</div>
	<div class="setting">
		<h4>Other options</h4>
		<label><input type="checkbox" <?php echo (isset($is_toc) AND $is_toc == 1) ? 'checked' : ''; ?> name="is_toc" value="1"> Include table of contents</label> <br>
	</div>
	</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<button type="button" id="save-options" class="btn btn-primary">Save changes</button>
</div>

<script type="text/javascript">

$(function(){
	<?php 
	// paging on/ff
	if(isset($is_paging) AND $is_paging == 1): ?>
		$("#page-number-options").show();
	<?php else: ?>
		$("#page-number-options").hide();
	<?php endif; ?>

	<?php 
	// Confidentiality message on/ff
	if(isset($is_confidential_msg) AND $is_confidential_msg == 1): ?>
		$("textarea[name='confidentiality_msg']").show();
	<?php else: ?>
		$("textarea[name='confidentiality_msg']").hide();
	<?php endif; ?>

	$("input[name='is_paging']").change(function(){
		var is_paging = $(this).is(":checked");

		if(is_paging){
			$("#page-number-options").show();
		}
		else{
			$("#page-number-options").hide();
		}
	});

	$("input[name='is_confidential_msg']").change(function(){
		var is_confidential_msg = $(this).is(":checked");
		if(is_confidential_msg){
			$("textarea[name='confidentiality_msg']").show();
		}
		else{
			$("textarea[name='confidentiality_msg']").hide();
		}
	})

	$("#save-options").click(function(){
		var data = $("#document-setup").serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

		//console.log(data);

		$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/ajax_document_options_save'); ?>",
			  data: data
			}).done(function( msg ) {
				var data = JSON.parse(msg);
			   	if(data.action == 'success'){
					location.reload();
				} 
			});


	})
})

</script>