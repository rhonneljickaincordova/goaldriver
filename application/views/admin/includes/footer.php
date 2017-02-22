	</div>

	<footer>
		<div class="container">
		<div class="col-sm-6">&copy; Copyright <?php echo date('Y'); ?> - GoalDriver- V1.0.35</div>
		<div class="col-sm-6" style="text-align:right;"></div>
		</div>
	</footer>
	<div id="ajax-msg"><i class="fa fa-spinner fa-pulse"></i> Loading...</div>

	<script type="text/javascript">

	$(function(){
		$.fn.dataTableExt.sErrMode = 'mute';
		$('#teams, #users').dataTable({
			  "columns": [
			    null,
			    null,
			    null,
			    null,
			    { "orderable": false }
			  ]
			});

		$(function () {
		  $('[data-toggle="tooltip"]').tooltip();
		})
	});


	tinymce.init({
		menubar:false,
	    selector: ".editor",
	    setup: function (editor) {
        	editor.on('change', function () {
            	editor.save();
        	});
    	}
	});

	// for inline editor
	tinymce.init({
	  selector: '.editable',
	  inline: true,
	  toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
	  menubar: false,
	});

	</script>
	<!--App version: <?php echo CI_VERSION; ?>-->


	<!-- Meeting Module Plugins used -->
	<script src="<?php echo base_url() ?>public/global.modal/global.modal.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>public/jquery-alerts/jquery.alerts.js" type="text/javascript"></script>

	<script src="<?php echo base_url(); ?>public/angular-ui-calendar/src/calendar.js"></script>
	<script src="<?php echo base_url(); ?>public/fullcalendar/dist/fullcalendar.min.js"></script>
	<script src="<?php echo base_url(); ?>public/fullcalendar/dist/gcal.js"></script>

	<script src="<?php echo base_url() ?>public/app/global.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>public/jquery.cookie.js"></script>
	
	<?php 
	if($this->session->userdata('user_id') != '' && enable_chat() == true){
		 $this->load->view("chat/index.php"); 	
	}
    ?>

	<?php
	if(isset($js))
	{
		if(!empty($js))
		{
			foreach($js as $js_dir)
			{
				echo "<script src='$js_dir' type='text/javascript'></script>";
			}
		}
	}
	?>

	<!-- Start of goaldriver Zendesk Widget script -->
	<!-- <script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(e){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("https://assets.zendesk.com/embeddable_framework/main.js","goaldriver.zendesk.com");
	/*]]>*/</script> -->
	<!-- End of goaldriver Zendesk Widget script -->

</body>
</html>
