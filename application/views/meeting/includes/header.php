<!DOCTYPE html>
<html lang="en" ng-app="moreApps" ng-cloak>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo (isset($title) ? $title : 'Business Planner'); ?></title>

  <link rel="stylesheet" href="<?php echo base_url(); ?>public/bootstrap334/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/styles.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/font-awesome.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/dataTables.bootstrap.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/angular-bootstrap/ui-bootstrap-csp.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/ui-select/dist/select.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>public/dataTables.bootstrap.css">

  <link rel="stylesheet" href="<?php echo base_url(); ?>public/fortawesome/font-awesome/css/font-awesome.css">

  <link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.css" />


  <link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.css" />
  <link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.css" />
  <link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />

  <link rel="stylesheet" href="<?php echo base_url() ?>public/css-circle/css/circle.css" />


  <!-- Meeting module plugins used -->
  <link href="<?php echo base_url() ?>public/global.modal/global.modal.css" rel="stylesheet">
  <link href="<?php echo base_url() ?>public/jquery-alerts/jquery.alerts.css" rel="stylesheet">


  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->
  <script src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
  <script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url(); ?>public/script.js"></script>
  <script src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
  <script src="<?php echo base_url(); ?>public/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>public/dataTables.bootstrap.js"></script>
  <script src="<?php echo base_url(); ?>public/tinymce/tinymce.min.js"></script>

  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular/angular.min.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular-bootstrap/ui-bootstrap.min.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular-bootstrap/ui-bootstrap-tpls.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular-bootstrap/angular-animate.js"></script>


  <!-- // <script  type="text/javascript" src="<?php echo base_url(); ?>public/ng-table/dist/ng-table.min.js"></script> -->
      

  <script  type="text/javascript" src="<?php echo base_url(); ?>public/momentjs/min/moment.min.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/momentjs/min/locales.min.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/humanize-duration/humanize-duration.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/ui-select/dist/select.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular-sanitize/angular-sanitize.min.js"></script>
 
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular-timer/app/js/_timer.js"></script>
  <script  type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>

  <script type="text/javascript" src="<?php echo base_url() ?>public/moment.js"></script>
  <script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.js"></script>
  <script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url() ?>public/chosen/chosen.jquery.js"></script>

  <script  type="text/javascript" src="<?php echo base_url(); ?>asset/header.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>asset/dashboard.js"></script>

  <?php if($this->session->userdata('logged_in')): ?>
  <style type="text/css">
  body{
    padding-top: 107px;
  }
  
  .scrollable-menu {
    height: auto;
    max-height: 200px;
    overflow-x: hidden;
  }

  #redesign.ui-select-bootstrap .ui-select-toggle{
    border:none!important;
    border-radius:none!important;
    -webkit-box-shadow:none!important;
    background-color:inherit!important;
  }

  #redesign.ui-select-search .ng-pristine .ng-valid .ng-empty .ng-touched:focus{
    border-color:none!important;
    background-color: inherit!important;
  }

  #redesign.ui-select-search .ng-pristine .ng-valid .ng-empty .ng-touched:hover{
    background-color: inherit!important;
  }

  .comment_modal.modal-dialog{
      width: 1150px!important;
      margin: 10px auto!important;

  }
  
  #contenteditable:hover{
      background: none;

  }
  #contenteditable:focus{
    outline:none;
  }

  .test + .tooltip.left > .tooltip-arrow {
      border-left: 5px solid red;
  }

  .chart {
    position:relative;
    margin:80px;
    width:220px; height:220px;
}
canvas {
    display: block;
    position:absolute;
    top:0;
    left:0;
}
.chart > span {
    color:#555;
    display:block;
    line-height:220px;
    text-align:center;
    width:220px;
    font-family:sans-serif;
    font-size:40px;
    font-weight:100;
    margin-left:5px;
}



  </style>

  <?php else: ?>
  <style type="text/css">
  body{
    padding-top: 50px;
  }
  </style>
  <?php endif; ?>
</head>

<script type="text/javascript"> 
  var base_url = "<?php echo base_url() ?>";
</script>

<html>

<body>
  <nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">
        Moreplanner Meeting Attendance
      </a>
    </div>
  </div>
</nav>


