<link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/jquery-confirm/dist/jquery-confirm.min.css" />

<script src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
<script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>public/script.js"></script>
<script src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
<script src="<?php echo base_url() ?>public/chosen/chosen.jquery.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-confirm/dist/jquery-confirm.min.js"></script>

<style type="text/css">
	.modal-header .close 
	{
		font-size:40px;
		margin-top: -10px !important;
	}
	.cursor-pointer
	{
		cursor:pointer;
	}
</style>

<table class="table table-condensed table-hover">
	<thead>
		<tr>
			<th>Email</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$participant = $participants;
			$nonuser    = explode(",", $nonusers);

		?>

		<?php if(!empty($participant)) :?>
			<?php foreach($participant as $key=>$partici) :?>

				<?php 
					$status = show_attendee_status($meeting_id, user_info("email", $partici)); 
				?>

				<tr>
					<td><?php echo user_info("email", $partici) ?></td>

					<?php if(!empty($status)) :?>
						<?php foreach($status as $stat) :?>
							<?php 
								$icon = "";
								$text = "";

								if($stat['acceptance_status'] == 0)
								{
									$icon = "fa-times";
									$text = "Declined";
									echo "<td><i class='fa <?php echo $icon ?> cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='".$text."' ></i></td> ";
								}
								if($stat['acceptance_status'] == 1)
								{
									$icon = "fa-check";
									$text = "Accepted";
									echo "<td><i class='fa <?php echo $icon ?> cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='".$text."'></i></td> ";
								}

								if($stat['acceptance_status'] == 2)
								{
									$icon = "fa-exclamation-triangle";
									$text = "Pending";
									echo "<td><i class='fa <?php echo $icon ?> cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='".$text."'></i></td> ";
								}

							?>
						<?php endforeach ;?>

					<?php else:?>
						<td><i class='fa fa-exclamation-triangle cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='Pending'></i></td> 
					<?php endif;?>

				</tr>
			<?php endforeach ;?>
		<?php endif;?>





		<!-- Non users listing -->
		<?php if(!empty($nonuser) && $nonuser != "") :?>
			<?php foreach($nonuser as $key=>$non) : ?>
				
				<?php 
					$status = show_attendee_status($meeting_id, $non); 
				?>

				<tr>
					<td><?php echo $non ?></td>

					<?php if(!empty($status)) :?>
						<?php foreach($status as $stat) :?>
						
							<?php 
								$icon = "";
								$text = "";

								if($stat['acceptance_status'] == 0)
								{
									$icon = "fa-times";
									$text = "Declined";
									echo "<td><i class='fa <?php echo $icon ?> cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='".$text."' ></i></td> ";
								}
								if($stat['acceptance_status'] == 1)
								{
									$icon = "fa-check";
									$text = "Accepted";
									echo "<td><i class='fa <?php echo $icon ?> cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='".$text."'></i></td> ";
								}

								if($stat['acceptance_status'] == 2)
								{
									$icon = "fa-exclamation-triangle";
									$text = "Pending";
									echo "<td><i class='fa <?php echo $icon ?> cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='".$text."'></i></td> ";
								}

							?>

						<?php endforeach ;?>

					<?php else:?>
						<td><i class='fa fa-exclamation-triangle cursor-pointer change-attend-stat' data-toggle='tooltip' data-placement='bottom' title='Pending'></i></td> 
					<?php endif;?>

				</tr>

			<?php endforeach ;?>
		<?php endif;?>



		<?php 
		/*
		<?php if(!empty($attendees)) :?>
			<?php foreach($attendees as $attendee) : ?>
				<tr>
					<td><?php echo $attendee['email'] ?></td>
					<td><i class="fa <?php echo ($attendee['attended'] == 0) ? "fa-times" : "fa-check" ?> cursor-pointer change-attend-stat" data-toggle="tooltip" data-placement="bottom" title="Click to change status" data-attendee-id="<?php echo $attendee['meeting_attendee_id'] ?>" data-status="<?php echo $attendee['attended'] ?>"></i></td>
				</tr>
			<?php endforeach ;?>
		<?php endif;?>
		*/?>
		
	</tbody>
</table>

<script type="text/javascript">
	$(document).ready(function(){
	  $('[data-toggle="tooltip"]').tooltip()

	  /** Changing of attendee status **

	  $('.change-attend-stat').bind('click', function(){
	  	var _this = $(this);
	  	var id = $(this).attr('data-attendee-id');
	  	var status = $(this).attr('data-status');
	  	
	  	if($(this).hasClass("fa-check"))
	  	{
	  		$.ajax({
	  			type:"POST",
	    		url:base_url+"index.php/meeting/update_attendees_status",
	    		data: {id:id, status:status},
	    		cache:false,
	    		success:function(response)
				{
					$(_this).attr('data-status', 0);
				}
	  		});

	  		$(this).removeClass("fa-check");
	  		$(this).addClass("fa-times");
	  	}

	  	else
	  	{
	  		$.ajax({
	  			type:"POST",
	    		url:base_url+"index.php/meeting/update_attendees_status",
	    		data: {id:id, status:status},
	    		cache:false,
	    		success:function(response)
				{
					$(_this).attr('data-status', 1);
				}
	  		});

	  		$(this).addClass("fa-check");
	  		$(this).removeClass("fa-times");
	  	}
	  });
	  
	  *****************/

	});
</script>