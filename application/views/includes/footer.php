	</div>

	<footer >

		<div class="container" ng-controller="footerCtrl" ng-init="get_url('<?php echo base_url();?>')">
			<div class="row">
				<?php 
					$status = get_feedback_status();
				?>
				<div class="col-sm-6">&copy; Copyright <?php echo date('Y'); ?> - GoalDriver - V1.0.35</div>
				<?php if($this->session->userdata('user_id') != ''): ?>
				<div class="col-sm-6" style="text-align:right;" ng-show="show_footer">
					<span>How are you feeling about Goal Driver?</span><br>
					<div class="feedback">
						<!-- <a href="javascript:;" data-target="#modal_feedback" data-toggle="modal" ng-click="onClick('happy')" ng-class="happy_image ? 'image_happy_click' : 'image_happy'"></a>
						<a href="javascript:;" data-target="#modal_feedback" data-toggle="modal" ng-click="onClick('fine')" ng-class="fine_image ? 'image_fine_click' : 'image_fine'">Fine</a>
						<a href="javascript:;" data-target="#modal_feedback" data-toggle="modal" ng-click="onClick('sad')" ng-class="sad_image ? 'image_sad_click' : 'image_sad'">Sad</a> -->
						
						<a href="javascript:;" data-target="#modal_feedback" data-toggle="modal" ng-click="onClick('happy')" class="<?php echo (get_feedback_status() == 1 ? "image_happy_click" : "image_happy")   ;?> " >Happy</a>
						<a href="javascript:;" data-target="#modal_feedback" data-toggle="modal" ng-click="onClick('fine')" class="<?php echo (get_feedback_status() == 2 ? "image_fine_click" : "image_fine")   ;?> " >Fine</a>
						<a href="javascript:;" data-target="#modal_feedback" data-toggle="modal" ng-click="onClick('sad')" class="<?php echo (get_feedback_status() == 3 ? "image_sad_click" : "image_sad")   ;?> " >Sad</a>

					</div>
				</div>
				<?php endif; ?>
			</div>
			<div class="modal fade" id="modal_feedback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog comment_modal">
					<div class="modal-content" >
						<div class="modal-header ">
							<img style="width:20px;" src="" ng-src="<?php echo base_url();?>uploads/footer/{{icon}}" >&nbsp;<strong>Feedback</strong>
							<button type="button" class="close" data-dismiss="modal"  ng-click="onClickCloseModal()"aria-label="Close"><span aria-hidden="true">&times;</span></button>

						</div>

						<div class="modal-body">
							<div class="alert alert-success"  ng-show="success_save_feedback">
							    <a href="#" class="close" ng-click="close()" data-dismiss="alert" aria-label="close">&times;</a>
							  	{{feedback_message}}
							</div>

							<textarea style="height:200px;" class="form-control" ng-model="feedback" placeholder="Would you like to send us some feedback ?">
							</textarea>
						</div>
						<div class="modal-footer">
							<button type="button" id="create-milestone" class="btn btn-primary" ng-click="save_feedback('save','<?php echo base_url();?>')">Send</button>
							<button type="button" id="create-team" class="btn btn-default" ng-click="save_feedback('cancel','<?php echo base_url();?>')">Cancel</button>
						</div>

					</div>
				</div>
			</div>

		</div>

	</footer>
	<br>
	<div id="ajax-msg" class='hide'><div>Loading...</div><img src="<?php echo base_url('public/images/loader.gif'); ?>"></div>

     
	<script type="text/javascript">




	$('.panel-title > a').click(function() {
    $(this).find('i').toggleClass('fa-plus fa-minus')
           .closest('panel').siblings('panel')
           .find('i')
           .removeClass('fa-minus').addClass('fa-plus');
	});

	$(function(){
		// $.fn.dataTableExt.sErrMode = 'mute';
		// $('#teams, #users').dataTable({
		// 	  "columns": [
		// 	    null,
		// 	    null,
		// 	    null,
		// 	    null,
		// 	    { "orderable": false }
		// 	  ]
		// 	});

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
	<script src="<?php echo base_url() ?>public/app/global.js" type="text/javascript"></script>



	<script src="<?php echo base_url(); ?>public/angular-ui-calendar/src/calendar.js"></script>
	<script src="<?php echo base_url(); ?>public/fullcalendar/dist/fullcalendar.min.js"></script>
	<script src="<?php echo base_url(); ?>public/fullcalendar/dist/gcal.js"></script>

	<script src="<?php echo base_url(); ?>public/jquery.cookie.js"></script>

	<script>
		$(function(){
			$.ajaxSetup({
				data: { csrf_gd: Cookies.get('csrf_gd') }
			});
		})
	</script>

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
	if(isset($css))
	{
		if(!empty($css))
		{
			foreach($css as $css_dir)
			{
				echo "<link rel='stylesheet' href='$css_dir'>";
			}
		}
	}
	/* if(class_exists("Highcharts")){
		echo highchart_assets();
	} */

	?>
	
	<!-- Start of goaldriver Zendesk Widget script -->
	<!-- <script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(e){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("https://assets.zendesk.com/embeddable_framework/main.js","goaldriver.zendesk.com");
	/*]]>*/</script> -->
	<!-- End of goaldriver Zendesk Widget script -->
</body>
</html>
