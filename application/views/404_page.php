<html>
<head>
	  <link rel="stylesheet" href="<?php echo base_url(); ?>public/bootstrap334/css/bootstrap.min.css">
	  <link rel="stylesheet" href="<?php echo base_url(); ?>public/styles_v1.css">
	  <link rel="stylesheet" href="<?php echo base_url(); ?>public/font-awesome.css">
	   <script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
	   <title>Page not found</title>
</head>

<body>
   <nav class="navbar navbar-default navbar-fixed-top">
	  	<div class="top-header ng-scope" ng-controller="UserSettings_header">
	        <div class="container">
			    <div class="navbar-header">
			      <div class="navbar-brand" href="#">
			        404 PAGE NOT FOUND
			      </div>
			    </div>
			</div>
		</div>
	</nav>	

<div class="container clearfix">

	<div class="panel panel-default" style="margin-top:8%">
			
		<div class="" style="padding: 14px;text-align:center;padding-top:30px;padding-bottom:30px;">
			<img alt="" src="<?php echo base_url()?>public/images/404.jpg" style="margin-right:6%;" />
			<a onClick="javascript:goBack()" style="cursor:pointer"><p class="col-md-8 col-md-offset-2 text-center"><strong style="margin-right:25%">Click here to go back.</strong></p></a>
		</div>
			
	</div>

</div>

<footer>
	<div class="container">
	<div class="col-sm-6">&copy; Copyright <?php echo date('Y'); ?> - GoalDriver</div>
	<div class="col-sm-6" style="text-align:right;"></div>
	</div>
</footer>

<script>
function goBack() 
{
    window.history.back();
}
</script>

</body>
</html>
