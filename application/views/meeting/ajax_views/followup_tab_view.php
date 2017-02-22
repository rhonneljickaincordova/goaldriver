<link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/jquery-confirm/dist/jquery-confirm.min.css" />

<script src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
<script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>public/script.js"></script>
<script src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
<script src="<?php echo base_url() ?>public/chosen/chosen.jquery.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-confirm/dist/jquery-confirm.min.js"></script>

<style type="text/css">
  .chosen-container
  {
    margin-bottom:10px;
  }
  .modal-header .close 
  {
    font-size:40px;
    margin-top: -10px !important;
  }
  .topics-label
  {
    font-size:13px;
  }
</style>


<?php echo form_open("", array("id"=>"agenda_create_new_meeting")) ;?>
  
  <p>Information from this meeting will be copied to the follow-up meeting.</p>

  <?php if(!empty($topics)) :?>
    <div class="form-group">
      <!-- <a href="javascript:void(0)" id="select-all-topics">Select all topics</a> -->
    </div>
  <?php else:?>
    <p>No topic(s) to select.</p>
  <?php endif;?>

      <?php if(!empty($topics)) :?>
        <?php foreach($topics as $topic) :?>

              <div class="form-group">
                <label class="topics-label">
                  <input type="hidden" name="topics[]" value="<?php echo $topic['topic_id'] ?>" />
                  <input class="topic-checkbox" type="checkbox" value="<?php echo $topic['topic_id'] ?>" checked disabled>
                  &nbsp;<span><?php echo $topic['topic_title'] ?></span>
                </label>
              </div>

        <?php endforeach ;?>
      <?php endif;?>

      <?php if(!empty($topics)) :?>
      <div class="form-group">
          <button type="button" class="btn btn-primary btn-create-new-meeting-agenda pull-right" data-organ-id="<?php echo encrypt($this->session->userdata('organ_id')) ?>" data-meeting-id="<?php echo $meeting_id ?>">Follow-up Meeting</button>
      </div>
      <?php endif;?>

<?php echo form_close() ;?>

<script type="text/javascript">
  $(document).ready(function(){
    $('#select-all-topics').bind('click', function(){
      $('.topic-checkbox').prop('checked', true);
    });
  });
</script>