<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$sales->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<?php echo form_open('pitch/edit/sales_channel'); ?>

		<h3>Sales channel</h3>
		<p>How will you get your products and service to the customer? Are you going to sell thorough a Website, shop or on the road? Will you use third parties who could be agents or distributors?</p>
		<br />
		<p><strong class="heading">Do you want to describe your sales channels or make a short list?</strong></p>
		<label class="heading"><input type="radio" name="type" <?php echo is_null(@$sales->type) ? 'checked' : '';?>  <?php echo @$sales->type == 'desc' ? 'checked' : ''; ?> value="desc"> Description</label>
		<label class="heading"><input type="radio" name="type" <?php echo @$sales->type == 'list' ? 'checked' : ''; ?> value="list"> Short list</label>
		<br />

		<div id="description">
			<p><strong class="heading">Describe your sales channels:</strong></p>
			<textarea name="description" class="form-control editor"><?php echo @$sales->text_value; ?></textarea>
		</div>

		<div id="short-list" style="display:none">
			<p><strong class="heading">List your sales channels (in priority order):</strong></p>
			<ol>
				<?php 
				$lists = unserialize(@$sales->list_value);
				
				if(! empty($lists)):
					foreach ($lists as $k => $v):
				?>
					<li><input type="text" name="sales[<?php echo $k; ?>]" class="field-list" value="<?php echo $v; ?>" /></li>

				<?php endforeach; else: ?>
					<li><input type="text" name="sales[]" class="field-list" placeholder="Click here to enter a sales channel." /></li>
					<li><input type="text" name="sales[]" class="field-list"/></li>
					<li><input type="text" name="sales[]" class="field-list"/></li>
					<li><input type="text" name="sales[]" class="field-list"/></li>
					<li><input type="text" name="sales[]" class="field-list"/></li>
				<?php endif; ?>
			</ol>
		</div>
		<br>
		<input type="submit" class="btn btn-success btn-sm pull-right" name="submit" value="Save and continue">

		<?php echo form_close(); ?>
	</div>
</div>


<script type="text/javascript">
$(function(){
	$("input[name='type']").change(function(){
		//console.log($(this).val());

		var type = $(this).val();

		if(type == 'desc'){
			$("#description").show();
			$("#short-list").hide();
		}
		else{
			$("#description").hide();
			$("#short-list").show();
		}
	})

	<?php 
	if(! is_null(@$sales->type)):
	if(@$sales->type == 'desc'): ?>
	$("#description").show();
	$("#short-list").hide();
	<?php else: ?>
	$("#description").hide();
	$("#short-list").show();
	<?php endif; else: ?>
	$("#description").show();
	$("#short-list").hide();
	<?php endif; ?>


	// toggle hide/show of this section in the view
	$("#hide_show").change(function(){
		var id = $(this).val();
		if ($(this).is(':checked')) {
	        //alert('checked');

	        $.ajax({
			  method: "POST",
			  url: "<?php echo site_url('pitch/hide_view'); ?>",
			  data: {plan_id: id, table:'pitch_sales', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is hidden from the view');
				}

			});
	    }
	    else{
	    	$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('pitch/hide_view'); ?>",
			  data: {plan_id: id, table:'pitch_sales', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is visible from the view');
				}

			});
	    }
	});
})
</script>

<?php $this->load->view('includes/footer'); ?>