<style type="text/css">
	.close-popover-btn
	{
	    width: 15px;
	    height: 15px;
	    position: absolute;
	    right: -5px;
	    top: -7px;
	    opacity: 1;
	    text-indent: -999999;
	    cursor: pointer;
	}	

	.close-popover-btn > p
	{
	    position: absolute;
	    right: 2px;
	    top: -6px;
	}	
</style>

<?php 

  if(!empty($datas))
  {
  	$data = $datas[0];

  	if(!empty($data['assigned_user']))
    {
      $assignees = unserialize($data['assigned_user']);
    }
  }

?>


<?php echo form_open("", array("id"=>"assign-task-due")) ?>

<div class="popover-task-content">
	<button type="button" class="close-popover-btn"><p>x</p></button>

	<input type="hidden" name="subtopic_id" value="<?php echo $subtopic_id ?>" />
	<input type="hidden" name="id" value="<?php echo $ntd_id ?>" />

	<p>Assign task to: (Optional)</p>
	<select class="form-control chosen-select" name="assigned_user[]" multiple="multiple">
		<?php if(!empty($assignees)) :?>
	        <?php foreach($assignees as $ass) :?>
	            <option value="<?php echo $ass ?>" selected><?php echo user_info("email", $ass) ?></option>
	        <?php endforeach ;?>
	     <?php endif;?>

	    <?php if(!empty($all_emails)):?>
	        <?php foreach($all_emails as $all_email) :?>
	            <option value="<?php echo $all_email['user_id'] ?>"><?php echo $all_email['email'] ?></option>
	        <?php endforeach;?>
	    <?php endif;?>
  	</select>

	<hr/>

	<p>Set due date: (Optional)</p>
	<div class="input-group">
      <input type="text" class="form-control" id="due_date" name="due_date" value="<?php echo (!empty($data)) ? $data['due_date'] : "" ?>" />
      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
  	</div>
	
	<br />

	<button type="button" class="btn btn-primary btn-xs pull-right btn-save-assigned-due" style="margin-bottom:10px">Save</button>
</div>

<?php echo form_close() ;?>

<script type="text/javascript">
  $(document).ready(function(){

    $('#due_date').datetimepicker({
       useCurrent: false, //Important! See issue #1075,
       locale: 'en',
    });

    /** Multiple choices for category in add product **/
    var config = {
      '.chosen-select'    : {max_selected_options: 4, placeholder_text_multiple: "Click to select"},
      '.chosen-no-single' : {disable_search_threshold:10},
      '.chosen-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-width'     : {width:"95%"}
  }
  for(var selector in config) 
  {
      $(selector).chosen(config[selector]);
  }

  $(".chosen-choices").addClass("form-control");


  	$('.btn-save-assigned-due').bind('click', function(){
  		var form_data = $('#assign-task-due').serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

  		$.post(base_url+"index.php/meeting/update_meeting_note_subtopic", form_data, function(response){
			var obj = $.parseJSON(response);

			if(obj['error'] == 0)
			{
				location.reload();
			}
		});

  	});

  });
</script>