<?php $this->load->view('includes/header'); ?>
<?php
$organ_id = $this->session->userdata("organ_id");
$is_organisation_owner = is_organisation_owner($organ_id);
$is_active = "";
$has_assigned_kpis = false;
$bg_white_wrapper = "";
if($kpis_count['daily'] > 0 ||$kpis_count['weekly'] > 0 ||$kpis_count['monthly'] > 0 ||$kpis_count['quarterly'] > 0 ||$kpis_count['yearly'] > 0){
	$has_assigned_kpis = true;
}else{
	$bg_white_wrapper = "min_height_300";
}
	
?>

<div class="bg-white-wrapper <?php echo $bg_white_wrapper; ?>">
	<div class='kpi_container'>
		<!-- Nav tabs -->
		<?php 
		
			?>
			<div class="col-md-12">
				<ul class="nav nav-tabs" role="tablist" id='kpis_tablist'>
					<li role="presentation" class="active"><a id="kpi_data_tab-li" href="#kpi_data_tab" aria-controls="kpi_data_tab" role="tab" data-toggle="tab">Enter Data</a></li>
					<li role="presentation"><a href="#kpi" aria-controls="kpi" role="tab" data-toggle="tab">My KPIs</a></li>
					<li role="presentation"><a id="kpi_graphs-li" href="#kpi_graphs_tab" aria-controls="kpi_graphs_tab" role="tab" data-toggle="tab">Graphs</a></li>
					<li role="presentation"><a id="kpi_highcharts-li" href="#kpi_highcharts_tab" aria-controls="kpi_highcharts_tab" role="tab" data-toggle="tab">Dashboard</a></li>
				</ul>
			</div>
			<div class="col-md-12">
				<div class="tab-content">
					<input type="hidden" id="organisation_user_type" value="member">
					<input type="hidden" id="kpi_permission" value="readonly">
					<div role="tabpanel" class="tab-pane active <?php echo ($has_assigned_kpis == false ? "no_assigned_kpi" : ""); ?>" id='kpi_data_tab'  ng-controller="kpi_data" ng-init="reload_day_data();">
						<div class="panel">
						  <div class="panel-body">
						  
							<?php 
							if($has_assigned_kpis){
								$this->load->view("kpi/pages/kpi_data.php"); 
							}else{
								echo "<label>You have no KPIs assigned to you.</label>";	
							}
							?>
						  </div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="kpi">
						<div class="panel">
							<div class="panel-body">
							<?php 
							if($has_assigned_kpis){
								$this->load->view("kpi/pages/kpi.php");
							}else{
								echo "<label>You have no KPIs assigned to you.</label>";	
							}
							?>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id='kpi_graphs_tab' ng-controller="kpi_graphs">
						<div class="panel">
							<div class="panel-body">
								<?php $this->load->view("kpi/pages/graph.php"); ?>
							</div>
						</div>
					</div>
					<?php
					$kpi_highcharts_controller = (class_exists("Highcharts")) ? 'ng-controller="kpi_highcharts"' : "";
					
					?>
					<div role="tabpanel" class="tab-pane" id='kpi_highcharts_tab' <?php echo $kpi_highcharts_controller; ?>>
						<?php $this->load->view("kpi/modal/filter_highchart.php"); ?>
						<?php $this->load->view("kpi/modal/filter_highchart_gauge.php"); ?>
						<div class="panel">
							<div class="panel-body">
								<?php $this->load->view("kpi/pages/highchart.php"); ?>
							 </div>
						</div>	
					</div>
				</div>
			</div>
			
			
		<div style="clear:both"></div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>

