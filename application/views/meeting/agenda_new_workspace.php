<?php $this->load->view('includes/header'); ?>

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
    line-height: 0px;
    margin-left: 30px;
  }

  .subtopic-toolbars
  {
    float:right;
    display:none;
    border: 1px solid #cce5f9;
    height: 19px;
    width: 50px;
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
    width:17%;
    margin-left: 2%;
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
    float:right;
    display:none;
    border: 1px solid #cce5f9;
    height: 36px;
    width: 135px;
    text-align: center;
    padding: 1px;
    background: #fff;
    color: #1f71c4;
    zoom: 1;
    margin-top: -8px;
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
  $last_created_meeting_id = $this->uri->segment(3);

  $get_data = $params;

  if(!empty($meetings))
  {
    $meeting = $meetings;
    $tags = unserialize($meeting['meeting_tags']);

    if(!empty($meeting['meeting_participants']))
    {
      $participants = unserialize($meeting['meeting_participants']);
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

<div class="col-sm-12">
  <div class="col-sm-9 padding-removed">
    <div class="create-meeting-container">
      <div class="panel panel-default">  
        <div class="panel-heading">
          <h4>
            Create Meeting Form
            <span class="pull-right">
                <a href="#" class="panel-minimize" id=""><i class="fa fa-minus"></i></a>
            </span>
          </h4>
        </div>

        <div class="panel-body">
        
        <?php echo form_open("", array("id"=>"save_meeting_info")) ;?>

            <p> This is a followup meeting</p>

            <div class="form-group">
              <label>Meeting Title <small>(required)</small></label>  
              <input class="form-control" placeholder="Meeting title" name="meeting_title" value="<?php echo (!empty($meeting)) ? $meeting['meeting_title'] : "" ?>"  />
            </div>
           

            <div class="form-group">
              <label>Tags</label>  <br/>
              <input class="form-control project-tags" data-role="tagsinput" placeholder="Enter project tags" name="meeting_tags" value="<?php echo (!empty($meeting)) ? $tags : "" ?>"  />
            </div>

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

            <div class="form-group">
              <small><span class="show-hide-toggle" style="cursor:pointer;text-decoration:underline"><p>Add Optional and CC</p></span></small>
            </div>

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

            <div class="form-group" style="">
              <button type="button" class="btn btn-primary pull-right btn-sm btn-save-meeting"><i class="fa fa-floppy-o"></i> Save Meeting</button>
            </div>

          <?php echo form_close() ;?>

        </div>
      </div>
    </div>  
  </div>


  <div class="col-sm-2 padding-removed">
    <div class="parking-lot-panel">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>
            Parking Lot
            <span class="pull-right">
                <a href="#" class="panel-minimize" id=""><i class="fa fa-minus"></i></a>
            </span>
          </h4>
        </div>
        <div class="panel-body" id="panel-minimize-parking-lot-content">
          <div class="parking-lot-content">
            <table class="table table-striped" id="topic-parkinglot-container">
                <tbody>
                  <tr class="parking-lot-guide"><a>What is a Parking Lot and how does it work?</a></tr>
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
                      <p class="empty-parking-lot">No Parking Lot Items</p>
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

</div>



<div class="col-sm-9">

<div class="col-sm-12  padding-removed" style="padding-right:0px">
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
                ?>
                <?php if(!empty($topics)) :?>
                  <?php foreach($topics as $topic) : ?>
                    <li class="list-group-item hasItems" id="item-<?php echo $topic['topic_id'] ?>">

                      <h3 class="topic-hover-header"><i class='fa fa-file-text-o'></i>&nbsp <?php echo $topic['topic_title'] ?> &nbsp 
                        <div class="topic-toolbars">
                          <i class="fa fa-pencil edit-topic-link" style="font-size:14px;cursor:pointer" data-toggle='tooltip' data-placement='bottom' title='Edit Topic' data-edit-topic-id="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                          <i class="fa fa-trash delete-topic-link" style="font-size:14px;cursor:pointer" data-toggle='tooltip' data-placement='bottom' title='Delete Topic' data-delete-topic-id="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                          <i class="fa fa-plus show-saveas-actions" style="font-size:14px;cursor:pointer" data-toggle='tooltip' data-placement='bottom' title='Add note, task or decision' data-topic-cont-id="<?php echo $topic['topic_id'] ?>"></i> &nbsp 
                          <i class="fa fa-share move-to-parkinglot" style="font-size:14px;cursor:pointer" data-toggle='tooltip' data-placement='bottom' title='Move to Parking Lot' data-parkinglot-topic-id="<?php echo $topic['topic_id'] ?>" data-parkinglot-meeting-id="<?php echo $last_created_meeting_id ?>" ></i>
                        </div>
                      </h3>
                        
                      <?php if($topic['presenter'] != 0):?>
                        <a href="javascript:void(0)"><?php echo user_info("first_name", $topic['presenter'])." ".user_info("last_name", $topic['presenter'])." - ".$topic['time'] ?></a><br/>
                      <?php endif;?>
                      <br/>

                      <div class="view-topic-ntd">
                        <?php echo get_topic_ntd($topic['topic_id']) ?>
                        <!-- <a href="javascript:void(0)" class="show-topic-ntd" data-topic-ntd-id="<?php echo $topic['topic_id'] ?>"><i class="fa fa-eye"></i> View Notes, Decision or Task</a> -->
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
                                        
                                        <button type="button" class="btn btn-primary btn-sm btn-save-saveas-actions" data-topic-id="<?php echo $topic['topic_id'] ?>"><i class="fa fa-floppy-o"></i> Save</button>
                                        <button type="button" class="btn btn-danger btn-sm btn-close-saveas-actions" data-close-topic-id-cont="<?php echo $topic['topic_id'] ?>"><i class="fa fa-times"></i> Close</button>
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
                      <?php echo list_subtopics($topic['topic_id']) ?>
                        <!-- Subtopics will come out here via ajax request -->
                      

                      <br />                    

                      <button type="button" class="btn btn-success btn-xs show-topic" id="show-subtopic-field<?php echo $topic_count ?>" style='margin-left:70px'><i class="fa fa-plus"></i> Add subtopic</button>
                        <div class="subtopic-form-cont<?php echo $topic_count ?>" style="padding-left:5px;margin-top:10px;display:none;margin-left:65px">
                          
                          <?php echo form_open("", array("id"=>"save-subtopic".$topic['topic_id']."")) ;?>
                            <input type="hidden" name="topic_id" value="<?php echo $topic['topic_id'] ?>" />
                            <input type="text" name="subtopic_title" class="form-control" placeholder="Enter title for subtopic" name="subtopic_title" />
                            <p class="pull-right" style="margin-top:5px">
                              <button type="button" class="btn btn-primary btn-xs btn-save-subtopic" data-topic-id="<?php echo $topic['topic_id'] ?>"><i class="fa fa-floppy-o"></i> Save</button>
                              <a href="javascript:void(0)" class="btn-hide-subtopic" id="<?php echo $topic_count ?>">Cancel</a>
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
              
              <?php echo form_open("", array("id"=>"save_created_topic")) ;?>
                
                <input type="hidden" name="meeting_id" value="<?php echo $last_created_meeting_id ?>" />

                <label>Topic Title</label>  
                <input class="form-control" placeholder="Enter topic title" name="topic_title" />
                <br />

                <div class="options-toolbar">
                  <div class="form-group">
                    <label>Presenter</label>  
                    <select class="form-control chosen-select" name="presenter" >
                      <option value="">[-- Select Presenter (optional) --]</option>
                      <?php if(!empty($all_emails)):?>
                          <?php foreach($all_emails as $all_email) :?>
                              <option value="<?php echo $all_email['user_id'] ?>"><?php echo $all_email['email'] ?></option>
                          <?php endforeach;?>
                      <?php endif;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Duration</label>  
                    <input class="form-control" placeholder="e.g 15m, 30m, 1h, 2h" name="time" />
                  </div>
                  <div class="form-group">
                    <button type="button" class="btn btn-success btn-sm btn-save-topic"><i class="fa fa-floppy-o"></i> Save</button>
                    <button type="button" class="btn btn-danger btn-sm toggle-hide-topic-cont"><i class="fa fa-times"></i> Cancel</button>
                  </div>
                </div>

              <?php echo form_close() ;?>

            </div>

            <div class="form-group">
              <button class="btn btn-primary btn-md pull-left btn-add-topic" <?php echo (!empty($last_created_meeting_id)) ? "" : "disabled data-toggle='tooltip' data-placement='bottom' title='Save meeting first' " ?> ><i class="fa fa-plus"></i> Add Topic</button>
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
    $('#date-time-picker-from-date').datetimepicker({
       useCurrent: false, //Important! See issue #1075
       format: "DD/MM/YYYY h:mm A"
    });

    $('#date-time-picker-to-date').datetimepicker({
        useCurrent: false,
        format: "DD/MM/YYYY h:mm A"
        // minDate: new Date()
    });

    /** Multiple choices for category in add product **/
    var config = {
          '.chosen-select'    : {max_selected_options: 4, placeholder_text_multiple: "Click to select"},
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
      if($target.is(':visible')) { $('i',$(this)).removeClass('fa-minus').addClass('fa-plus'); }
      else { $('i',$(this)).removeClass('fa-plus').addClass('fa-minus'); }
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

  });
</script>
