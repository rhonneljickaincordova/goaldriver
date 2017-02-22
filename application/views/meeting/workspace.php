<?php $this->load->view('includes/header'); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>public/meeting.js"></script>

<style type="text/css">
  .bootstrap-tagsinput
  {
    width:100%;
  }  

  .bootstrap-tagsinput .tag 
  {
    font-size: 13px;
  }

  .email_result:hover
  {
    background: #56a7f9;
  }
  .chosen-container
  {
    width:100% !important;
  }
  .topics-container
  {
    height:auto;
    padding:5px;
  }
  
  .subtopic_title_heading
  {
    padding:10px;
    line-height: 22px;
    margin-left: 30px;
  }

  .subtopic-toolbars
  {
    float:right;
    display:none;
    border: 1px solid #cce5f9;
    /*height: 19px;*/
    width: 115px;
    text-align: center;
    padding: 1px;
    background: #fff;
    color: #1f71c4;
    zoom: 1;
    margin-top:-8px;
  }

  .subtopic_title_heading:hover .subtopic-toolbars
  {
    display:block;
  }

  .form-group
  {
    margin-bottom: 20px;
  }
  .view-subtopic-btn
  {
    margin-top:15px;
  }
  .toolbar-actions
  {
    border:1px solid #D0CDCD;
    height: 45px;
    background: #F3F3F3;
    margin-top:-10px;
  }
  .controls-actions
  {
    width:474px;
    height:28px;
    margin-top:5px;
    margin-bottom: 5px;
    margin-left: 10px;
  }
  .private-checkbox
  {
    cursor:pointer;
  }
  .subtopic-items-cont
  {
    margin-bottom: 20px;
    margin-left: 55px;
  }
  .parking-lot-panel
  {
    position: fixed;
    width:15%;
    margin-left: 1%;
  }
  .bold-content
  {
    font-weight: 600;
    display: inline-block;
    font-size: 13px;
  }
  .remove_from_parkinglot
  {
    margin-top:10px;
    cursor:pointer;
  }
  .remove_from_parkinglot > i
  {
    margin-top:10px;
  }
  .empty-parking-lot
  {
    text-align: center;
    font-weight: 700;
    font-style: italic;
    color: #999;
  }
  .textarea-with-label
  {
    padding:5px;
    background-color:#E8E8E8;
  }
  .content-item
  {
    padding:10px;
    background: #D0E7FD;
  }
  .content-item > .item-label
  {
    font-size:14px;
    color: #0088CC;
  }
  .content-item > .subtopic-note-toolbars
  {
  float:right;
  margin-top: -8px;
  display: none;
  text-align: center;
  color: #1f71c4;
  }
  .content-item:hover .subtopic-note-toolbars
  {
  display:block;
  }
  .content-item > .topic-ntd-toolbars
  {
  float:right;
  margin-top: -8px;
  display: none;
  text-align: center;
  color: #1f71c4;
  }
  .content-item:hover .topic-ntd-toolbars
  {
  display:block;
  }

  .content-item:hover .subtopic-note-toolbars
  {
  display:block;
  }
  label
  {
    font-size:15px;
  }
  hr
  {
    border:1px solid #E4E4E4;
  }
  a:hover
  {
    text-decoration: none !important;
  }
  .topic-toolbars
  {
    float: right;
    display: none;
    border: 1px solid #cce5f9;
    height: 39px;
    width: 190px;
    text-align: center;
    padding: 4px;
    background: #fff;
    color: #1f71c4;
    zoom: 1;
    margin-top: -4px;
  }
  .topic-hover-header:hover .topic-toolbars
  {
    display:block;
    cursor:pointer;
  }
  .list-group-item
  {
    cursor:move;
    margin-bottom:10px;
  }

  .panel-default
  {
    background-color:#ECECEC !important;
  }

  .padding-removed
  {
    padding:0px;
  }

  .align-box
  {
    width: 73%;
    margin-left: 15px;
  }

  .icon-toolbars
  {
    font-size:14px;
    cursor:pointer;
  }

  .topic-hover-header
  {
    margin-bottom:10px;
  }  
  label
  {
      margin-bottom:10px !important;
  }

  .box-header
  {
    border-bottom: 1px solid #e5e5e5;
    margin: 0;
    padding: 7px 10px;
    box-sizing: border-box;
    box-shadow: 0px 1px 2px rgba(200,200,200,0.1);
    background: #56a7f9;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
  }
  .box-header > h3
  {
    line-height: 1.2em;
    font-size: 18px;
    display: inline;
    color: white;
    font-weight: 300;
  }
  .form-control
  {
    margin-bottom: 10px;
  }
 
