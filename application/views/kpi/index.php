<?php $this->load->view('includes/header'); ?>

<div class="bg-white-wrapper">
	<div class='kpi_container'>
			<!-- Nav tabs -->
			<div class="col-md-12">
				<ul class="nav nav-tabs" role="tablist" id='kpis_tablist'>
					<li role="presentation" class="active"><a id="kpi_data_tab-li" href="#kpi_data_tab" aria-controls="kpi_data_tab" role="tab" data-toggle="tab">Enter Data</a></li>
					<li role="presentation"><a href="#kpi" aria-controls="kpi" role="tab" data-toggle="tab">My KPIs</a></li>
					<li role="presentation"><a id="kpi_graphs-li" href="#kpi_graphs_tab" aria-controls="kpi_graphs_tab" role="tab" data-toggle="tab">Graphs</a></li>
					<li role="presentation"><a id="kpi_highcharts-li" href="#kpi_highcharts_tab" aria-controls="kpi_highcharts_tab" role="tab" data-toggle="tab">Dashboard</a></li>
					<li style='float:right !important' class='new_button_container'>
						<div class="btn-toolbar">
							
							
						</div>
					</li>
				</ul>
			</div>
			
			<input type="hidden" id="organisation_user_type" value="admin">
			<input type="hidden" id="kpi_permission" value="readwrite">
			<div class="col-md-12">
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id='kpi_data_tab'  ng-controller="kpi_data" ng-init="reload_day_data();">
						<div class="panel">
						  <div class="panel-body">
							<?php $this->load->view("kpi/pages/kpi_data.php"); ?>
						  </div>
						</div>
					</div>
					
					<div role="tabpanel" class="tab-pane" id="kpi" ng-controller="kpi">
						<?php $this->load->view("kpi/modal/add_kpi.php"); ?>
						<?php $this->load->view("kpi/modal/edit_kpi.php"); ?>	
						<?php $this->load->view("kpi/modal/delete_kpi.php"); ?>	
						<div class="panel">
						  <div class="panel-body">
							<?php $this->load->view("kpi/pages/kpi.php"); ?>
						  </div>
						</div>
					</div>
					
					<div role="tabpanel" class="tab-pane" id='kpi_graphs_tab' ng-controller="kpi_graphs">
						<?php $this->load->view("kpi/modal/add_graph.php"); ?>
						<?php $this->load->view("kpi/modal/edit_graph.php"); ?>
						<?php $this->load->view("kpi/modal/delete_graph.php"); ?>	
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

