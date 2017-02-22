<table id="kpi_calendar-yearly" class="data_entry_table table table-bordered">
	<thead>
		<tr>
			
			
			<th class="kpi_ids"></th>
			
			
			<th>
				<div class="form-group">
					<div class='input-group date' id='mydatepicker-years' style="width: 140px;">
						<input type='text' id="mydatepicker_text-years" class="form-control" style="width:100px !important; max-width:100px !important" />
						<img class="input-group-addon orange_calendar" src="<?php echo base_url()."uploads/icons/calendar.png";?>"/>	
					</div>
				</div>
			</th>
			
			
			<th>
				<button type="button" class="prev_data_btn" ng-click="prev_year_data()"><span class="glyphicon glyphicon-chevron-left"></span></button>
				<input type="hidden" value="<?php echo $enterData_yearly_dates['start_date']; ?>" id="start_year">
			</th>
			
			
			<?php
			if($enterData_yearly_dates['dates'])
			{
				foreach($enterData_yearly_dates['dates'] as $date)
				{
					?>
					<th class='kpi_years_date'>
						<p><?=$date->formatted_date?></p>
						<input type="hidden" value="<?=$date->selected_date?>" class="year_val">
					</th>
					<?php
				}
			}
			?>
			
			
			<th>
				<button type="button" class="next_data_btn" ng-click="next_year_data()"><span class="glyphicon glyphicon-chevron-right"></span></button>
				<input type="hidden" value="<?php echo $enterData_yearly_dates['last_date']; ?>" id="last_year">
			</th>
			
			
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div>
	<input type="button" value="Save" class="btn btn-primary" ng-click="KpiData_SaveChanges('yearly')" style="float:left;">
	<div id="data_yearly_alert_message" class="col-md-9" ></div>
</div>
<div style="clear:both"></div>




