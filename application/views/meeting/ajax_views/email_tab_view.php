<link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/jquery-confirm/dist/jquery-confirm.min.css" />

<script src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
<script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>public/script.js"></script>
<script src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
<script src="<?php echo base_url() ?>public/chosen/chosen.jquery.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-confirm/dist/jquery-confirm.min.js"></script>


<style type="text/css">
	.chosen-container
	{
		margin-bottom:10px;
	}
	.modal-header .close 
	{
		font-size:40px;
		margin-top: -10px !important;
	}
</style>

<?php
  
  if(!empty($meetings))
  {
  	$meeting = $meetings[0];

  	if(!empty($meeting['meeting_participants']))
    {
      $participants = unserialize($meeting['meeting_participants']);
    }

    if(!empty($meeting['nonuser_participants']))
    {
      $nonusers = unserialize($meeting['nonuser_participants']);
    }

    if($meeting['meeting_optional'] != "NA")
    {
      $optionals = unserialize($meeting['meeting_optional']);
    }

    if($meeting['meeting_cc'] != "NA")
    {
      $ccs = unserialize($meeting['meeting_cc']);
    }

  }

?>

<?php echo form_open("", array("id"=>"form_email_agenda")) ;?>
	
	<input type="hidden" name="meeting_id" value="<?php echo $meeting_id ?>" />
	<input type="hidden" name="meeting_title" value="<?php echo $meeting['meeting_title'] ?>" />
	<input type="hidden" name="start_date" value="<?php echo $meeting['when_from_date'] ?>" />

	<table class="table table-condensed table-hover">
		<thead>
			<tr>
				<th>Send</th>
				<th>Name</th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($participants)) :?>
                <?php foreach($participants as $par) :?>
					<tr>
						<td><input checked="checked" class="email-list-checkbox" id="participants" name="participants[]" type="checkbox" value="<?php echo user_info("email", $par) ?>"></td>
						<td><?php echo user_info("first_name", $par)." ".user_info("last_name", $par). " &lt;".user_info("email", $par)."&gt " ?> </td>
					</tr>
				<?php endforeach ;?>
            <?php endif;?>
		</tbody>
	</table>

    <br/>

	<span class="help-block">Non-users (Invite users that are not registered to the system - Optional)</span>
		
    <?php

    /*
        <table id="tbl_nonuser_emails" class="nonuser-emails table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="">Email</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text"  class="form-control" name="nonuser_emails[]" />
                    </td>
                    
                    <td>
                        <button class="btn btn-danger" style="visibility:hidden"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: left;">
                        <button type="button" id="add_nonusers_email" class="btn btn-primary text-white btn-sm"><i class="fa fa-plus"></i> Add more</button>
                    </td>
                </tr>
            </tfoot>
        </table>

    */

    ?>


        <div class="form-group">
          <label>Non-user participants</label>  <br/>
          <input class="form-control" data-role="tagsinput" placeholder="Enter email" name="nonusers_participant" value="<?php echo (!empty($meeting)) ? $nonusers : "" ?>"  />
        </div>

    <!-- ** commented optional/CC fields html fields
	<span class="help-block">CC (These recipients will receive an email with the minutes/agenda - Optional)</span>
	
	 <select class="form-control chosen-select form-cascade form-cascade-control" id="cat" name="optionals[]" multiple="multiple">
        <?php if(!empty($emails)):?>
            <?php foreach($emails as $email) :?>
                <option value="<?php echo $email['email'] ?>"><?php echo $email['email'] ?></option>
            <?php endforeach;?>
        <?php endif;?>
    </select>
    -->

    <br/>

	<label for="email_message">Message</label>
	<textarea class="form-control" id="email_message" name="email_message" rows="3"></textarea>

	<div class="form-group">
		<label class=""></label>
        <button type="button" class="btn btn-danger pull-right close_ajax_modal" style="margin-top:10px">Close</button>
		<button type="button" class="btn btn-primary pull-right btn-send-agenda" style="margin-top:10px">Send</button>
	</div>

<?php echo form_close() ;?>

<script type="text/javascript">
	$(function(){

  		/** Multiple choices for category in add product **/
	    var config = {
	          '.chosen-select'    : {max_selected_options: 4, placeholder_text_multiple: "Select email address"},
	          '.chosen-no-single' : {disable_search_threshold:10},
	          '.chosen-no-results': {no_results_text:'Oops, nothing found!'},
	          '.chosen-width'     : {width:"95%"}
	      }
	      for(var selector in config) 
	      {
	          $(selector).chosen(config[selector]);
	      }

	      $(".chosen-choices").addClass("form-control");
  	});

	$("#add_nonusers_email").on("click", function () {
        counter = $('#tbl_nonuser_emails tr').length - 2;
        var newRow = $("<tr>");
        var cols = "";

        cols += '<td><input type="text" class="form-control" name="nonuser_emails[]"/></td>';
        cols += '<td><button class="ibtnDel btn btn-danger"><i class="fa fa-times"></i></button></td>';

        newRow.append(cols);
        $("table.nonuser-emails").append(newRow);
        counter++;
    });

    $("table.nonuser-emails").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();
        counter -= 1
        $('#addrow').attr('disabled', false).prop('value', "Add Row");
    });
</script>