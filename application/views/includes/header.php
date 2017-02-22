<?php  
echo '<!--';
print_r($_SESSION);
echo '-->';

defined('BASEPATH') OR exit('No direct script access allowed'); 

//echo $this->config->item('upload_dir');
$organ_name = '<img src="'.base_url('public/images/GoalDriver_logo_250.png').'" height="25">';

if($this->session->userdata('logged_in')){
  $organ_name = organ_info('name', $this->session->userdata('organ_id'));
  $is_active = user_info('is_active', $this->session->userdata('user_id'));
  $is_confirmed = user_info('is_confirmed', $this->session->userdata('user_id'));
  $profile_pic = user_info('profile_pic', $this->session->userdata('user_id'));
  $email = $this->session->userdata('email');  

  if($organ_name == '')
  {
    $organ_name = '<img src="'.base_url('public/images/GoalDriver_logo_250.png').'" height="25">';
  }  
}

?>
<!DOCTYPE html>
<html lang="en" ng-app="moreApps" ng-cloak>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo (isset($title) ? $title : 'GoalDriver'); ?></title>
  <link rel="icon" href="<?php echo base_url('public/favicon.ico'); ?>">

  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/fullcalendar/dist/fullcalendar.css">
    
 
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/bootstrap334/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/bootstrap334/css/dropdowns-enhancement.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/font-awesome.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/dataTables.bootstrap.css">
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> -->
  
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/angular-bootstrap/ui-bootstrap-csp.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/ui-select/dist/select.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/dataTables.bootstrap.css">
  <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>public/fortawesome/font-awesome/css/font-awesome.css"> -->
  <link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.css" />
  <link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.css" />
  <!-- <link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.css" /> -->
  <link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />
  <link rel="stylesheet" href="<?php echo base_url() ?>public/css-circle/css/circle.css" />
  <link rel="stylesheet" href="<?php echo base_url() ?>public/jquery-confirm/dist/jquery-confirm.min.css" />

  <!-- Meeting module plugins used -->
  <link href="<?php echo base_url() ?>public/global.modal/global.modal.css" rel="stylesheet">
  <!-- <link href="<?php echo base_url() ?>public/jquery-alerts/jquery.alerts.css" rel="stylesheet"> -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/styles_v1.css">
   

  <script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/bootstrap334/js/dropdowns-enhancement.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/dataTables.bootstrap.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/tinymce/tinymce.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-confirm/dist/jquery-confirm.min.js"></script>


  <script type="text/javascript" src="<?php echo base_url(); ?>public/angular/angular.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/angular-bootstrap/ui-bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/angular-bootstrap/ui-bootstrap-tpls.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/angular-bootstrap/angular-animate-v1.5.0.js"></script>
  <!-- <script type="text/javascript" src="<?php echo base_url(); ?>public/angular-animate/angular-animate-v1.5.0.js"></script> -->

  <script type="text/javascript" src="<?php echo base_url(); ?>public/angular-ui-calendar/src/calendar.js"></script>

  <script type="text/javascript" src="<?php echo base_url(); ?>public/momentjs/min/moment.min.js"></script>
  <!--<script type="text/javascript" src="<?php echo base_url(); ?>public/momentjs/min/locales.min.js"></script>-->
  <script type="text/javascript" src="<?php echo base_url(); ?>public/humanize-duration/humanize-duration.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/ui-select/dist/select.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>public/angular-sanitize/angular-sanitize.min.js"></script>
 
  <script  type="text/javascript" src="<?php echo base_url(); ?>public/angular-timer/app/js/_timer.js"></script>
  <script  type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>

  <!--<script type="text/javascript" src="<?php echo base_url() ?>public/moment.js"></script>-->
  <script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.js"></script>
  <!--<script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>-->
  <script type="text/javascript" src="<?php echo base_url() ?>public/chosen/chosen.jquery.js"></script>

  <script type="text/javascript" src="<?php echo base_url(); ?>public/script.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>public/date.js"></script>
   <?php 
	if($this->session->userdata('user_id') != '' && enable_chat() == true){
		 ?>
		 <link rel="stylesheet" href="<?php echo base_url() ?>public/app/css/chat.css" />	
		<script type="text/javascript" src="<?php echo base_url(); ?>public/app/chat.js"></script>
		 <?php
	}
    ?>
   
  

