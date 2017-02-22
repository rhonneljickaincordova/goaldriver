<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('plan/includes/menu'); ?>
<?php //$this->load->view('includes/section-sidebar'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />

<div class="bg-white-wrapper">

<div class="panel panel-default">
  <div class="panel-heading"><h4>Comments</h4></div>
  <div class="panel-body">
  	<div id="comments">
		<?php if(count($comments)): ?>
			<?php foreach($comments as $comment): ?>
				<div class="comment">
					<div style="margin-bottom:.2em;">
						<i class="fa fa-user"></i> <span class="text-muted"><?php echo $comment['first_name'].' '.$comment['last_name']; ?></span> commented on <a href="<?php echo site_url('plan/chapter/'.encrypt($comment['chapter_id']).'/'.encrypt($comment['section_id'])); ?>"><?php echo $comment['section_title']; ?></a> > <a href="<?php echo site_url('plan/chapter/'.encrypt($comment['chapter_id'])); ?>/<?php echo encrypt($comment['section_id']); ?>#section-title-<?php echo $comment['subsection_id']; ?>"><?php echo $comment['subsection_title']; ?></a></strong>
						&nbsp;&nbsp;<span class="text-muted"><?php echo timeAgo($comment['entered']); ?></span>
					</div>
					<div><a href="javascript:;" class="show-commented-text">Show comment</a></div>
					<div class="commented-text" style="display:none;"><?php echo $comment['comment']; ?></div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p>No one has posted any comments yet</p>
		<?php endif; ?>
	</div>
  </div>
</div>

</div>
<?php $this->load->view('includes/section-footer'); ?>

<script type="text/javascript">
$(function(){
	var rights = $('.check_rights').val();

	if(rights == "disabled")
	{
		// $('#plan-menu li a').hide();
		$('#plan-menu li:nth-child(2), #plan-menu li:nth-child(3)').remove();
		$("#sections .button-group").remove();
		$('a[data-toggle=modal]').hide();
		$('i[data-toggle=modal]').hide();
		$('i[data-toggle=tooltip]').hide();
		// $('div > a').hide();
	}

	$(".comment").each(function(){
		var _this = $(this);
		_this.find('a.show-commented-text').click(function(){
			_this.find('.commented-text').show();
		})
	})
})
</script>
<?php $this->load->view('includes/footer'); ?>