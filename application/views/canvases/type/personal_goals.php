<?php 
$this->load->view('includes/header'); 


$why = json_decode($canvas[0]->why);
$health = json_decode($canvas[0]->health);
$relationships = json_decode($canvas[0]->relationships);
$balance = json_decode($canvas[0]->balance);
$money = json_decode($canvas[0]->money);
$activities = json_decode($canvas[0]->activities);
$values = json_decode($canvas[0]->values);

?>

<div id="content" class="bg-white-wrapper">

<div id="personal-goal-canvas">
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
	<table height="100%" class="canvas">
		<tr>
			<td colspan="3" class="area">
				<h4>Why</h4>
				<table>
					<tr>
						<td>
							<div class="desc">What is your why?</div>
						</td>
					</tr>
					<tr>
						<td>
							<ul id="why">
								<?php if(count($why) > 0): ?>
								<?php foreach($why as $w): ?>
								<li><input type="text" name="why[]" value="<?php echo $w; ?>"> <a href="#" class="delete_why"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="why[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-why" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="area">
				<h4>Health</h4>
				<table>
					<tr>
						<td>
							<div class="desc">What does my health look like?</div>
						</td>
					</tr>
					<tr>
						<td>
							<ul id="health">
								<?php if(count($health) > 0): ?>
								<?php foreach($health as $h): ?>
								<li><input type="text" name="health[]" value="<?php echo $h; ?>"> <a href="#" class="delete_health"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="health[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-health" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
				</table>
			</td>
			<td class="area">
				<h4>Relationships</h4>
				<table>
					<tr>
						<td>
							<div class="desc">What relationships do I have?</div>
						</td>
					</tr>
					<tr>
						<td>
							<ul id="relationships">
								<?php if(count($relationships) > 0): ?>
								<?php foreach($relationships as $relationship): ?>
								<li><input type="text" name="relationships[]" value="<?php echo $relationship; ?>"> <a href="#" class="delete_relationships"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="relationships[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-relationships" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
				</table>
			</td>
			<td class="area">
				<h4>Balance</h4>
				<table>
					<tr>
						<td>
							<div class="desc">How much time do I spend at work?</div>
						</td>
					</tr>
					<tr>
						<td>
							<ul id="balance">
								<?php if(count($balance) > 0): ?>
								<?php foreach($balance as $b): ?>
								<li><input type="text" name="balance[]" value="<?php echo $b; ?>"> <a href="#" class="delete_balance"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="balance[]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-balance" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="area">
				<h4>Money</h4>
				<table>
					<tr>
						<td>
							<div class="desc">What possesions do I own?</div>
							<ul id="possesions">
								<?php if(count($money[0]) > 0): ?>
								<?php foreach($money[0] as $m): ?>
								<li><input type="text" name="money[0][]" value="<?php echo $m; ?>"> <a href="#" class="delete_possesions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="money[0][]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-possesions" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
						<td>
							<div class="desc">What savings do I have?</div>
							<ul id="savings">
								<?php if(count($money[1]) > 0): ?>
								<?php foreach($money[1] as $m): ?>
								<li><input type="text" name="money[1][]" value="<?php echo $m; ?>"> <a href="#" class="delete_possesions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="money[1][]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-savings" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
					<tr>
						<td>
							<div class="desc">How much do I spend a year?</div>
							<ul id="spend">
								<?php if(count($money[2]) > 0): ?>
								<?php foreach($money[2] as $m): ?>
								<li><input type="text" name="money[2][]" value="<?php echo $m; ?>"> <a href="#" class="delete_possesions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="money[2][]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-spend" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
						<td>
							<div class="desc">How much do I gave away each year?</div>
							<ul id="gave-away">
								<?php if(count($money[3]) > 0): ?>
								<?php foreach($money[3] as $m): ?>
								<li><input type="text" name="money[3][]" value="<?php echo $m; ?>"> <a href="#" class="delete_possesions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="money[3][]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-gave-away" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
				</table>
			</td>
			<td class="area">
				<h4>Activities</h4>
				<table>
					<tr>
						<td>
							<div class="desc">What do I do at work?</div>
							<ul id="do-at-work">
								<?php if(count($activities[0]) > 0): ?>
								<?php foreach($activities[0] as $a): ?>
								<li><input type="text" name="activities[0][]" value="<?php echo $a; ?>"> <a href="#" class="delete_do_at_work"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="activities[0][]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-do-at-work" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
					<tr>
						<td>
							<div class="desc">What do I do outside work?</div>
							<ul id="do-outside-work">
								<?php if(count($activities[1]) > 0): ?>
								<?php foreach($activities[1] as $a): ?>
								<li><input type="text" name="activities[1][]" value="<?php echo $a; ?>"> <a href="#" class="delete_do_outside_work"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
								<?php endforeach; ?>
								<?php else: ?>
								<li><input type="text" name="activities[1][]" placeholder="Click to add an item"></li>
								<?php endif; ?>
							</ul>
							<a href="#" id="add-do-outside-work" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="area">
				<h4>Values</h4>
				<div class="desc">What are my personal values?</div>
				<ul id="values">
					<?php if(count($values) > 0): ?>
					<?php foreach($values as $value): ?>
					<li><input type="text" name="values[]" value="<?php echo $value; ?>"> <a href="#" class="delete_values"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
					<?php endforeach; ?>
					<?php else: ?>
					<li><input type="text" name="values[]" placeholder="Click to add an item"></li>
					<?php endif; ?>
				</ul>
				<a href="#" id="add-values" data-toggle="tooltip" data-placement="bottom" title="Add item"><i class="fa fa-plus" aria-hidden="true"></i></a>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
					<input type="submit" class="btn btn-success" name="save_changes" value="Save all changes">
					<input type="hidden" name="table" value="canvas_personal_goals">
					<input type="hidden" name="canvas_id" value="<?php echo $canvas[0]->id; ?>">
				</td>
			</tr>
	</table>
	<?php echo form_close();?>
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
			  url: "<?php echo site_url('canvases/update_canvas_name/personal'); ?>",
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


	// WHY
	$("#why").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="why[]" placeholder="Click to add an item"> <a href="#" class="delete_why"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#why").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_why").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-why").click(function(){
		var html_li = '<li><input type="text" name="why[]" placeholder="Click to add an item"> <a href="#" class="delete_why"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#why").append(html_li);

		// delete
		$("a.delete_why").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_why").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// HEALTH
	$("#health").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="health[]" placeholder="Click to add an item"> <a href="#" class="delete_health"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#health").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_health").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-health").click(function(){
		var html_li = '<li><input type="text" name="health[]" placeholder="Click to add an item"> <a href="#" class="delete_health"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#health").append(html_li);

		// delete
		$("a.delete_health").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_health").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// RELATIONSHIPS
	$("#relationships").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="relationships[]" placeholder="Click to add an item"> <a href="#" class="delete_relationships"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#relationships").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_relationships").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-relationships").click(function(){
		var html_li = '<li><input type="text" name="relationships[]" placeholder="Click to add an item"> <a href="#" class="delete_relationships"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#relationships").append(html_li);

		// delete
		$("a.delete_relationships").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_relationships").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// BALANCE
	$("#balance").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="balance[]" placeholder="Click to add an item"> <a href="#" class="delete_balance"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#balance").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_balance").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-balance").click(function(){
		var html_li = '<li><input type="text" name="balance[]" placeholder="Click to add an item"> <a href="#" class="delete_balance"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#balance").append(html_li);

		// delete
		$("a.delete_balance").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_balance").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// MONEY[POSSESIONS]
	$("#possesions").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="money[0][]" placeholder="Click to add an item"> <a href="#" class="delete_possesions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#possesions").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_possesions").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-possesions").click(function(){
		var html_li = '<li><input type="text" name="money[0][]" placeholder="Click to add an item"> <a href="#" class="delete_possesions"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#possesions").append(html_li);

		// delete
		$("a.delete_possesions").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_possesions").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// MONEY[SAVINGS]
	$("#savings").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="money[1][]" placeholder="Click to add an item"> <a href="#" class="delete_savings"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#savings").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_savings").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-savings").click(function(){
		var html_li = '<li><input type="text" name="money[1][]" placeholder="Click to add an item"> <a href="#" class="delete_savings"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#savings").append(html_li);

		// delete
		$("a.delete_savings").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_savings").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// MONEY[SPEND]
	$("#spend").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="money[2][]" placeholder="Click to add an item"> <a href="#" class="delete_spend"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#spend").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_spend").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-spend").click(function(){
		var html_li = '<li><input type="text" name="money[2][]" placeholder="Click to add an item"> <a href="#" class="delete_spend"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#spend").append(html_li);

		// delete
		$("a.delete_spend").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_spend").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// MONEY[GAVE AWAY]
	$("#gave-away").keydown(function (e) {
	  if (e.keyCode == 13) {
	    var html_li = '<li><input type="text" name="money[3][]" placeholder="Click to add an item"> <a href="#" class="delete_gave_away"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#gave-away").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_gave_away").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-gave-away").click(function(){
		var html_li = '<li><input type="text" name="money[3][]" placeholder="Click to add an item"> <a href="#" class="delete_gave_away"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#gave-away").append(html_li);

		// delete
		$("a.delete_gave_away").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_gave_away").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>


	// ACTIVITIES[DO AT WORK]
	$("#do-at-work").keydown(function (e) {
	  if (e.keyCode == 13) {
	   	var html_li = '<li><input type="text" name="activities[0][]" placeholder="Click to add an item"> <a href="#" class="delete_do_at_work"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#do-at-work").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_do_at_work").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-do-at-work").click(function(){
		var html_li = '<li><input type="text" name="activities[0][]" placeholder="Click to add an item"> <a href="#" class="delete_do_at_work"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#do-at-work").append(html_li);

		// delete
		$("a.delete_do_at_work").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_do_at_work").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// ACTIVITIES[DO OUTSIDE WORK]
	$("#do-outside-work").keydown(function (e) {
	  if (e.keyCode == 13) {
	   	var html_li = '<li><input type="text" name="activities[1][]" placeholder="Click to add an item"> <a href="#" class="delete_do_outside_work"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#do-outside-work").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_do_outside_work").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-do-outside-work").click(function(){
		var html_li = '<li><input type="text" name="activities[1][]" placeholder="Click to add an item"> <a href="#" class="delete_do_outside_work"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#do-outside-work").append(html_li);

		// delete
		$("a.delete_do_outside_work").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_do_outside_work").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

	// VALUES
	$("#values").keydown(function (e) {
	  if (e.keyCode == 13) {
	   	var html_li = '<li><input type="text" name="values[]" placeholder="Click to add an item"> <a href="#" class="delete_values"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#values").append(html_li).find('input:text').focus();

		// delete
		$("a.delete_values").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});

		return false;
	  }
	});

	$("#add-values").click(function(){
		var html_li = '<li><input type="text" name="values[]" placeholder="Click to add an item"> <a href="#" class="delete_values"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>';
		$("ul#values").append(html_li);

		// delete
		$("a.delete_values").each(function(i){
			$(this).click(function(){
				$(this).parent('li').remove();
			})
		});
	});

	// delete
	$("a.delete_values").each(function(i){
		$(this).click(function(){
			$(this).parent('li').remove();
		})
	});
	// ---------------------------------------- >>>

})

</script>

<?php $this->load->view('includes/footer'); ?>