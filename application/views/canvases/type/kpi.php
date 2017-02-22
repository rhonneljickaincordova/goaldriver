<?php 
$this->load->view('includes/header'); 

$sales = json_decode($canvas[0]->sales);
$operations = json_decode($canvas[0]->operations);
$customers = json_decode($canvas[0]->customers);
$marketing = json_decode($canvas[0]->marketing);
$people = json_decode($canvas[0]->people);


?>

<div id="content" class="bg-white-wrapper">
	<?php echo form_open('canvases/save_canvas_items'); ?>
	
<div class="pull-left">
	<h1><a href="<?php echo site_url('canvases'); ?>">Canvases</a> <i class="fa fa-angle-double-right" aria-hidden="true"></i> <span><?php echo $canvas[0]->name; ?></span> <a data-toggle="modal" data-target="#editCanvasTitle" href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></h1><br>
</div>
<div class="pull-right">
	<!-- Single button -->
		<div class="btn-group">
			<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    <i class="fa fa-share" aria-hidden="true"></i> Share (<?php echo count($shared_users); ?>) <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pull-right">
				<?php 
				foreach ($users as $user): 
					$is_checked = '';
					foreach($shared_users as $su){
						if($su->user_id == $user->user_id)
						{
							$is_checked = 'checked';
						}
					}
				if($owner != $user->user_id):
				?>
				<li><input type="checkbox" id="<?php echo $user->user_id; ?>" <?php echo $is_checked; ?> name="share_to[]" value="<?php echo $user->user_id; ?>"><label for="<?php echo $user->user_id; ?>"><?php echo $user->first_name.' '.$user->last_name; ?></label></li>	
				<?php endif; endforeach; ?>
				
			</ul>
		</div>
</div>


	<table class="canvas" height="100%">
		<tr>
			<td class="area">
				<h4>Sales</h4>
				<div class="desc">Short description here</div>

				<ul id="sales">
					<?php if(count($sales) > 0): ?>
					<?php foreach($sales as $sale): ?>
					<li><input type="text" name="sales[]" value="<?php echo $sale; ?>"> <a href="#" class="delete_sales"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
					<?php endforeach; ?>
					<?php else: ?>
					<li><input type="text" name="sales[]" placeholder="Click to add an item"></li>
					<?php endif; ?>
				</ul>

				<a href="#" id="add-more-sales" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
			</td>
			<td></td>
			<td class="area">
				<h4>Operations</h4>
				<div class="desc">Short description here</div>

				<ul id="operations">
					<?php if(count($operations) > 0): ?>
					<?php foreach($operations as $operation): ?>
					<li><input type="text" name="operations[]" value="<?php echo $operation; ?>"> <a href="#" class="delete_operations"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
					<?php endforeach; ?>
					<?php else: ?>
					<li><input type="text" name="operations[]" placeholder="Click to add an item"></li>
					<?php endif; ?>
				</ul>

				<a href="#" id="add-more-operations" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="area">
				<h4>Customers</h4>
				<div class="desc">Short description here</div>

				<ul id="customers">
					<?php if(count($customers) > 0): ?>
					<?php foreach($customers as $customer): ?>
					<li><input type="text" name="customers[]" value="<?php echo $customer; ?>"> <a href="#" class="delete_customers"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
					<?php endforeach; ?>
					<?php else: ?>
					<li><input type="text" name="customers[]" placeholder="Click to add an item"></li>
					<?php endif; ?>
				</ul>

				<a href="#" id="add-more-customers" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
			</td>
			<td></td>
		</tr>
		<tr>
			<td class="area">
				<h4>Marketing</h4>
				<div class="desc">Short description here</div>

				<ul id="marketing">
					<?php if(count($marketing) > 0): ?>
					<?php foreach($marketing as $market): ?>
					<li><input type="text" name="marketing[]" value="<?php echo $market; ?>"> <a href="#" class="delete_marketing"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
					<?php endforeach; ?>
					<?php else: ?>
					<li><input type="text" name="marketing[]" placeholder="Click to add an item"></li>
					<?php endif; ?>
				</ul>

				<a href="#" id="add-more-marketing" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
			</td>
			<td></td>
			<td class="area">
				<h4>People</h4>
				<div class="desc">Short description here</div>

				<ul id="people">
					<?php if(count($people) > 0): ?>
					<?php foreach($people as $p): ?>
					<li><input type="text" name="people[]" value="<?php echo $p; ?>"> <a href="#" class="delete_people"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
					<?php endforeach; ?>
					<?php else: ?>
					<li><input type="text" name="people[]" placeholder="Click to add an item"></li>
					<?php endif; ?>
				</ul>

				<a href="#" id="add-more-people" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				<input type="submit" class="btn btn-success" name="save_changes" value="Save all changes">
				<input type="hidden" name="table" value="canvas_kpi">
				<input type="hidden" name="canvas_id" value="<?php echo $canvas[0]->id; ?>">
			</td>
		</tr>
	</table>

