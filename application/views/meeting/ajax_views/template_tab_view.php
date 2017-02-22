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
</style>

<div class="form-group">
	<input class="template_type" style="height:20px;width:20px;cursor:pointer" checked name="download_type" type="radio" value="load">&nbsp <a style="cursor:pointer;font-size:24px;color:#000"> Load Template</a>
</div>
<div class="form-group">
	<input class="template_type" style="height:20px;width:20px;cursor:pointer" name="download_type" type="radio" value="save">&nbsp <a style="cursor:pointer;font-size:24px;color:#000"> Save Template</a>
</div>

<hr>

<?php echo form_open("", array("id"=>"load_meeting_template")) ?>
	<div class="load-template-cont">
		<strong>Saved Templates</strong> <br />
		( Choose template to use )
		<table class="table">
		  <tbody>
		  		<input type="hidden" name="current_meeting_id" value="<?php echo $id ?>" />

		  		<?php if(!empty($templates)) :?>
		  			<?php foreach($templates as $template) :?>
			          <tr class="">
			            <td>
			            	<input type="radio" name="from_meeting_id" value="<?php echo $template['from_meeting_id'] ?>" style="float: left;height: 20px;width: 20px;cursor:pointer" />
			                <a href="#" style="float: left;margin-left: 10px;margin-top: 3px;"><?php echo $template['template_name'] ?></a>
			            </td>
			            <td>
			                <i class="fa fa-trash-o delete-saved-template" data-template-id="<?php echo $template['template_id'] ?>" style="cursor:pointer;color:blue;margin-top:5px" data-toggle='tooltip' data-placement='bottom' title='Delete' ></i>
					    </td>
			          </tr>
			        <?php endforeach;?>
			    <?php endif;?>
		  </tbody>
		</table>

		<div class="form-group">
			<label class=""></label>
			<button type="button" class="btn btn-primary pull-right btn-load-template">Load</button>
		</div>
	</div>
<?php echo form_close();?>


<?php echo form_open("", array("id"=>"save_template_form")) ?>
	<div class="save-tempalte-cont" style="display:none">
		<input type="hidden" name="from_meeting_id" value="<?php echo $id ?>" />
	    <div class="form-group">
		    <label>Title (required)</label>
		    <input type="text" class="form-control" name="template_name" />
	    </div>
	    <div class="form-group">
			<label class=""></label>
			<button type="button" class="btn btn-primary pull-right btn-save-template">Save</button>
		</div>
	</div>
<?php echo form_close() ;?>


<script type="text/javascript">
	$(document).ready(function(){
		$('.template_type').bind('click', function(){
			if($(this).val() == "load")
			{
				$('.save-tempalte-cont').hide();
				$('.load-template-cont').show();
				
			}
			if($(this).val() == "save")
			{
				$('.save-tempalte-cont').show();
				$('.load-template-cont').hide();
			}
		});

		$(function () {
		  $('[data-toggle="tooltip"]').tooltip();
		})
	});
</script>