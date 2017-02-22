<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>

<div id="main-content">
<div class="clearfix"></div>

<div id="pitch-publish" class="clearfix">
	<div class="col-sm-7">
		<?php echo form_open('pitch/publish', array('id' => 'unpublish-form')); ?>

		<?php if(count($mypitch)): ?>
			<h3><i class="fa fa-check-circle" style="color:#3c763d;"></i> Your strategy is now published as a secret web page</h3>
			<div class="well">
				<p><strong>Here is the secret location of your published strategy:</strong></p>
				<input type="text" value="<?php echo site_url('pitch/view/'.$mypitch->code); ?>" readonly class="form-control">
				<br>
				<a href="<?php echo site_url('pitch/view/'.$mypitch->code); ?>" target="_blank">View your secret webpage <i class="fa fa-share-square-o"></i></a>
			</div>

			<h3>Get feedback from people you know</h3>
			<p>Anyone with the secret link above can see your strategy. No login is required. Share this link with people you know and trust. We won't share it with anyone, and the page is not visible to search engines.</p>
			<p><small>You can turn off publishing at any time, and your secret web page will go away.</small></p>
			<input type="hidden" name="id" value="<?php echo $mypitch->id; ?>">
			<input type="hidden" name="is_publish" value="no">
			<input type="submit" name="publish" class="btn btn-primary" id="unpublish" value="Stop publishing my strategy">
		<?php else: ?>
			<h3>Publish your strategy as a secret web page</h3>
			<p>Want to get feedback on your strategy? Click the button below to publish it as a web page. We'll give you an unguessable link that you can share with your advisors, friends, and others. If you change your mind, you can stop publishing at any time.</p>

			<h3>Get feedback from people you know</h3>
			<p>Anyone with the secret link you share can see your strategy. No login is required. It's up to you to share that link with people you know and trust. We won't share it with anyone, though, and the page won't be visible to search engines.</p>
			<input type="hidden" name="is_publish" value="yes">
			<input type="submit" name="publish" class="btn btn-primary" value="Publish my strategy">
		<?php endif; ?>
		<?php echo form_close(); ?>
	</div>
</div>
</div>

<script type="text/javascript">
$(function(){
	$("#unpublish").confirm({
		title: 'Confirm!',
	    content: 'Are you sure you want to stop publishing your strategy?',
	    confirmButtonClass: 'btn-info',
    	cancelButtonClass: 'btn-danger',
	    confirm: function(){
	        $("#unpublish-form").submit();
	    }
	})
})
</script>

<?php $this->load->view('includes/footer'); ?>