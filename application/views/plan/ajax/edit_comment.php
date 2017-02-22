<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title">Edit comment</h4>
</div>
<div class="modal-body">
	<div id="alert-msg"></div>
	<textarea class="form-control" id="comment" name="comment"><?php echo $comment->comment; ?></textarea>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<button type="button" id="submit-comment-content" class="btn btn-primary">Save changes</button>
</div>


<script type="text/javascript">

$(function(){
	$("#submit-comment-content").click(function(){
		var comment = $.trim($("#comment").val());
		var comment_id = <?php echo $comment->id; ?>;

		if(comment != '')
		{
			$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/ajax_edit_comment_save'); ?>",
				  data: {comment: comment, comment_id: comment_id }
				}).done(function( msg ) {
					
					var data = JSON.parse(msg);
				   	if(data.action == 'success'){
						$("#alert-msg").html("<div class=\"alert alert-success\">Comment has been saved.</div>");	

						setTimeout(function(){
							$("#edit_comment").modal("hide");
						}, 2000);

						location.reload();
					}
				});
		}
	})

	// $('#edit_comment').on('hidden.bs.modal', function () {
		
	// })
})

</script>