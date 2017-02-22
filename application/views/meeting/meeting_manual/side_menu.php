<div class="col-sm-3">
	<ul id="plan" class="list-group">

		<li class="<?php echo ($this->uri->segment(2)=='email-agenda-minutes'?'active':''); ?> list-group-item"><a href="<?php echo site_url('meeting/email-agenda-minutes'); ?>">Email meeting agenda to users and non-users of the system</a></li>
		<li class="<?php echo ($this->uri->segment(2)=='print-meeting-agenda'?'active':''); ?> list-group-item"><a href="<?php echo site_url('meeting/print-meeting-agenda'); ?>">Print meeting information</a></li>
		<li class="<?php echo ($this->uri->segment(2)=='download-agenda-minutes'?'active':''); ?> list-group-item"><a href="<?php echo site_url('meeting/download-agenda-minutes'); ?>">Download meeting information (PDF)</a></li>
		<li class="<?php echo ($this->uri->segment(2)=='manage-attendance'?'active':''); ?> list-group-item"><a href="<?php echo site_url('meeting/manage-attendance'); ?>">Manange meeting attendance</a></li>
		<li class="<?php echo ($this->uri->segment(2)=='agenda-templates'?'active':''); ?> list-group-item"><a href="<?php echo site_url('meeting/agenda-templates'); ?>">Agenda and minutes template</a></li>
		<li class="<?php echo ($this->uri->segment(2)=='follow-up-meeting'?'active':''); ?> list-group-item"><a href="<?php echo site_url('meeting/follow-up-meeting'); ?>">Follow-up meetings</a></li>
		
	</ul>
</div