<script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/3.2.0/lodash.min.js"></script>
<script>
  if (!window.jQuery.ui) {
    document.write('<script src="js/libs/lodash.min.js"><\/script>');
  }
</script>
<script type="text/javascript" src="<?php echo base_url() ?>public/dotansimha-angularjs-dropdown-multiselect/src/angularjs-dropdown-multiselect.js"></script>


  <script  type="text/javascript" src="<?php echo base_url(); ?>asset/header.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>asset/resetpassword.js"></script>
  <script  type="text/javascript" src="<?php echo base_url(); ?>asset/footer.js"></script>
 
<style type="text/css">
body{
  <?php if($this->session->userdata('logged_in')): ?>
  padding-top: 107px;
  <?php else: ?>
  padding-top: 50px;
  <?php endif; ?>
}
footer .feedback{
  float: right;
}
footer .feedback a{
  width: 20px;
  display:block;
  height :20px;
  float:left;
  text-indent: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-align: right;
  margin-right:3px;
}

footer .feedback a.image_happy_click {
   background:url('<?php echo base_url();?>uploads/footer/happy.png');
   background-size: 20px 20px;
}

footer .feedback a.image_happy {
   background:url('<?php echo base_url();?>uploads/footer/happy_grey.png');
   background-size: 20px 20px;
}

footer .feedback a.image_happy:hover{
   background: url('<?php echo base_url();?>uploads/footer/happy.png');
   background-size: 20px 20px;
}



footer .feedback a.image_fine_click{
   background: url('<?php echo base_url();?>uploads/footer/ok.png');
   background-size: 20px 20px;
}

footer .feedback a.image_fine {
   background:url('<?php echo base_url();?>uploads/footer/ok_grey.png');
   background-size: 20px 20px;
}

footer .feedback a.image_fine:hover{
   background: url('<?php echo base_url();?>uploads/footer/ok.png');
   background-size: 20px 20px;
}


footer .feedback a.image_sad_click{
   background: url('<?php echo base_url();?>uploads/footer/sad.png');
   background-size: 20px 20px;
}


footer .feedback a.image_sad {
   background:url('<?php echo base_url();?>uploads/footer/sad_grey.png');
   background-size: 20px 20px;
}

footer .feedback a.image_sad:hover{
   background: url('<?php echo base_url();?>uploads/footer/sad.png');
   background-size: 20px 20px;
}
.milestone_checkbox{
  background:url('<?php echo base_url();?>uploads/icons/start-inactive.png');
  width: 18px;
  height: 17px;
}

.milestone_checkbox_active{
  background:url('<?php echo base_url();?>uploads/icons/start-active.png');
  width: 18px;
  height: 17px;
}

.milestone_checkbox_input {
  display: none;        
 }


</style>

</head>

<script type="text/javascript"> 
  var base_url = "<?php echo base_url() ?>";
  var csrf_name = "<?php csrf_name(); ?>";
</script>

