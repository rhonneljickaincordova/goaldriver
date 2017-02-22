<ul class="nav nav-tabs" role="tablist" id="kpidata_tablist">
	<li role="presentation" class="active">
		<a id='daily-li' href="#kpi_data-daily" aria-controls="kpi_data-daily" role="tab" data-toggle="tab">
			Daily<span class="badge ng-binding margin_left_5"><?php echo $kpis_count['daily']; ?></span> 
		</a>
	</li>
	<li role="presentation">
		<a id='weekly-li' href="#kpi_data-weekly" aria-controls="kpi_data-weekly" role="tab" data-toggle="tab">
			Weekly<span class="badge ng-binding margin_left_5"><?php echo $kpis_count['weekly']; ?></span>
		</a>
	</li>
	<li role="presentation">
		<a id='monthly-li' href="#kpi_data-monthly" aria-controls="kpi_data-monthly" role="tab" data-toggle="tab">
			Monthly<span class="badge ng-binding margin_left_5"><?php echo $kpis_count['monthly']; ?></span>
		</a>
	</li>
	<li role="presentation">
		<a id='quarterly-li' href="#kpi_data-quarterly" aria-controls="kpi_data-quarterly" role="tab" data-toggle="tab">
			Quarterly<span class="badge ng-binding margin_left_5"><?php echo $kpis_count['quarterly']; ?></span>
		</a>
	</li>
	<li role="presentation">
		<a id='yearly-li' href="#kpi_data-yearly" aria-controls="kpi_data-yearly" role="tab" data-toggle="tab">
		Yearly<span class="badge ng-binding margin_left_5"><?php echo $kpis_count['yearly']; ?></span>
		</a>
	</li>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="kpi_data-daily" style="padding:0px !important;">
		<?php $this->load->view("kpi/tables/kpi_data_daily.php"); ?>
	</div>
	<div role="tabpanel" class="tab-pane" id="kpi_data-weekly" style="padding:0px !important;">
		<?php $this->load->view("kpi/tables/kpi_data_weekly.php"); ?>
	</div>
	<div role="tabpanel" class="tab-pane" id="kpi_data-monthly" style="padding:0px !important;">
		<?php $this->load->view("kpi/tables/kpi_data_monthly.php"); ?>
	</div>
	<div role="tabpanel" class="tab-pane" id="kpi_data-quarterly" style="padding:0px !important;">
		<?php $this->load->view("kpi/tables/kpi_data_quarterly.php"); ?>
	</div>
	<div role="tabpanel" class="tab-pane" id="kpi_data-yearly" style="padding:0px !important;">
		<?php $this->load->view("kpi/tables/kpi_data_yearly.php"); ?>
	</div>
</div>

