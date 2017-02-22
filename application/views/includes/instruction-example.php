<?php 
$rand = random_string('alnum');

$example_id = 'example_'.$rand;
$instruction_id = 'instruction_'.$rand;
$id = $rand;
?>

<div class="example-instruction" id="<?php echo $id; ?>" style="position:relative;">
	<a href="javascript:;" class="down toggle-in-ex">Toggle instructions/example</a>

	<div class="example-instruction-wrapper">
		<div class="ex_wrap_<?php echo $id; ?>">
		  	<ul class="nav nav-tabs" role="tablist">
			    <li role="presentation" class="active"><a href="#<?php echo $instruction_id; ?>" aria-controls="instructions" role="tab" data-toggle="tab">Instructions</a></li>
			    <li role="presentation"><a href="#<?php echo $example_id; ?>" aria-controls="example" role="tab" data-toggle="tab">Example</a></li>
			</ul>
			<div class="tab-content">
			    <div role="tabpanel" class="tab-pane active" id="<?php echo $instruction_id; ?>">
			    	<?php if($this->session->userdata('user_type') == 'admin'): ?>
			    	<a href="javascript:;" class="pull-right instruction-edit" id="<?php echo $sec_id; ?>" table="<?php echo $table; ?>" uid="<?php echo $id; ?>" data-toggle="tooltip" data-placement="bottom" title="Edit this instructions">Edit this instructions</a>
			    	<?php endif; ?>
			    	<?php echo '<div id="instructions-content">'.html_entity_decode($instructions).'</div>'; ?>
			    	
			    </div>
			    <div role="tabpanel" class="tab-pane" id="<?php echo $example_id; ?>">
			    	<?php if($this->session->userdata('user_type') == 'admin'): ?>
			    	<a href="javascript:;" class="pull-right example-edit" id="<?php echo $sec_id; ?>" table="<?php echo $table; ?>" uid="<?php echo $id; ?>" data-toggle="tooltip" data-placement="bottom" title="Edit this example">Edit this example</a>
			    	<?php endif; ?>
			    	<?php echo '<div id="example-content">'.html_entity_decode($example).'</div>'; ?>
			    	
			    </div>
			</div>
		</div>
	</div>
</div>
