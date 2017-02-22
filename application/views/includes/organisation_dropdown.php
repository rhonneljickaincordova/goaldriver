<div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainnav" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<div class="navbar-brand OrganisationTitle" href="#" ng-click="show_OrgList()" hide-org-list>
		<?php 
		if($this->session->userdata('user_id') != ''): 
			?>
			<i class="fa fa-bars" aria-hidden="true"></i>&nbsp;<?php echo $organ_name; ?>
			<div ng-show="dropdown_OrgList" class="dropdown_OrgList" >
				<span class="tip"></span>
				<div id="loader_div" ng-show="loader_div">
					<div id="circularG">
						<div id="circularG_1" class="circularG"></div>
						<div id="circularG_2" class="circularG"></div>
						<div id="circularG_3" class="circularG"></div>
						<div id="circularG_4" class="circularG"></div>
						<div id="circularG_5" class="circularG"></div>
						<div id="circularG_6" class="circularG"></div>
						<div id="circularG_7" class="circularG"></div>
						<div id="circularG_8" class="circularG"></div>
					</div>
				</div>
				<div ng-show="error_OrgList">{{ _error_OrgList_message }}</div>
				<div class="ul_OrgList_con">
					<div class="ul_OrgList_con_fixed">
						<ul ng-show="ul_OrgList" class="ul_OrgList">
							<li ng-repeat="listed_organisation in listed_organisations">
								<div>
									<a ng-href="<?php echo base_url();?>index.php/user-settings/organisations/change_organisation/{{ listed_organisation.organ_id}}" class="nounderline">
									{{ listed_organisation.name }}
									</a>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<input type="hidden" id="uri_string" value="<?php echo uri_string(); ?>">
				<div id="view-all-org-wrapper">
					<a href="<?php echo site_url('user-settings/organisations'); ?>">View all</a>
				</div>
			</div>
		<?php 
	else: 
		 echo $organ_name; 
	endif; 
	?>
	</div>
</div>