</style>

<link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />

<script type="text/javascript" src="<?php echo base_url() ?>public/moment.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/chosen/chosen.jquery.js"></script>


<?php $this->load->view('meeting/includes/menu') ;?>

<?php 
  $user_id = $this->session->userdata('user_id');
  $last_created_meeting_id = decrypt($this->uri->segment(3));

  if(!empty($params))
  {
    $param = $params['followup'];  
  }
  
  if(!empty($meetings))
  {
    $meeting = $meetings[0];
    $tags = unserialize($meeting['meeting_tags']);

    if(!empty($meeting['meeting_participants']))
    {
      $participants = unserialize($meeting['meeting_participants']);
    }

    if(!empty($meeting['nonuser_participants']))
    {
      $nonusers = unserialize($meeting['nonuser_participants']);
    }

    if($meeting['meeting_optional'] != "NA")
    {
      $optionals = unserialize($meeting['meeting_optional']);
    }

    if($meeting['meeting_cc'] != "NA")
    {
      $ccs = unserialize($meeting['meeting_cc']);
    }
  }

?>

  <div class="col-sm-8 padding-removed">
    <div class="create-meeting-container">
      <div class="panel panel-default">  
        <div class="panel-heading" style="background:#f5f5f5 !important">
          <h4>
            Meeting Information
            <span class="pull-right">
                <a href="#" class="panel-minimize" id=""><i class="fa fa-minus"></i></a>
            </span>
          </h4>
        </div>

        <div class="panel-body">
        
        <?php echo form_open("", array("id"=>"save_meeting_info")) ;?>

            <?php if(!empty($param)) :?>
              <div class="alert alert-info text-center" role="alert"><p><i class="fa fa-share"></i> This is a followup meeting.</p></div>
            <?php endif;?>

            <div class="form-group">
              <label>Meeting Title <small>(required)</small></label>  
              <input class="form-control" placeholder="" name="meeting_title" value="<?php echo (!empty($meeting)) ? $meeting['meeting_title'] : "" ?>"  />
            </div>
           
            <!--
            <div class="form-group">
              <label>Tags</label>  <br/>
              <input class="form-control project-tags" data-role="tagsinput" placeholder="" name="meeting_tags" value="<?php echo (!empty($meeting)) ? $tags : "" ?>"  />
            </div>
            -->

            <div class="form-group">
              <label>Participants</label><br/>
              <select class="form-control chosen-select" name="participants[]" multiple="multiple" >

              <?php if(!empty($meetings)) :?>

                  <?php if(!empty($participants)) :?>
                    <?php foreach($participants as $par) :?>
                        <option value="<?php echo $par ?>" selected><?php echo user_info("email", $par) ?></option>
                    <?php endforeach ;?>
                  <?php endif;?>

                  <?php if(!empty($emails)):?>
                      <?php foreach($emails as $email) :?>
                          <option value="<?php echo $email['user_id'] ?>"><?php echo $email['email'] ?></option>
                      <?php endforeach;?>
                  <?php endif;?>

              <?php else :?>

                  <option value="<?php echo $this->session->userdata("user_id") ?>" selected><?php echo user_info("email", $user_id) ?></option>
                  
                  <?php if(!empty($emails)):?>
                      <?php foreach($emails as $email) :?>
                          <option value="<?php echo $email['user_id'] ?>"><?php echo $email['email'] ?></option>
                      <?php endforeach;?>
                  <?php endif;?>

              <?php endif;?>

              </select>
           </div>


           <div class="form-group">
              <label>Non-user participants</label>  <br/>
              <input class="form-control nonusers-parti" data-role="tagsinput" placeholder="Enter email" name="nonusers_participant" value="<?php echo (!empty($meeting)) ? $nonusers : "" ?>"  />
            </div>


           <!-- ** commented optional and CC html fields

            <div class="show-hide-optional-cc" style="<?php echo (!empty($meeting)) ? "display:block" : "display:none" ?>">
              <div class="form-group">
                <label>Optional</label><br/>
                <select class="form-control chosen-select" name="optionals[]" multiple="multiple" >
                  
                  <?php if(!empty($meetings)) :?>

                    <?php if(!empty($optionals)) :?>
                      <?php foreach($optionals as $opt) :?>
                          <option value="<?php echo $opt ?>" selected><?php echo user_info("email", $opt) ?></option>
                      <?php endforeach ;?>
                    <?php endif;?>

                    <?php if(!empty($emails)):?>
                        <?php foreach($emails as $email) :?>
                            <option value="<?php echo $email['user_id'] ?>"><?php echo $email['email'] ?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                    

                  <?php else :?>

                    <?php if(!empty($emails)):?>
                        <?php foreach($emails as $email) :?>
                            <option value="<?php echo $email['user_id'] ?>"><?php echo $email['email'] ?></option>
                        <?php endforeach;?>
                    <?php endif;?>

                  <?php endif;?>

                </select>
              </div>

              <div class="form-group">
                <label>CC</label><br/>
                <select class="form-control chosen-select" name="cc[]" multiple="multiple" >

                  <?php if(!empty($meetings)) :?>

                    <?php if(!empty($ccs)) :?>
                      <?php foreach($ccs as $cc) :?>
                          <option value="<?php echo $cc ?>" selected><?php echo user_info("email", $cc) ?></option>
                      <?php endforeach ;?>
                    <?php endif;?>

                     <?php if(!empty($emails)):?>
                        <?php foreach($emails as $email) :?>
                            <option value="<?php echo $email['user_id'] ?>"><?php echo $email['email'] ?></option>
                        <?php endforeach;?>
                    <?php endif;?>

                  <?php else :?>

                    <?php if(!empty($emails)):?>
                        <?php foreach($emails as $email) :?>
                            <option value="<?php echo $email['user_id'] ?>"><?php echo $email['email'] ?></option>
                        <?php endforeach;?>
                    <?php endif;?>

                  <?php endif;?>

              </select>
              </div>
            </div>

            <?php if(!empty($meeting)) :?>
              <div class="form-group">
                <small><span class="show-hide-toggle" style="cursor:pointer;text-decoration:underline"><p>Hide Optional and CC</p></span></small>
              </div>

            <?php else:?>
              <div class="form-group">
                <small><span class="show-hide-toggle" style="cursor:pointer;text-decoration:underline"><p>Add Optional and CC</p></span></small>
              </div>

            <?php endif;?>

            ** End of the optional and CC html -->

            <div class="form-group">
              <label>When</label><br />
              <div class="input-group" style="margin-bottom:5px">
                  <span class="input-group-addon">From</span>
                  <input type="text" class="form-control" id="date-time-picker-from-date" name="meeting_date_from" value="<?php echo (!empty($meeting)) ? $meeting['when_from_date'] : "" ?>" />
              </div>
              <div class="input-group">
                  <span class="input-group-addon">To &nbsp &nbsp&nbsp</span>
                  <input type="text" class="form-control" id="date-time-picker-to-date" name="meeting_date_to" value="<?php echo (!empty($meeting)) ? $meeting['when_to_date'] : "" ?>" />
              </div>
            </div>

            <div class="form-group">
              <label>Location</label>  
              <input class="form-control" placeholder="" name="meeting_location" value="<?php echo (!empty($meeting)) ? $meeting['meeting_location'] : "" ?>"  />
            </div>

            <hr />

            <?php 
              $get_data = $this->input->get();
              $is_followup = $get_data['followup'];
            ?>

            <div class="form-group" style="<?php echo ( !empty($meeting) || !empty($is_followup) ) ? "display:none" : "display:block" ?>">
              <button type="button" class="btn btn-primary pull-right btn-save-meeting"> Save Meeting</button>
            </div>

            <div class="form-group" style="<?php echo ( !empty($meeting) && empty($is_followup) ) ? "display:block" : "display:none" ?>">
              <button type="button" class="btn btn-primary pull-right btn-update-meeting" data-btn-meeting-id="<?php echo (!empty($meeting)) ? $meeting['meeting_id'] : "" ?>"> Update</button>
            </div>

            <div class="form-group" style="<?php echo ( !empty($meeting) && !empty($is_followup) ) ? "display:block" : "display:none" ?>">
              <button type="button" class="btn btn-primary pull-right btn-update-meeting" data-btn-meeting-id="<?php echo (!empty($meeting)) ? $meeting['meeting_id'] : "" ?>"> Save Meeting</button>
            </div>

          <?php echo form_close() ;?>

        </div>
      </div>
    </div>  
  </div>


  <div class="col-sm-3">
    <!--<div class="parking-lot-panel"> -->
      <div class="panel panel-default">
        <div class="parking-lot-panel">

            <div class="panel-heading">
              <h4>
                Back Burner
                <span class="pull-right">
                    <a href="#" class="panel-minimize" id=""><i class="fa fa-minus"></i></a>
                </span>
              </h4>
            </div>
            <div class="panel-body" id="panel-minimize-parking-lot-content" style="background:#ECECEC !important">
              <div class="parking-lot-content">
                <table class="table table-condensed bg-white-wrapper" id="topic-parkinglot-container">
                    <tbody>
                      <!--<tr class="parking-lot-guide"><a>What is a Back Burner and how does it work?</a></tr>-->
                      <?php if(!empty($parkinglots)) :?>
                        <?php foreach($parkinglots as $park) :?>
                          <tr class="topic-title-parking-lot">
                            <td>
                              <a href="#" class="remove_from_parkinglot" data-parkinglot-meeting-id="<?php echo $last_created_meeting_id ?>" data-topic-id="<?php echo $park['topic_id'] ?>" data-toggle='tooltip' data-placement='bottom' title='Move to meeting' ><i class="fa fa-arrow-left"></i></a>
                            </td>
                            <td>
                              <div class="bold-content">
                                <h5><?php echo $park['topic_title'] ?></h5>
                              </div>
                              <p class="meeting-title">
                                Meeting title:
                                <a href="#"><?php echo meeting_info("meeting_title", $park['meeting_id']) ?></a>
                              </p>
                            </td>
                          </tr>
                        <?php endforeach;?>

                    <?php else:?>
                      <tr class="topic-title-parking-lot" id="empty-list">
                        <td>
                          <p class="empty-parking-lot">No Back Burner Items</p>
                        </td>
                      </tr>
                    <?php endif;?>
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
  </div>

  <div class="clearfix"></div>
  
  <?php if(!empty($tasks) && !empty($param)) :?>
    <div class="col-sm-8 padding-removed">
      <div class="topics-panel-list">
          
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4>
                Tasks from previous meeting
                <span class="pull-right">
                    <a href="#" class="panel-minimize" id=""><i class="fa fa-minus"></i></a>
                </span>
              </h4>
            </div>
            <div class="panel-body" id="panel-minimize-topic-content">
                <div class="tasks-container">
                  <ul class="list-group">
                  <?php foreach($tasks as $task) :?>
                    <li class="list-group-item"> 
                      <p><input type="checkbox" class='mark_complete_task' data-id="<?php echo $task['note_id_linked'] ?>" <?php echo ($task['status'] == 10) ? "checked" : "" ?> /> <?php echo $task['task_name']?> - <a><?php echo user_info('first_name', $task['owner_id'])." ".user_info('last_name', $task['owner_id']) ?></a> <p>
                      <p style="margin-left:2.5%"><?php echo gd_date($task['entered_on']) ?></p>
                    </li>
                  <?php endforeach;?>
                  </ul>
                </div>
            </div>

          </div>
      </div>
    </div>
  <?php endif;?>

  <div class="clearfix"></div>

  <div class="col-sm-8 padding-removed">
    <div class="topics-panel-list">
        
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4>
              Meeting Topics
              <span class="pull-right">
                  <a href="#" class="panel-minimize" id=""><i class="fa fa-minus"></i></a>
              </span>
            </h4>
          </div>
          <div class="panel-body" id="panel-minimize-topic-content">
              
              <div class="topics-container"  id="topic-items">
                <ul id="meeting_topics" class="list-group">
                
                  <?php 
                    $topic_count = 1;
                    $counter = 0;
                  ?>
                  <?php if(!empty($topics)) :?>
                    <?php foreach($topics as $topic) : ?>
                      <li class="list-group-item hasItems topic-list-parent-cont" id="item-<?php echo $topic['topic_id'] ?>">

                        <h1 class="topic-hover-header"><i class='fa fa-file-text-o'></i>&nbsp <?php echo $topic['topic_title'] ?> &nbsp 
                          <div class="topic-toolbars">
                            <i class="fa fa-arrow-up icon-toolbars move-up"  data-toggle='tooltip' data-placement='bottom' title='Move up' data-topic-meeting-id="<?php echo $last_created_meeting_id ?>" data-position="<?php echo $topic['position'] ?>" data-topic-id-tomoved="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                            <i class="fa fa-arrow-down icon-toolbars move-down"  data-toggle='tooltip' data-placement='bottom' title='Move down' data-topic-meeting-id="<?php echo $last_created_meeting_id ?>" data-position="<?php echo $topic['position'] ?>" data-topic-id-tomoved="<?php echo $topic['topic_id'] ?>"></i> &nbsp 

                            <i class="fa fa-pencil edit-topic-link icon-toolbars"  data-toggle='tooltip' data-placement='bottom' title='Edit Topic' data-topic-meeting-id="<?php echo $last_created_meeting_id ?>" data-edit-topic-id="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                            <i class="fa fa-trash delete-topic-link icon-toolbars"  data-toggle='tooltip' data-placement='bottom' title='Delete Topic' data-delete-topic-id="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                            <i class="fa fa-plus show-saveas-actions icon-toolbars" data-toggle='tooltip' data-placement='bottom' title='Add note, task or decision' data-topic-cont-id="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                            <i class="fa fa-share move-to-parkinglot icon-toolbars" data-toggle='tooltip' data-placement='bottom' title='Move to Back Burner' data-parkinglot-topic-id="<?php echo $topic['topic_id'] ?>" data-parkinglot-meeting-id="<?php echo $last_created_meeting_id ?>" ></i>
                          </div>
                        </h1>
                          
                        <?php if($topic['presenter'] != 0):?>
                          <a href="javascript:void(0)"><?php echo user_info("first_name", $topic['presenter'])." ".user_info("last_name", $topic['presenter'])." - ".$topic['time'] ?></a><br/>
                        <?php endif;?>
                        <br/>

                        
                        <div class="view-topic-ntd">
                          <ul class="list-group meeting_topics_ntds">
                            <?php 
                              $get_data = $this->input->get();
                              $is_followup = $get_data['followup'];
                            ?>
                            <?php echo get_topic_ntd($topic['topic_id'], $is_followup) ?>
                            <!-- <a href="javascript:void(0)" class="show-topic-ntd" data-topic-ntd-id="<?php echo $topic['topic_id'] ?>"><i class="fa fa-eye"></i> View Notes, Decision or Task</a> -->
                          </ul>
                        </div>
                        

                        <!-- ntd meaning is note,task,decision -->
                        <div class="ntd-container<?php echo $topic['topic_id'] ?>" style="height:auto">
                          <!-- Subtopics will come out here via ajax request -->
                        </div>

                        <div class="topic-items-cont-input<?php echo $topic['topic_id'] ?>">
                          <input type="text" class="form-control topic-input-field" data-topic-input-field="<?php echo $topic['topic_id'] ?>" placeholder="Write note, task or decision" />
                        </div>
                        <div class="topic-items-cont<?php echo $topic['topic_id'] ?>" style="display:none">

                            <?php echo form_open("", array("id"=>"add-topic-note-task-decision".$topic['topic_id']."")) ;?>

                              <input type="hidden" name="meeting_id" value="<?php echo $last_created_meeting_id ?>" />
                              <input type="hidden" name="meeting_topic_id" value="<?php echo $topic['topic_id'] ?>" />
                              <input type="hidden" name="entered_by" value="<?php echo $user_id ?>" />

                              <textarea class="form-control topic-textarea-field<?php echo $topic['topic_id'] ?>" name="text" placeholder="Write note, decision or task"></textarea>

                              <div class="toolbar-actions">
                                  <div class="controls-actions">
                                  
                                    <div class="col-sm-12">
                                      <div class="save-as-actions">
                                        <p>Save As: &nbsp
                                          <input type="radio" name="type" value="1" /> Note &nbsp
                                          <input type="radio" name="type" value="2" /> Task &nbsp
                                          <input type="radio" name="type" value="3" /> Decision &nbsp
                                          
                                          <button type="button" class="btn btn-primary btn-save-saveas-actions" data-topic-id="<?php echo $topic['topic_id'] ?>"> Save</button>
                                          <button type="button" class="btn btn-danger btn-close-saveas-actions" data-close-topic-id-cont="<?php echo $topic['topic_id'] ?>"> Close</button>
                                        </p>

                                      </div>
                                    </div>

                                  </div>
                              </div>

                            <?php echo form_close() ;?>

                        </div>

                        <br />
                       <!--  <div class="view-subtopic-btn">
                          <a href="javascript:void(0)" class="show-subtopic" data-subtopic-id="<?php echo $topic['topic_id'] ?>"><i class="fa fa-eye"></i> View subtopics</a>
                        </div> -->
                        <?php 
                          $get_data = $this->input->get();
                          $is_followup = $get_data['followup'];
                        ?>
                        <?php echo list_subtopics($topic['topic_id'], $is_followup) ?>
                          <!-- Subtopics will come out here via ajax request -->

                        <br />                    

                        <button type="button" class="btn btn-primary show-topic" data-subtopic-count="<?php echo $topic_count ?>" id="show-subtopic-field<?php echo $topic_count ?>" style='margin-left:70px'><i class="fa fa-plus"></i> Add subtopic</button>
                          <div class="subtopic-form-cont<?php echo $topic_count ?>" style="padding-left:5px;margin-top:10px;display:none;margin-left:65px">
                            
                            <?php echo form_open("", array("id"=>"save-subtopic".$topic['topic_id']."", "class"=>"submit-subtopic-form", "data-form-id"=>"".$topic['topic_id'])) ;?>
                              <input type="hidden" name="topic_id" value="<?php echo $topic['topic_id'] ?>" />
                              <input type="text" required name="subtopic_title" class="form-control subtopic-title-input-field<?php echo $topic_count ?>" placeholder="Enter title for subtopic" name="subtopic_title" />
                              <p class="pull-right" style="margin-top:-7px">
                                <button type="submit" class="btn btn-primary btn-save-subtopic" data-topic-id="<?php echo $topic['topic_id'] ?>"> Save</button>
                                <button href="javascript:void(0)" class="btn btn-danger btn-hide-subtopic" id="<?php echo $topic_count ?>">Cancel</button>
                              </p>
                            <?php echo form_close() ;?>

                          </div>

                        <br />

                        </li>

                      <?php $topic_count++ ;?>
                    <?php endforeach;?>
                  <?php endif;?>

                </ul>

              </div>

              <br />

              <div class="form-group grey-border topic-form-cont" style="display:none">
                
                <div class="panel"> 
                  <div class="panel-body">
                    <ul class="add-topic-container list-group">
                      <?php echo form_open("", array("id"=>"save_created_topic")) ;?>
                        <input type="hidden" name="meeting_id" value="<?php echo $last_created_meeting_id ?>" />

                        <label>Topic Title</label>  
                        <input class="form-control input-topic-field" placeholder="Topic title" name="topic_title" />
                        <br />

                        <div class="options-toolbar">
                          <div class="form-group">
                            <label>Presenter</label>  
                            <select class="form-control chosen-select" name="presenter" >
                              <option value="">Select Presenter (optional)</option>
                              <?php if(!empty($participants)) :?>
                                <?php foreach($participants as $par) :?>
                                    <option value="<?php echo $par ?>"><?php echo user_info("email", $par) ?></option>
                                <?php endforeach ;?>
                              <?php endif;?>
                            </select>
                          </div>
                          <div class="form-group">
                            <label>Duration</label>  
                            <input class="form-control" placeholder="e.g 15m, 30m, 1h, 2h" name="time" />
                          </div>
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-save-topic"> Save</button>
                            <button type="button" class="btn btn-danger toggle-hide-topic-cont"> Cancel</button>
                          </div>
                        </div>

                      <?php echo form_close() ;?>
                    </ul>
                  </div>
                </div>

            </div>

            <div class="form-group">
              <button class="btn btn-primary pull-left btn-add-topic" <?php echo (!empty($last_created_meeting_id)) ? "" : "disabled data-toggle='tooltip' data-placement='bottom' title='Save meeting first' " ?> ><i class="fa fa-plus"></i> Add Topic</button>

              <div class="load-template-action">
                <?php if(!empty($last_created_meeting_id)) :?>
                  <a class="pull-left" style="color:black;margin-left: 10px;margin-top: 9px;">OR</a>
                <?php endif;?>

                <a href="javascript:void(0)" id="open_meeting_templates_below" data-meeting-id="<?php echo $last_created_meeting_id ?>" style="<?php echo (!empty($last_created_meeting_id)) ? "margin-top: 7px;font-size: 16px;margin-left: 16px" :"display:none" ?>" class="pull-left" >Load agenda from template</a>
              </div>

            </div>

          </div>  
        </div>
        
      </div>
    </div>

