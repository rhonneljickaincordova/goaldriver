
	<ul id="plan-menu">
		<li class="<?php echo $this->uri->segment(2) == 'chapter' ? 'active' : '' ?>"><a class="view" data-toggle="tooltip" data-placement="bottom" title="View" href="<?php echo site_url('plan'); ?>">View</a></li>
		<li class="<?php echo $this->uri->segment(2) == 'cover_page' ? 'active' : '' ?>"><a class="cover-page" data-toggle="tooltip" data-placement="bottom" title="Cover page" href="<?php echo site_url('plan/cover_page'); ?>">Cover page</a></li>
		<li class="<?php echo $this->uri->segment(2) == 'download' ? 'active' : '' ?>"><a class="download" data-toggle="tooltip" data-placement="bottom" title="Download &amp; Print" href="<?php echo site_url('plan/download'); ?>">Download &amp; Print</a></li>
		<li class="<?php echo $this->uri->segment(2) == 'comments' ? 'active' : '' ?>"><a class="comments" data-toggle="tooltip" data-placement="bottom" title="Comments" href="<?php echo site_url('plan/comments'); ?>">Comments</a></li>
	</ul>
