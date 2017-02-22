<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php //$this->load->view('pitch/includes/sidebar'); ?>

<div class="col-sm-7">
	<h3>Export your pitch as a slide deck</h3>
	<p>The content in your LivePlan pitch works great as a slide show to accompany a live presentation. Want to impress potential investors or make a splash at a pitching event? Punch up your talk with great-looking slides of your pitch text, market chart, competitive landscape, team view, and more.</p>

	<p>Click Export to PowerPoint to produce a .pptx file, which you can open in PowerPoint, Keynote, or other presentation software or upload to your favorite slide sharing service.</p>
	<br>
	<input type="submit" class="btn btn-primary" name="export" value="Export to PowerPoint">
</div>
<div class="col-sm-5">
<img src="<?php echo base_url(); ?>/public/images/present_ppt.png">
</div>

<?php $this->load->view('includes/footer'); ?>