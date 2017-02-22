<html>
<head>
	<title>My Pitch</title>

	<link rel="stylesheet" href="<?php echo base_url(); ?>public/bootstrap334/css/bootstrap.min.css">
	<script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>

	<style type="text/css">
	body{
		font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
	}
	#published-pitch{
		margin: 2em 0;
	}
	#company, #headline{
		text-align: center;
	}
	.clearfix{
		padding: 0 0 2em;
	}
	h3{
		text-align: center;
		margin-bottom: 1em;
		font-size: 30px;
		border-bottom: 1px solid #eee;
		padding: 10px 0;
		background: #f8f8f8;
	}
	h4{
		
		font-size: 23px;
	}
	ol{
		margin: 0;
		padding: 0;
	}
	ol li{
		list-style-position: inside;
	}
	#financial-projections ul{
		padding: 0;
	}
	#financial-projections ul li{
		list-style-position: inside;
	}
	</style>
</head>
<body>




<div class="container">
	<div class="col-sm-12" id="published-pitch">

	<?php if(@$company->hide == 0): ?>
		<?php if(@$company->logo == '' && @$company->name == ''): ?>
		<h3><span>Name and Logo</span></h3>	
		<?php endif; ?>
		<div id="company">
			<?php if(@$company->logo != ''): ?>
			<img src="<?php echo base_url('uploads/'.@$company->logo); ?>" width="200">
			<?php endif; ?>

			<?php if(@$company->name != ''): ?>
			<h1><?php echo @$company->name; ?></h1>
			<?php endif; ?>

			<?php if(@$company->logo == '' && @$company->name == ''): ?>
			Not yet started.
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if(@$headline->hide == 0): ?>
	<div id="headline" class="vision">
		<h3><span>Vision</span></h3>
		<div class="inner headline bg-white-wrapper">
			<?php if(@$headline->value != ''): ?>
			<?php echo html_entity_decode(@$headline->value); ?>
			<?php else: ?>
			Not started yet.
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>


	<?php if(@$purpose->hide == 0): ?>
	<div class="vision">
		<div class="inner bg-white-wrapper">
			<h3><span>Purpose</span></h3>
			<?php if(@$purpose->value != ''): ?>
			<?php echo html_entity_decode(@$purpose->value); ?>
			<?php else: ?>
			Not started yet.
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if(@$values->hide == 0): ?>
	<div class="vision">
		<div class="inner bg-white-wrapper">
			<h3><span>Values</span></h3>
			<?php if(@$values->value != ''): ?>
			<?php echo html_entity_decode(@$values->value); ?>
			<?php else: ?>
			Not started yet.
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if(@$positioning->hide == 0): ?>
	<div class="vision">
		<div class="inner bg-white-wrapper">
			<h3><span>Positioning</span></h3>
			<?php if(@$positioning->value != ''): ?>
			<?php echo html_entity_decode(@$positioning->value); ?>
			<?php else: ?>
			Not started yet.
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>



	<?php if(@$problem->hide == 0 || @$solution->hide == 0 || @$targetmarket[0]->hide == 0 || @$competition[0]->hide == 0): ?>
	<h3><span>Our Opportunity</span></h3>

	<div id="our-opportunity" class="row">
		<div class="clearfix">
			<?php if(@$problem->hide == 0): ?>
			<div class="col-sm-6">
				<div class="inner">
					<h4>Problems worth solving</h4>
					<?php if(@$problem->type == 'desc'): ?>
					<?php echo html_entity_decode(@$problem->text_value); ?>
					<?php else: ?>
					<?php 
					$problems = unserialize(@$problem->list_value);
					//var_dump($problems);
					if( $problems ){
						echo '<ol>';
						foreach ($problems as $problem_list) {
							if($problem_list != '')
								echo '<li>'.$problem_list.'</li>';
						}
						echo '</ol>';
					}
					else{
						echo 'Not started yet.';
					}
					?>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if(@$solution->hide == 0): ?>
			<div class="col-sm-6">
				<div class="inner">
					<h4>Our solutions</h4>
					<?php if(@$solution->type == 'desc'): ?>
					<?php echo html_entity_decode(@$solution->text_value); ?>
					<?php else: ?>
					<?php 
					$solutions = unserialize(@$solution->list_value);

					if($solutions){
						echo '<ol>';
						foreach ($solutions as $solution_list) {
							if($solution_list != '')
								echo '<li>'.$solution_list.'</li>';
						}
						echo '</ol>';
					}else{
						echo 'Not started yet.';
					}
					?>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<div class="clearfix">
			<?php if(@$targetmarket[0]->hide == 0): ?>
			<div class="col-sm-6">
				<div class="inner">
					<h4>Target market</h4>
					<?php if($targetmarket): ?>
					<table class="table">
						<thead>
							<th>Name</th>
							<th>No. of prospects</th>
							<th>Spend annually</th>
						</thead>
					<?php 
					foreach ($targetmarket as $segment) {
						$segment_data = unserialize($segment->data);
						echo '<tr>';
							echo '<td>'.$segment_data['name_segment'].'</td>';
							echo '<td>'.$segment_data['prospect_segment'].'</td>';
							echo '<td>&pound;'.$segment_data['annual_prospect'].'</td>';
						echo '</tr>';
					}
					?>
					</table>
					<?php else: ?>
					Not started yet.
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if(@$competition[0]->hide == 0): ?>
			<div class="col-sm-6">
				<div class="inner">
					<h4>Competition</h4>
					<?php if(count($competition)): ?>
					<table class="table">
						<thead>
							<th>Name</th>
							<th>Advantage</th>
						</thead>
					<?php 
					//print_r($competition);
					foreach ($competition as $comp) {
						echo '<tr>';
							echo '<td>'.$comp->name.'</td>';
							echo '<td>'.$comp->advantage.'</td>';
						echo '</tr>';
					}
					?>
					</table>
					<?php else: ?>
					Not started yet.
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if(@$funding_needs->hide == 0 && $funding_needs->amount != NULL): ?>
	<div id="funding-needs">
		<h4>Funding needs</h4>
		<?php if(count($funding_needs)): ?>
		<table class="table">
			<thead>
				<th>Funding needed</th>
				<th>How will you use these funds?</th>
			</thead>
			<tr>
				<td><?php echo $funding_needs->amount; ?></td>
				<td><?php echo $funding_needs->text; ?></td>
			</tr>
		</table>
		<?php else: ?>
		Not started yet.
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if(@$sales_channel->hide == 0 || @$marketing->hide == 0): ?>
	<h3><span>Sales and marketing</span></h3>
	<div id="sales-marketing" class="row">
		<?php if(@$sales_channel->hide == 0): ?>
		<div class="col-sm-6">
			<div class="inner">
				<h4>Sales channel</h4>
				<?php if(@$sales_channel->type == 'desc'): ?>
				<?php echo html_entity_decode(@$sales_channel->text_value); ?>
				<?php else: ?>
				<?php 
				$sales = unserialize(@$sales_channel->list_value);
				//var_dump($problems);
				if( $sales ){
					echo '<ol>';
					foreach ($sales as $sales_list) {
						if($sales_list != '')
							echo '<li>'.$sales_list.'</li>';
					}
					echo '</ol>';
				}
				else{
					echo 'Not started yet.';
				}
				?>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if(@$marketing->hide == 0): ?>
		<div class="col-sm-6">
			<div class="inner">
				<h4>Marketing activities</h4>
				<?php if(@$marketing->type == 'desc'): ?>
				<?php echo html_entity_decode(@$marketing->text_value); ?>
				<?php else: ?>
				<?php 
				$marketing_activities = unserialize(@$marketing->list_value);
				//var_dump($problems);
				if( $marketing_activities ){
					echo '<ol>';
					foreach ($marketing_activities as $market_act) {
						if($market_act != '')
							echo '<li>'.$market_act.'</li>';
					}
					echo '</ol>';
				}
				else{
					echo 'Not started yet.';
				}
				?>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<!-- <h3><span>Forecast</span></h3>
	<div id="financial-projections">
		<?php 

			if(count($forecasts)): ?>
			<ul class="forecast-lists">
				<?php foreach ($forecasts as $forecast): ?>
				<li>
					<?php if($forecast->url != ''): ?>
					<?php echo $forecast->url; ?>
					<?php else: ?>
					<?php echo $forecast->file; ?>
					<?php endif; ?>
				</li>	
				<?php endforeach; ?> 
			</ul>
			<?php else: ?>
			Not started yet.
			<?php endif; ?>
	</div> -->

	<?php if(@$pitch_milestone[0]->hide == 0): ?>
	<h3><span>Milestones</span></h3>
	<div id="milestones">
		<?php if(count($milestones)): ?>
		<table class="table table-bordered">
			<thead>
				<th>Milestone</th>
				<th>Status</th>
				<th>Who's Responsible</th>
				<th>Due Date</th>
			</thead>
			<?php 
			foreach ($milestones as $m): 
			//$tasks = $this->task->get_many_by('milestone_id', $m->id);
			?>
			<tr>
				<td><?php echo $m->name; ?></td>
				<td>
					<?php
					
					switch ($m->status) {
						case 1:
							echo '10%';
							break;
						case 2:
							echo '20%';
							break;
						case 3:
							echo '30%';
							break;
						case 4:
							echo '40%';
							break;
						case 5:
							echo '50%';
							break;
						case 6:
							echo '60%';
							break;
						case 7:
							echo '70%';
							break;
						case 8:
							echo '80%';
							break;
						case 9:
							echo '90%';
							break;
						case 10:
							echo '100%';
							break;
						default:
							echo '0%';
							break;
					};
					?>
				</td>
				<td><?php echo user_info('first_name', $m->owner_id).' '.user_info('last_name', $m->owner_id); ?></td>
				<td><?php echo date('F j, Y', strtotime($m->dueDate)); ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php else: ?>
		Not started yet.
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if(@$pitch_teamkey[0]->hide == 0): ?>
	<h3><span>Team and key roles</span></h3>
	<div id="team-and-key-roles">
		<?php 
		
		if(count($users)): ?>
		<ol>
		<?php 
		foreach ($users as $user): ?>
			<li><?php echo $user->first_name.' '.$user->last_name; ?>&nbsp;&nbsp;&nbsp;<span class="text-muted"><?php echo $user->job_title; ?></span></li>	
		<?php endforeach; ?>
		</ol>
		<?php else: ?>
		Not started yet.
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if(@$partners[0]->hide == 0): ?>
	<h3><span>Partners and resouces</span></h3>
	<div id="partners-resources">
		<?php if(count($partners)): ?>
		<table class="table table-bordered">
			<thead>
				<th>Logo</th>
				<th>Name</th>
				<th>Description</th>
			</thead>
		
		<?php foreach($partners as $partner): 
			if($partner->name != NULL):
		?>
			<tr>
				<td><img src="<?php echo base_url('uploads/'.$partner->logo); ?>" width="120"></td>
				<td><?php echo $partner->name; ?></td>
				<td><?php echo $partner->description; ?></td>
			</tr>
		<?php endif; endforeach; ?>
		</table>
		<?php else: ?>
		Not started yet.
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>

</body>
</html>

