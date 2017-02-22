<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('meeting/meeting_manual/side_menu'); ?>

<div class="col-sm-9">
	<h3>Manage meeting attendance</h3>
	<br />

      <div class="col-sm-9" data-highlight="">
        <p>The basic agenda and minutes space is a template, with the title, the participants, date, time and location. When you print or email your agenda all information will be formatted in a professional looking document.</p>

		<p>In addition there is another template feature; you can load a standard meeting outline (list of topics) by choosing of your custom templates.</p>

		<p></p>
		<div class="center">
		</div><p></p>

		<h3>Loading templates</h3>

		<p>On the top of your note taking area click the “Templates” button and select “Load template”. You can select one of the templates you created yourself. </p>

		<p>Once a template is loaded, you can modify the agenda as you see fit; you can delete topics, you can move them to a different location (click and hold the move link and drag the topic to a different location or even to the Parking Lot) and you can add additional topics. You can even load multiple templates in one meeting.</p>

		<p></p>
		<div class="center"><a href="" class="fancybox" rel="fancybox-group" title=""><img alt="Select one or more standard or custom templates for your agenda and minutes" class="center zoom" src="<?php echo base_url('public/images/template_1.png') ?>" width="500"></a></div><p></p>

		<h3>Saving Meeting Templates</h3>

		<p>Creating your own templates is really easy. If you have a meeting agenda/minutes structure you want to use for future meetings, just click “Templates”, select “Save as template”, give it a name and save it. By default it will only save the Topics and any topic tags, but you also have the option to include the Notes, Decisions and Tasks in your template (including private notes for instructions).</p>

      <div class="clearfix"></div>
      
          
</div>



<?php $this->load->view('includes/footer'); ?>