<?php $this->load->view('includes/header'); ?>

<?php 
$key_partners = json_decode($canvas[0]->key_partners);
$key_activities = json_decode($canvas[0]->key_activities);
$key_resources = json_decode($canvas[0]->key_resources);
$value_propositions = json_decode($canvas[0]->value_propositions);
$customer_relationships = json_decode($canvas[0]->customer_relationships);
$channels = json_decode($canvas[0]->channels);
$customer_segments = json_decode($canvas[0]->customer_segments);
$cost_structure = json_decode($canvas[0]->cost_structure);
$revenue_streams = json_decode($canvas[0]->revenue_streams);


?>

<div id="content" class="bg-white-wrapper">
<div id="business-model-canvas">
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
			<td class="area" height="66%" id="key_partners" rowspan="2" width="20%">
				<table>
					<tr>
						<td>
							<h4>Key partners</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="key-partners">
								<?php if(count($key_partners) > 0): ?>
								<?php foreach($key_partners as $partner): ?>
								<li><input type="text" name="key_partners[]" value="<?php echo $partner; ?>"> <a href="#" class="delete_key_partners"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="key_partners[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-key-partners" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-more-key-partners">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			<td class="area" height="33%" id="key_activities" width="20%">
				<table>
					<tr>
						<td>
							<h4>Key activities</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="key-activities">
								<?php if(count($key_activities) > 0): ?>
								<?php foreach($key_activities as $activity): ?>
								<li><input type="text" name="key_activities[]" value="<?php echo $activity; ?>"> <a href="#" class="delete_key_activities"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="key_activities[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-key-activities" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-more-key-activities">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			<td class="area" colspan="2" height="66%" id="value_propositions" rowspan="2" width="20%">
				<table>
					<tr>
						<td>
							<h4>Value propositions</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="value-propositions">
								<?php if(count($value_propositions) > 0): ?>
								<?php foreach($value_propositions as $value): ?>
								<li><input type="text" name="value_propositions[]" value="<?php echo $value; ?>"> <a href="#" class="delete_value_propositions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="value_propositions[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-value-propositions" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-more-value-propositions">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			<td class="area" height="33%" id="customer_relationships" width="20%">
				<table>
					<tr>
						<td>
							<h4>Customer relationships</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="customer-relationships">
								<?php if(count($customer_relationships) > 0): ?>
								<?php foreach($customer_relationships as $customer): ?>
								<li><input type="text" name="customer_relationships[]" value="<?php echo $customer; ?>"> <a href="#" class="delete_customer_relationships"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="customer_relationships[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-customer-relationships" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-more-customer-relationships">Save</a> -->
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
								<?php foreach($customer_segments as $customer): ?>
								<li><input type="text" name="customer_segments[]" value="<?php echo $customer; ?>"> <a href="#" class="delete_customer_segments"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="customer_segments[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-customer-segments" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-customer-segments">Save</a> -->
						</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr>
			<td class="area" height="33%" id="key_resources" width="20%">
				<table>
					<tr>
						<td>
							<h4>Key resources</h4>
							<div class="desc">Short description here</div>
						</td>
					</tr>
					<tr class="items">
						<td>
							<ul id="key-resources">
								<?php if(count($key_resources) > 0): ?>
								<?php foreach($key_resources as $resource): ?>
								<li><input type="text" name="key_resources[]" value="<?php echo $resource; ?>"> <a href="#" class="delete_key_resources"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="key_resources[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-key-resources" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
							<!-- <a href="#" class="btn btn-success btn-sm" id="save-more-key-resources">Save</a> -->
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
							<a href="#" id="add-more-channels" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
								<?php if(count($cost_structure) > 0): ?>
								<?php foreach($cost_structure as $structure): ?>
								<li><input type="text" name="cost_structure[]" value="<?php echo $structure; ?>"> <a href="#" class="delete_cost_structure"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="cost_structure[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-cost-structure" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
								<?php foreach($revenue_streams as $streams): ?>
								<li><input type="text" name="revenue_streams[]" value="<?php echo $streams; ?>"> <a href="#" class="delete_revenue_streams"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="revenue_streams[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-more-revenue-streams" data-toggle="tooltip" data-placement="bottom" title="Add more item"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
					<input type="hidden" name="table" value="canvas_business_model">
					<input type="hidden" name="canvas_id" value="<?php echo $canvas[0]->id; ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo form_close(); ?>
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
			  url: "<?php echo site_url('canvases/update_canvas_name/business'); ?>",
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


	// table names
	var business_model = 'canvas_business_model';

	// KEY PARTNERS
	$("#key_partners").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="key_partners[]" placeholder="Click to add an item"> <a href="#" class="delete_key_partners"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-partners").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_key_partners").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-more-key-partners").click(function(){
		var html_li = '<li><input type="text" name="key_partners[]" placeholder="Click to add an item"> <a href="#" class="delete_key_partners"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-partners").append(html_li);

		// delete
		$("a.delete_key_partners").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	$("a.delete_key_partners").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>
	

	// KEY ACTIVITIES
	$("#key_activities").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="key_activities[]" placeholder="Click to add an item"> <a href="#" class="delete_key_activities"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-activities").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_key_activities").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-key-activities").click(function(){
		var html_li = '<li><input type="text" name="key_activities[]" placeholder="Click to add an item"> <a href="#" class="delete_key_activities"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-activities").append(html_li);

		// delete
		$("a.delete_key_activities").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_key_activities").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});

	// END -------------------------------------------- >>>>>

	// KEY RESOURCES
	$("#key_resources").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="key_resources[]" placeholder="Click to add an item"> <a href="#" class="delete_key_resources"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-resources").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_key_resources").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-key-resources").click(function(){
		var html_li = '<li><input type="text" name="key_resources[]" placeholder="Click to add an item"> <a href="#" class="delete_key_resources"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#key-resources").append(html_li);

		// delete
		$("a.delete_key_resources").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_key_resources").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

	// VALUE PROPOSITIONS
	$("#value_propositions").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="value_propositions[]" placeholder="Click to add an item"> <a href="#" class="delete_value_propositions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#value-propositions").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_value_propositions").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-value-propositions").click(function(){
		var html_li = '<li><input type="text" name="value_propositions[]" placeholder="Click to add an item"> <a href="#" class="delete_value_propositions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#value-propositions").append(html_li);

		// delete
		$("a.delete_value_propositions").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_value_propositions").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

	// CUSTOMER RELATIONSHIPS
	$("#customer_relationships").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="customer_relationships[]" placeholder="Click to add an item"> <a href="#" class="delete_customer_relationships"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customer-relationships").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_customer_relationships").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-customer-relationships").click(function(){
		var html_li = '<li><input type="text" name="customer_relationships[]" placeholder="Click to add an item"> <a href="#" class="delete_customer_relationships"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customer-relationships").append(html_li);

		// delete
		$("a.delete_customer_relationships").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_customer_relationships").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

	// CHANNELS
	$("#channels").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="channels[]" placeholder="Click to add an item"> <a href="#" class="delete_channels"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#channels").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_channels").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-channels").click(function(){
		var html_li = '<li><input type="text" name="channels[]" placeholder="Click to add an item"> <a href="#" class="delete_channels"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#channels").append(html_li);

		// delete
		$("a.delete_channels").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_channels").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

	// CUSTOMER SEGMENTS
	$("#customer_segments").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="customer_segments[]" placeholder="Click to add an item"> <a href="#" class="delete_customer_segments"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customer-segments").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_customer_segments").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-customer-segments").click(function(){
		var html_li = '<li><input type="text" name="customer_segments[]" placeholder="Click to add an item"> <a href="#" class="delete_customer_segments"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#customer-segments").append(html_li);

		// delete
		$("a.delete_customer_segments").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_customer_segments").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

	// COST STRUCTURE
	$("#cost_structure").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="cost_structure[]" placeholder="Click to add an item"> <a href="#" class="delete_cost_structure"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#cost-structure").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_cost_structure").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-cost-structure").click(function(){
		var html_li = '<li><input type="text" name="cost_structure[]" placeholder="Click to add an item"> <a href="#" class="delete_cost_structure"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#cost-structure").append(html_li);

		// delete
		$("a.delete_cost_structure").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_cost_structure").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

	// REVENUE STREAMS
	$("#revenue_streams").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="revenue_streams[]" placeholder="Click to add an item"> <a href="#" class="delete_revenue_streams"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#revenue-streams").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_revenue_streams").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})

		return false;
	  }
	});

	$("#add-more-revenue-streams").click(function(){
		var html_li = '<li><input type="text" name="revenue_streams[]" placeholder="Click to add an item"> <a href="#" class="delete_revenue_streams"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#revenue-streams").append(html_li);

		// delete
		$("a.delete_revenue_streams").each(function(){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		})
	});

	$("a.delete_revenue_streams").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// END -------------------------------------------- >>>>>

})

</script>

<?php $this->load->view('includes/footer'); ?>