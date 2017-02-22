<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	<div id="pitch">
		<h3>Forecast</h3>
		<p>When you are confident that you have identified a market with a problem worth solving, it's time to put together a quick financial forecast for your solution. Can you actually turn a profit? Some opportunities look good at a glance, but don't hold up to financial scrutiny. If you are planning for an already established company, setting financial targets is just as important so you can track your progress in the coming year.</p>
		<br />
		
		<?php echo $response_msg; ?>
		<?php echo form_open_multipart('pitch/edit/forecast'); ?>

		<div id="share-link">
			<div class="form-group">
				<label class="heading">Link <small class="text-muted">(e.g. A link to a google document)</small></label>
				<input type="text" name="url" class="form-control" placeholder="http://">
			</div>
			<p>- OR - <a href="#" id="show-upload-doc">Upload a document</a></p>
		</div>
		
		<div id="upload-doc" style="display:none;">
			<div class="form-group">
				<label class="heading">Upload a document <small class="text-muted">(Supported files: PDF, MSWord, MSPowerpoint, MSExcel)</small></label>
				<input type="file" name="document" class="form-control">
			</div>
			<p>- OR - <a href="#" id="show-share-link">Post a link</a></p>
		</div>

		<input type="submit" class="btn btn-primary btn-sm" name="submit" value="Save">
		<?php echo form_close(); ?>
		<br><br>

		<div id="forecast">
			<div id="forecast-load">
				<ul class="forecast-lists">
					<?php 
					if(count($forecasts)):
					foreach ($forecasts as $forecast): ?>
					<li>
						<?php if($forecast->url != ''): ?>
						<a target="_blank" href="<?php echo $forecast->url; ?>"><?php echo $forecast->url; ?></a>
						<?php else: ?>
						<a target="_blank" href="<?php echo site_url('pitch/download_forecast/'.encrypt($forecast->id)); ?>"><?php echo $forecast->file; ?></a>
						<?php endif; ?>

						<span class="pull-right">
							<a href="javascript:;" onclick="delete_forecast(<?php echo $forecast->id; ?>)" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-o"></i></a>
						</span>
					</li>	
					<?php endforeach; else: ?>
					<li>No data yet.</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<br><br>
		<a href="<?php echo site_url('pitch/edit/milestones'); ?>" class="btn btn-success btn-sm pull-right">Continue</a>
	</div>
</div>
<script type="text/javascript">
	

$(function(){
	$('a#show-upload-doc').click(function(){
		$("#share-link").hide();
		$("#upload-doc").show();
	});

	$('a#show-share-link').click(function(){
		$("#share-link").show();
		$("#upload-doc").hide();
	})

})

function delete_forecast(id)
{
	var conf = confirm('Are you sure you want to delete?');

	if(conf)
	{
		$.ajax({
		  method: "POST",
		  url: "<?php echo site_url('pitch/delete_forecast'); ?>",
		  data: {id: id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
		}).done(function( msg ) {
			var data = JSON.parse(msg);
			
			if(data.action == 'success'){	
				$('#forecast').load(location.href + ' #forecast-load');
			}
		});
	}
}



</script>

<?php $this->load->view('includes/footer'); ?>