</div>

<?php $this->load->view('includes/footer'); ?>



<script type="text/javascript">
  $(document).ready(function() 
  {
    var dateNow = new Date();

    $('#date-time-picker-from-date').datetimepicker({
       useCurrent: false, //Important! See issue #1075
       format: "DD/MM/YYYY h:mm A",
    });

    $('#date-time-picker-to-date').datetimepicker({
        useCurrent: false,
        format: "DD/MM/YYYY h:mm A"
        // minDate: new Date()
    });


    /** Multiple choices **/
    var config = {
          '.chosen-select'    : {max_selected_options: 4, placeholder_text_multiple: "Select here"},
          '.chosen-no-single' : {disable_search_threshold:10},
          '.chosen-no-results': {no_results_text:'Oops, nothing found!'},
          '.chosen-width'     : {width:"95%"}
      }
      for(var selector in config) 
      {
          $(selector).chosen(config[selector]);
      }

      $(".chosen-choices").addClass("form-control");
    

    $('.btn-add-topic').tooltip('show');


    $('.show-subtopic').bind('click', function(){
      var topic_id = $(this).attr("data-subtopic-id");
      var $target = $(this).parent().next('.subtopics'+topic_id);

      $.ajax({
        type:"POST",
        url:base_url+"index.php/meeting/list_subtopics/"+topic_id,
        cache:false,
        success:function(response)
        {
          if(response == "")
          {
            $('.subtopics'+topic_id).html("<h5>No subtopics.</h5>");
          }
          else
          {
            $('.subtopics'+topic_id).html(response);
          }
        }
      });

      $('.minimize-subtopics').show();

    });


    $('.show-topic-ntd').bind('click', function(){
      var topic_id = $(this).attr("data-topic-ntd-id");
      var $target = $(this).parent().next('.ntd-container'+topic_id);

       $.ajax({
        type:"POST",
        url:base_url+"index.php/meeting/get_topic_ntd/"+topic_id,
        cache:false,
        success:function(response)
        {
          if(response == "")
          {
            $('.ntd-container'+topic_id).html("<h5>Nothing to show.</h5>");
          }
          else
          {
            $('.ntd-container'+topic_id).html(response);
          }
        }
      });

      return false;

    });



    $('.panel-minimize').click(function(e){
      e.preventDefault();
      var $target = $(this).parent().parent().parent().next('.panel-body');

      if($target.is(':visible')) 
      { 
        $('i',$(this)).removeClass('fa-minus').addClass('fa-plus'); 

        if($("#panel-minimize-parking-lot-content").css('display') == 'block')
        {
          $('.parking-lot-panel').css('width', '15%');
        }
      }
      else 
      { 
        $('i',$(this)).removeClass('fa-plus').addClass('fa-minus'); 
      }
      $target.slideToggle();

    });

    $('.btn-close-saveas-actions').bind('click', function(){
      var topic_id = $(this).attr('data-close-topic-id-cont');
      $('.topic-items-cont'+topic_id).hide();
    });

    $('.show-saveas-actions').bind('click', function(){
      var topic_id = $(this).attr('data-topic-cont-id');
      $('.topic-items-cont'+topic_id).show();
    });

    /** Move to parking lot **/
    $('.move-to-parkinglot').bind('click', function(){
      var topic_id = $(this).attr('data-parkinglot-topic-id');
      var meeting_id = $(this).attr('data-parkinglot-meeting-id');

      $.ajax({
        type:"POST",
        url: base_url+"index.php/meeting/move_to_parkinglot/"+topic_id+"/"+meeting_id,
        cache:false,
        success:function(response)
        {
          var obj = $.parseJSON(response);
          setTimeout(function(){
            location.reload();
          },0);
        },
        error:function(error)
        {
          console.log(error);
        },

      });

    });

    /** Remove from parking lot **/
    $('.remove_from_parkinglot').bind('click', function(){
      var topic_id = $(this).attr('data-topic-id');
      var meeting_id = $(this).attr('data-parkinglot-meeting-id');

      $.ajax({
        type:"POST",
        url: base_url+"index.php/meeting/removed_topic_from_parkinglot/"+topic_id+"/"+meeting_id,
        cache:false,
        success:function(response)
        {
          var obj = $.parseJSON(response);

          setTimeout(function(){
            location.reload();
          },0);
        },
        error:function(error)
        {
          console.log(error);
        },

      });

    });


    // add 1 hour after on the starting date
    $("#date-time-picker-from-date").on("dp.change", function(e){
        var words = $(this).val().split(" ");
        var date = words[0]
        var time  = words[1];
        var ampm = words[2];

        var split_time = time.split(":");
        var added_hour = split_time[0];
        var minutes = split_time[1];
        var convert_to_int = parseInt(added_hour);

        var final_time = convert_to_int + 1;


        if(final_time > 12)
        {
          final_time = 1;
          
          if(ampm == "AM")
          {
            ampm = "PM";
          }
          
        }

        $('#date-time-picker-to-date').val(date + " "+ final_time + ":" + minutes + " " +ampm);
        $('#date-time-picker-to-date').data("DateTimePicker").minDate(e.date); //disable dates before of the starting date
    });

    //disable choosing of dates after the selected TO date
    $("#date-time-picker-to-date").on("dp.change", function (e) {
        $('#date-time-picker-from-date').data("DateTimePicker").maxDate(e.date);
    });


    /** Submit ajax form when saving topic **/
     $('#save_created_topic').submit(function(e){
      var form_data = $(this).serialize()+"&"+csrf_name+"="+Cookies.get('csrf_gd');

      $.post(base_url+"index.php/meeting/save_meeting_topic", form_data, function(response){
        var obj = $.parseJSON(response);

        if(obj['error'] == 0)
        {
          
          $('#meeting_topics').load(location.href + ' #meeting_topics', function(){
             $.getScript(base_url+'public/script.js');
          });

          $('input[name=topic_title]').val("");
          $('input[name=time]').val("");
          $('input[name=topic_title]').focus();
        }
        else
        {
          $.alert({
              title: 'Error!',
              content: obj['message'],
              confirmButtonClass: 'btn-danger',
          });
        }
      });

        return false;
     });


     $('.mark_complete_task').bind('click', function(){
      var id = $(this).attr('data-id');
      var status;
      var _this = $(this);

      if($(this).is(':checked'))
      {
        status = 10;

        $.ajax({
          type:"POST",
          url: base_url+"index.php/meeting/mark_status_complete",
          data: { id:id, status:status, csrf_gd: Cookies.get('csrf_gd') },
          success:function(response)
          {
            var obj = $.parseJSON(response);

            if(obj['error'] == 0)
            {
              $.alert({
                  title: 'Success!',
                  content: obj['message'],
                  confirmButtonClass: 'btn-success',
              });
            }
            else
            {
              $.alert({
                  title: 'Error!',
                  content: obj['message'],
                  confirmButtonClass: 'btn-danger',
              });
            }

          },
          error:function(xhr)
          { 
            console.log(xhr);
          }

        });
      }
      else
      {
        status = 0;

        $.ajax({
          type:"POST",
          url: base_url+"index.php/meeting/mark_status_complete",
          data: { id:id, status:status, csrf_gd: Cookies.get('csrf_gd') },
          success:function(response)
          {
            var obj = $.parseJSON(response);

            if(obj['error'] == 0)
            {
              $.alert({
                  title: 'Success!',
                  content: obj['message'],
                  confirmButtonClass: 'btn-success',
              });
            }
            else
            {
              $.alert({
                  title: 'Error!',
                  content: obj['message'],
                  confirmButtonClass: 'btn-danger',
              });
            }

          },
          error:function(xhr)
          { 
            console.log(xhr);
          }

        });
      }

     });


  });
</script>