<body>

  <!-- Global Modal (This is for ajax modal) -->
  <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close close_ajax_modal" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"></h4>
        </div>
        <div class="modal-body ajax_modal"></div>
        <div class="modal-footer"></div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="top-header" ng-controller="UserSettings_header">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainnav" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="navbar-brand OrganisationTitle" href="#" ng-click="show_OrgList()" hide-org-list>
            <?php if($this->session->userdata('user_id') != ''): ?>
            <i class="fa fa-bars" aria-hidden="true"></i>&nbsp;<?php echo $organ_name; ?>
            <div ng-show="dropdown_OrgList" class="dropdown_OrgList" >
              <span class="tip"></span>
              <div id="loader_div" ng-show="loader_div">
                <div id="circularG">
                  <div id="circularG_1" class="circularG"></div>
                  <div id="circularG_2" class="circularG"></div>
                  <div id="circularG_3" class="circularG"></div>
                  <div id="circularG_4" class="circularG"></div>
                  <div id="circularG_5" class="circularG"></div>
                  <div id="circularG_6" class="circularG"></div>
                  <div id="circularG_7" class="circularG"></div>
                  <div id="circularG_8" class="circularG"></div>
                </div>
              </div>
              <div ng-show="error_OrgList">{{ _error_OrgList_message }}</div>
              <div class="ul_OrgList_con">
                <div class="ul_OrgList_con_fixed">
                  <ul ng-show="ul_OrgList" class="ul_OrgList">
                    <li ng-repeat="listed_organisation in listed_organisations">
                    <div>
                      <a ng-href="<?php echo base_url();?>index.php/user-settings/organisations/change_organisation/{{ listed_organisation.organ_id}}" class="nounderline">
                     {{ listed_organisation.name }}
                      </a>
                    </div>
                    </li>
                  </ul>
                </div>
              </div>
              <input type="hidden" id="uri_string" value="<?php echo uri_string(); ?>">
              <div id="view-all-org-wrapper">
                <a href="<?php echo site_url('user-settings/organisations'); ?>">View all</a>
              </div>
            </div>
            <?php else: ?>
            <?php echo $organ_name; ?>
            <?php endif; ?>
           </div>
        </div>

        <ul class="nav navbar-nav navbar-right">
          <?php if($this->session->userdata('logged_in')): ?>
            <div class="notification" ng-controller="headerCtrl" ng-init="get_userId('<?php echo $this->session->userdata('user_id'); ?>','<?php echo site_url(); ?>')">

              <ul class="nav navbar-nav navbar-right">
                 <?php if($this->session->userdata('logged_in')): ?>
                    <li uib-dropdown ng-click="updateStatus()" on-toggle="toggled(open)" >
                      <a href="javascript:;" id="simple-dropdown" uib-dropdown-toggle>
                        <span class="bell"></span>
                        <span ng-show="count_usernotification >  '0' ">
                           <span class="badge">{{count_usernotification}}</span>
                        </span>
                      </a>
                      <span class="tip"></span>
                      <ul class="dropdown-menu scrollable-menu" uib-dropdown-menu aria-labelledby="simple-dropdown" ng-click="$event.stopPropagation()" style="width: 400px;">
                          <li class="dropdown-header" style="background-color:#565D65;">
                              <div class="alert alert-success"  ng-show="delete_data">
                                  <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                  {{delete_message}}
                              </div>
                            <div class="row">
                                <div class="checkbox col-md-8"> 
                                  <label><input type="checkbox" ng-model="selectedAll" ng-click="checkAll()"><h3><p  style="color:#aaafb8!important">Select All</p></h3></label>
                                </div>

                              
                                <div class="col-md-4">
                                 <button type="button" class="btn btn-danger" ng-click="delete_all()">Delete</button>
                                </div>
                            </div>
                          </li>
                          <li ng-repeat="choice in usernotification" class="dropdown-content" >
                            <div class="row">
                                  <div class="col-md-2">
                                    <div class="container-fluid">
                                      <input type="checkbox" ng-model="choice.Selected" ng-click="onSelectNotification(choice.Selected,choice.notification_id)">
                                    </div>
                                   </div>
                                  <div class="col-md-1">
                                      <div ng-if="choice.notification_type_id == 1" >
                                              <i class="fa fa-user"></i>
                                      </div>
                                      <div ng-if="choice.notification_type_id == 2" >
                                              <i class="fa fa-check-circle"></i>
                                      </div>
                                      <div ng-if="choice.notification_type_id == 3" >
                                              <i class="fa fa-line-chart" aria-hidden="true"></i>
                                      </div>
                                      <div ng-if="choice.notification_type_id == 4" >
                                              <i class="fa fa-tasks"></i>
                                      </div>
                                      <div ng-if="choice.notification_type_id == 5" >
                                            <i class="fa fa-folder-open"></i>
                                      </div>
                                  </div>
                                  <div class="col-md-8"> 
                                      <p ng-click="onclick(choice)">   {{choice.text}} </p> 
                                  </div>

                            </div>

                         </li>  

                      </ul>

                    </li>    
                      


                <?php endif; ?>


              </ul>

          </div>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                  
                  <?php if($profile_pic != ''): ?>
                  <img src="<?php echo profile_pic($this->session->userdata('user_id'), $profile_pic, TRUE); ?>" width="30">&nbsp;
                  <?php else: ?>
                  <img src="<?php echo base_url('public/images/unknown.png'); ?>">
                  <?php endif; ?>

                  <?php echo user_info('first_name', $this->session->userdata('user_id')) .' '.user_info('last_name', $this->session->userdata('user_id'));?>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <span class="tip"></span>
                  <li class="my-profile"><a href="<?php echo site_url('account/profile'); ?>"><span></span>My Profile</a></li>
                  <!--<li class="my-calendar"><a href="#"><span></span>My Calendar</a></li>
                  <li class="my-task"><a href="#"><span></span>My Task</a></li>-->
                  <?php if(is_account_owner($this->session->userdata('user_id'))): ?>
                  <li class="settings"><a href="<?php echo site_url('account/settings'); ?>"><span></span>Account</a></li>
                  <?php endif; ?>
                  <li class="divider"></li>
                  <li class="logout"><a class="logout" href="<?php echo site_url('account/logout'); ?>"><span></span>Log Out</a></li>
                </ul>
              </li>
              <?php else: ?>
              <li><a href="<?php echo site_url('account/sign_up'); ?>">Register</a></li>
              <li><a href="<?php echo site_url('account/sign_in'); ?>">Sign in</a></li>
              <?php endif; ?>
            </ul>
        </div>
      </div>

      <?php 
      if($this->session->userdata('logged_in')): ?>
      <div class="bottom-header">
        <div class="container">
          <div class="navbar-header">
            
          </div>
          <ul id="mainnav" class="nav navbar-nav navbar-left navbar-collapse collapse">
              <?php if($this->session->userdata('organ_id') != ''): ?>
                
                <?php 
                      $user_id = $this->session->userdata('user_id');
                      $organ_id  = $this->session->userdata('organ_id');

                ?>

              <li class="<?php echo $this->uri->segment(1) == 'dashboard' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 1) == 1 ? "display:none" : "")   ;?> " ><a href="<?php echo site_url('dashboard'); ?>">Dashboard</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'canvases' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 8) == 1 ? "display:none" : "")   ;?> "><a href="<?php echo site_url('canvases'); ?>">Canvases</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'pitch' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 2) == 1 ? "display:none" : "")   ;?> " ><a href="<?php echo site_url('pitch'); ?>">Strategy</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'plan' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 3) == 1 ? "display:none" : "")   ;?> "><a href="<?php echo site_url('plan'); ?>">Plan</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'milestone' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 4) == 1 ? "display:none" : "")   ;?> "><a href="<?php echo site_url('milestone'); ?>">Milestones</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'kpi' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 5) == 1 ? "display:none" : "")   ;?> "><a href="<?php echo site_url('kpi'); ?>">KPIs</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'meeting' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 6) == 1  ? "display:none" : "")   ;?> "><a href="<?php echo site_url('meeting'); ?>">Meetings</a></li>
              <li class="<?php echo $this->uri->segment(1) == 'teams' ? 'active' : '' ?>" style="<?php echo (has_hidden_rights($user_id, $organ_id, 7) == 1 ? "display:none" : "")   ;?> "><a href="<?php echo site_url('teams/user'); ?>">Teams</a></li>
              
              <?php else: ?>
              <li class="<?php echo $this->uri->segment(2) == 'organisations' ? 'active' : '' ?>"><a href="<?php echo site_url('user-settings/organisations'); ?>">Home</a></li>
              <li><a href="<?php echo site_url('learning/index'); ?>">Learning</a></li> 
              <li><a href="<?php echo site_url('account/settings'); ?>">Account</a></li>
              <?php endif; ?>
             </ul>
          

        </div>
      </div>  
      <?php endif; ?>

      </nav>

      <div class="page-title">
        <div class="container">
          <h1><?php echo $title; ?></h1>
        </div>
      </div>

    
      <div class="container">
      <?php if($this->session->userdata('logged_in') && $is_confirmed == 0): ?>
        <div class="alert alert-danger"><i class="fa fa-info-circle"></i> Your account is <strong>not activated.</strong> To activate, login to your email <strong><?php echo $email; ?></strong> and click on link to activate.</div>
      <?php endif; ?>