<?php echo form_close(); ?>

</div><!-- Edit title -->

<div class="modal fade" id="editCanvasTitle" tabindex="-1" role="dialog" aria-labelledby="editCanvasTitleLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit name</h4>
      </div>
      <div class="modal-body">
      <label>Name</label>
      <input type="text" class="form-control" value="<?php echo $canvas[0]->name; ?>">
  	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="save-canvas-name" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

$(function(){
	// Edit canvas name
	$("#save-canvas-name").click(function(){
		var new_name = $("#editCanvasTitle").find('input[type="text"]').val();
		console.log(new_name);

		if($.trim(new_name) != '')
		{
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('canvases/update_canvas_name/kpi'); ?>",
			  data: {id:<?php echo $canvas[0]->id; ?>, name: new_name, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);
			   	if(data.response == 'success'){
					$("#content h1 span").text(new_name);
					$("#editCanvasTitle").modal('hide');
				} 
			});
		}
		else{
			$.alert({
			    title: 'Error!',
			    content: 'Canvas name required.',
			});
		}
	});


	// SALES
	$("#sales").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="sales[]" placeholder="Click to add an item"> <a href="#" class="delete_sales"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#sales").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_sales").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-sales").click(function(){
		var html_li = '<li><input type="text" name="sales[]" placeholder="Click to add an item"> <a href="#" class="delete_sales"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#sales").append(html_li);

		// delete
		$("a.delete_sales").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_sales").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	// OPERATIONS
	$("#operations").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="operations[]" placeholder="Click to add an item"> <a href="#" class="delete_operations"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#operations").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_operations").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-operations").click(function(){
		var html_li = '<li><input type="text" name="operations[]" placeholder="Click to add an item"> <a href="#" class="delete_operations"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#operations").append(html_li);

		// delete
		$("a.delete_operations").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
		$("a.delete_operations").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	// END ------------------------------------>>>>

	// CUSTOMERS
	$("#customers").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="customers[]" placeholder="Click to add an item"> <a href="#" class="delete_customers"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customers").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_customers").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-customers").click(function(){
		var html_li = '<li><input type="text" name="customers[]" placeholder="Click to add an item"> <a href="#" class="delete_customers"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customers").append(html_li);

		// delete
		$("a.delete_customers").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
		$("a.delete_customers").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	// END ------------------------------------>>>>

	// MARKETING
	$("#marketing").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="marketing[]" placeholder="Click to add an item"> <a href="#" class="delete_marketing"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#marketing").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_marketing").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-marketing").click(function(){
		var html_li = '<li><input type="text" name="marketing[]" placeholder="Click to add an item"> <a href="#" class="delete_marketing"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#marketing").append(html_li);

		// delete
		$("a.delete_marketing").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_marketing").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	// PEOPLE
	$("#people").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="people[]" placeholder="Click to add an item"> <a href="#" class="delete_people"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#people").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_people").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-people").click(function(){
		var html_li = '<li><input type="text" name="people[]" placeholder="Click to add an item"> <a href="#" class="delete_people"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#people").append(html_li);

		// delete
		$("a.delete_people").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_people").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

})

</script>
<?php $this->load->view('includes/footer'); ?>