<div class="col-sm-3">
	<ul id="plan" class="list-group">
		<li class="<?php echo ($this->uri->segment(3)=='company'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/company'); ?>">Company</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='vision'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/vision'); ?>">Vision</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='purpose'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/purpose'); ?>">Purpose</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='values'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/values'); ?>">Values</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='positioning'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/positioning'); ?>">Positioning</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='problem_solving'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/problem_solving'); ?>">Problems worth solving</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='solution'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/solution'); ?>">Our solution</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='target_market'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/target_market'); ?>">Target market</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='competition'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/competition'); ?>">Competition</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='funding_needs'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/funding_needs'); ?>">Funding needs</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='sales_channel'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/sales_channel'); ?>">Sales channel</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='marketing_activities'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/marketing_activities'); ?>">Marketing activities</a></li>
		<!-- <li class="<?php echo ($this->uri->segment(3)=='forecast'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/forecast'); ?>">Forecast</a></li> -->
		<li class="<?php echo ($this->uri->segment(3)=='milestones'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/milestones'); ?>">Milestones</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='team_key_roles'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/team_key_roles'); ?>">Team and key roles</a></li>
		<li class="<?php echo ($this->uri->segment(3)=='partners'?'active':''); ?> list-group-item"><a href="<?php echo site_url('pitch/edit/partners'); ?>">Partners and resources</a></li>
	</ul>
</div>