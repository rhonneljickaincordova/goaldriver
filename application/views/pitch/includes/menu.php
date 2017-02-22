<ul id="pitch-menu">
	<li class="<?php echo ($this->uri->segment(1) == 'pitch' && $this->uri->segment(2) == '' || $this->uri->segment(1) == 'pitch' && $this->uri->segment(2) == 'index') ? 'active' : '' ?>"><a class="view" href="<?php echo site_url('pitch/index'); ?>" data-toggle="tooltip" data-placement="bottom" title="View">View</a></li>
	<li class="<?php echo $this->uri->segment(2) == 'edit' ? 'active' : '' ?>"><a class="edit" href="<?php echo site_url('pitch/edit/company'); ?>" data-toggle="tooltip" data-placement="bottom" title="Edit">Edit</a></li>
	<li class="<?php echo $this->uri->segment(2) == 'publish' ? 'active' : '' ?>"><a class="publish" href="<?php echo site_url('pitch/publish'); ?>" data-toggle="tooltip" data-placement="bottom" title="Publish">Publish</a></li>
	<!-- <li><a href="<?php echo site_url('pitch/present'); ?>">Present</a></li> -->
</ul>

