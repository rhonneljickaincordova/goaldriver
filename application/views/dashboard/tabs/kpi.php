<div class="panel">
	<div class="panel-body">
		<!--<div class="container-fluid">-->
			<div class="d_kpis_container_fixed" ng-controller="kpi_highcharts" ng-init="get_highcharts();">
				<?php $this->load->view("kpi/modal/filter_highchart.php"); ?>
				<?php $this->load->view("kpi/modal/filter_highchart_gauge.php"); ?>
				<div id="container-highchart"></div>
			</div>
		<!--</div>-->
	</div>
</div>