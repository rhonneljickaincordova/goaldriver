<?php 
$this->load->view('includes/header'); 

$problems = json_decode($canvas[0]->problem);
$solutions = json_decode($canvas[0]->solution);
$key_metrics = json_decode($canvas[0]->key_metrics);
$value_propositions = json_decode($canvas[0]->unique_value_propositions);
$unfair_advantages = json_decode($canvas[0]->unfair_advantage);
$channels = json_decode($canvas[0]->channels);
$customer_segments = json_decode($canvas[0]->customer_segments);
$cost_structures = json_decode($canvas[0]->cost_structure);
$revenue_streams = json_decode($canvas[0]->revenue_streams);

?>

<div id="content" class="bg-white-wrapper">
<div id="lean-canvas">
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
		<tbody>
			<tr>
			<td class="area" height="66%" id="problem" rowspan="2" width="20%">
				<table>
					<tr>
						<td>
							<h4>Problem</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="problem">
								<?php if(count($problems) > 0): ?>
								<?php foreach($problems as $problem): ?>
								<li><input type="text" name="problem[]" value="<?php echo $problem; ?>"> <a href="#" class="delete_problem"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="problem[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-problem" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-problem">Save</a> -->
						</td>
					</tr>
					
				</table>
			</td>
			<td class="area" height="33%" id="solution" width="20%">
				<table>
					<tr>
						<td>
							<h4>Solution</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="solution">
								<?php if(count($solutions) > 0): ?>
								<?php foreach($solutions as $solution): ?>
								<li><input type="text" name="solution[]" value="<?php echo $solution; ?>"> <a href="#" class="delete_solution"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="solution[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-solution" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-solution">Save</a> -->
						</td>
					</tr>
					
				</table>
			</td>
			<td class="area" colspan="2" height="66%" id="unique_value_propositions" rowspan="2" width="20%">
				<table>
					<tr>
						<td>
							<h4>Unique value propositions</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="unique-value-propositions">
								<?php if(count($value_propositions) > 0): ?>
								<?php foreach($value_propositions as $value_proposition): ?>
								<li><input type="text" name="unique_value_propositions[]" value="<?php echo $value_proposition; ?>"> <a href="#" class="delete_unique_value_propositions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="unique_value_propositions[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-unique-value-propositions" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-unique-value-propositions">Save</a> -->
						</td>
					</tr>
				</table>
				
			</td>
			<td class="area" height="33%" id="unfair_advantage" width="20%">
				<table>
					<tr>
						<td>
							<h4>Unfair advantage</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="unfair-advantage">
								<?php if(count($unfair_advantages) > 0): ?>
								<?php foreach($unfair_advantages as $unfair_advantage): ?>
								<li><input type="text" name="unfair_advantage[]" value="<?php echo $unfair_advantage; ?>"> <a href="#" class="delete_unfair_advantage"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="unfair_advantage[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-unfair-advantage" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-unfair-advantage">Save</a> -->
						</td>
					</tr>
				</table>
				
			</td>
			<td class="area" height="66%" id="customer_segments" rowspan="2" width="20%">
				<table>
					<tr>
						<td>
							<h4>Customer segments</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="customer-segments">
								<?php if(count($customer_segments) > 0): ?>
								<?php foreach($customer_segments as $customer_segment): ?>
								<li><input type="text" name="customer_segments[]" value="<?php echo $customer_segment; ?>"> <a href="#" class="delete_customer_segments"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="customer_segments[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-customer-segments" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-customer-segments">Save</a> -->
						</td>
					</tr>
				</table>
				
			</td>
			</tr>
			<tr>
			<td class="area" height="33%" id="key_metrics" width="20%">
				<table>
					<tr>
						<td>
							<h4>Key metrics</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="key-metrics">
								<?php if(count($key_metrics) > 0): ?>
								<?php foreach($key_metrics as $key_metric): ?>
								<li><input type="text" name="key_metrics[]" value="<?php echo $key_metric; ?>"> <a href="#" class="delete_key_metrics"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="key_metrics[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-key-metrics" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-key-metrics">Save</a> -->
						</td>
					</tr>
				</table>
				
			</td>
			<td class="area" height="33%" id="channels" width="20%">
				<table>
					<tr>
						<td>
							<h4>Channels</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="channels">
								<?php if(count($channels) > 0): ?>
								<?php foreach($channels as $channel): ?>
								<li><input type="text" name="channels[]" value="<?php echo $channel; ?>"> <a href="#" class="delete_channels"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="channels[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-channels" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-channels">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr>
			<td class="area" colspan="3" height="33%" id="cost_structure" width="50%">
				<table>
					<tr>
						<td>
							<h4>Cost structure</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="cost-structure">
								<?php if(count($cost_structures) > 0): ?>
								<?php foreach($cost_structures as $cost_structure): ?>
								<li><input type="text" name="cost_structure[]" value="<?php echo $cost_structure; ?>"> <a href="#" class="delete_cost_structure"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="cost_structure[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-cost-structure" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-cost-structure">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			<td class="area" colspan="3" height="33%" id="revenue_streams" width="50%">
				<table>
					<tr>
						<td>
							<h4>Revenue streams</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="revenue-streams">
								<?php if(count($revenue_streams) > 0): ?>
								<?php foreach($revenue_streams as $revenue_stream): ?>
								<li><input type="text" name="revenue_streams[]" value="<?php echo $revenue_stream; ?>"> <a href="#" class="delete_revenue_streams"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="revenue_streams[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>

							<a href="#" id="add-more-revenue-streams" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-revenue-streams">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
					<input type="submit" class="btn btn-success" name="save_changes" value="Save all changes">
					<input type="hidden" name="table" value="canvas_lean">
					<input type="hidden" name="canvas_id" value="<?php echo $canvas[0]->id; ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo form_close(); ?>
</div>

</div>

</div>

<!-- Edit title -->
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
			  url: "<?php echo site_url('canvases/update_canvas_name/lean'); ?>",
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
	})

	// PROBLEM
	$("#problem").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="problem[]" placeholder="Click to add an item"> <a href="#" class="delete_problem"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#problem").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_problem").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-problem").click(function(){
		var html_li = '<li><input type="text" name="problem[]" placeholder="Click to add an item"> <a href="#" class="delete_problem"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#problem").append(html_li);

		// delete
		$("a.delete_problem").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_problem").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	//SOLUTION
	$("#solution").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="solution[]" placeholder="Click to add an item"> <a href="#" class="delete_solution"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#solution").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_solution").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-solution").click(function(){
		var html_li = '<li><input type="text" name="solution[]" placeholder="Click to add an item"> <a href="#" class="delete_solution"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#solution").append(html_li);

		// delete
		$("a.delete_solution").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_solution").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	// KEY METRICS
	$("#key_metrics").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="key_metrics[]" placeholder="Click to add an item"> <a href="#" class="delete_key_metrics"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-metrics").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_key_metrics").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-key-metrics").click(function(){
		var html_li = '<li><input type="text" name="key_metrics[]" placeholder="Click to add an item"> <a href="#" class="delete_key_metrics"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-metrics").append(html_li);

		// delete
		$("a.delete_key_metrics").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_key_metrics").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	// UNIQUE VALUE PROPOSITIONS
	$("#unique_value_propositions").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="unique_value_propositions[]" placeholder="Click to add an item"> <a href="#" class="delete_unique_value_propositions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#unique-value-propositions").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_unique_value_propositions").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
		return false;
	  }
	});

	$("#add-more-unique-value-propositions").click(function(){
		var html_li = '<li><input type="text" name="unique_value_propositions[]" placeholder="Click to add an item"> <a href="#" class="delete_unique_value_propositions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#unique-value-propositions").append(html_li);

		// delete
		$("a.delete_unique_value_propositions").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_unique_value_propositions").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>


	// UNFAIR ADVANTAGE
	$("#unfair_advantage").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="unfair_advantage[]" placeholder="Click to add an item"> <a href="#" class="delete_unfair_advantage"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#unfair-advantage").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_unfair_advantage").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-unfair-advantage").click(function(){
		var html_li = '<li><input type="text" name="unfair_advantage[]" placeholder="Click to add an item"> <a href="#" class="delete_unfair_advantage"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#unfair-advantage").append(html_li);

		// delete
		$("a.delete_unfair_advantage").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_unfair_advantage").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>


	// CHANNELS
	$("#channels").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="channels[]" placeholder="Click to add an item"> <a href="#" class="delete_channels"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#channels").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_channels").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});


	$("#add-more-channels").click(function(){
		var html_li = '<li><input type="text" name="channels[]" placeholder="Click to add an item"> <a href="#" class="delete_channels"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#channels").append(html_li);

		// delete
		$("a.delete_channels").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_channels").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	// CUSTOMER SEGMENTS
	$("#customer_segments").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="customer_segments[]" placeholder="Click to add an item"> <a href="#" class="delete_customer_segments"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customer-segments").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_customer_segments").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-customer-segments").click(function(){
		var html_li = '<li><input type="text" name="customer_segments[]" placeholder="Click to add an item"> <a href="#" class="delete_customer_segments"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customer-segments").append(html_li);

		// delete
		$("a.delete_customer_segments").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_customer_segments").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>

	// COST STRUCTURE
	$("#cost_structure").keydown(function (e) {
	  if (e.keyCode == 13) {
	   var html_li = '<li><input type="text" name="cost_structure[]" placeholder="Click to add an item"> <a href="#" class="delete_cost_structure"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#cost-structure").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_cost_structure").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-cost-structure").click(function(){
		var html_li = '<li><input type="text" name="cost_structure[]" placeholder="Click to add an item"> <a href="#" class="delete_cost_structure"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#cost-structure").append(html_li);

		// delete
		$("a.delete_cost_structure").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_cost_structure").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>


	// REVENUE STREAMS
	$("#revenue_streams").keydown(function (e) {
	  if (e.keyCode == 13) {
	   var html_li = '<li><input type="text" name="revenue_streams[]" placeholder="Click to add an item"> <a href="#" class="delete_revenue_streams"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#revenue-streams").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_revenue_streams").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
		return false;
	  }
	});

	$("#add-more-revenue-streams").click(function(){
		var html_li = '<li><input type="text" name="revenue_streams[]" placeholder="Click to add an item"> <a href="#" class="delete_revenue_streams"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#revenue-streams").append(html_li);

		// delete
		$("a.delete_revenue_streams").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_revenue_streams").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END ------------------------------------>>>>
})



</script>

<?php $this->load->view('includes/footer'); ?>