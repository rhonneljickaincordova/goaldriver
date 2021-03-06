<table id="kpi_calendar-daily" class="data_entry_table table table-bordered">
	<thead>
		<tr>
		
			
			<th class="kpi_ids"></th>
			
			
			<th>
				<div class="form-group">
					<div class='input-group date' id='mydatepicker-days' style="width: 140px;">
						<input type='text' id="mydatepicker_text-days" class="form-control" style="width:100px !important; max-width:100px !important" />
						<img class="input-group-addon orange_calendar" src="<?php echo base_url()."uploads/icons/calendar.png";?>"/>	
					</div>
				</div>
			</th>
			
			
			<th class="prev_data">
				<button type="button" class="prev_data_btn" ng-click="prev_day_data()"><span class="glyphicon glyphicon-chevron-left"></span></button>
				<input type="hidden" value="<?php echo $enterData_daily_dates['start_date']; ?>" id="start_day">
			</th>
			
			<?php
			if($enterData_daily_dates['dates'])
			{
				foreach($enterData_daily_dates['dates'] as $date)
				{
					?>
					<th class='kpi_days_date'>
						<p><?=$date->formatted_date?></p>
						<input type="hidden" value="<?=$date->selected_date?>" class="day_val">
					</th>
					<?php
				}	
			}
			?>
			
			
			<th class="next_data">
				<button type="button" class="next_data_btn" ng-click="next_day_data()"><span class="glyphicon glyphicon-chevron-right"></span></button>
				<input type="hidden" value="<?php echo $enterData_daily_dates['last_date']; ?>" id="last_day">
			</th>
			
			
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div>
	<input type="button" value="Save" class="btn btn-primary" ng-click="KpiData_SaveChanges('daily')" style="float:left;"> 
	<div id="data_daily_alert_message" class="col-md-9" ></div>
</div>
<div style="clear:both"></div>




