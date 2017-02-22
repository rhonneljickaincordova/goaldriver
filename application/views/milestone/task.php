<?php $this->load->view('includes/header'); ?>

<div class="bg-white-wrapper">
<div ng-controller="taskupdateCtrl" ng-init="get_task('<?php echo site_url(); ?>','<?php echo $task_id;?>')">

  <div class="form-group">
    <label>Milestone <small>(required)</small></label>  
    <input class="form-control" placeholder="Milestone" name="meeting_title" ng-model="milestone_task" readonly/>
  </div>


  <div class="form-group">
      <label>Owner</label>  <br/>
    <ui-select ng-model="person._user_id" on-select="onChangeOwner($select.selected.user_id)">
    <ui-select-match>
        <span ng-bind="$select.selected.first_name"></span>
        <span ng-bind="$select.selected.last_name"></span>
    </ui-select-match>
      <ui-select-choices repeat="person.id as person in user | filter: first_name: $select.search">
        <div ng-bind-html="person.first_name +' '+ person.last_name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
 </div> 
  <div class="form-group">
    <label>Who else</label><br/>
    <ui-select multiple ng-model="_multipleUser" on-select="onChangeParticipant(_multipleUser.participant)">
      <ui-select-match>{{$item.first_name}} {{$item.last_name}}</ui-select-match>
        <ui-select-choices repeat="person in participant | filter: first_name: $select.search">
        <div ng-bind-html="person.first_name +' '+ person.last_name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>

  <div class="form-group" >
    <label>Task Name</label>  <br/>
    <input class="form-control" placeholder="Enter Task Name" name="meeting_tags" ng-model="name_task" />
  </div>
  <div class="form-group">
    <label for="description" class="control-label">Description</label>
    <textarea id="description" class="form-control" name="description" ng-model="description_task"></textarea>
  </div>
<div class="form-group">
            <div class="row">
                <div class='col-sm-12'>
                    <label>Due Date</label>  <br/>
                    <input type='text' class="form-control" id='datetimepicker4' />
                </div>
                <script type="text/javascript">
                    $(function () {
                        $('#datetimepicker4').datetimepicker();
                    });
                </script>
            </div>
        </div>
        
  
  <div class="form-group">
        <label>Priority</label>  <br/>
      <ui-select ng-model="_priority" on-select="onChangePriority($select.selected.id)">
      <ui-select-match>
          <span ng-bind="$select.selected.name"></span>
      </ui-select-match>
        <ui-select-choices repeat="person.id as person in priorityArray | filter: name: $select.search">
          <span ng-bind="person.name"></span>
        </ui-select-choices>
      </ui-select>
   </div> 

<div class="form-group">
        <label>Status </label>  <br/>
      <ui-select ng-model="_status" on-select="onChangeStatus($select.selected.id)">
      <ui-select-match>
          <span ng-bind="$select.selected.name"></span>
      </ui-select-match>
        <ui-select-choices repeat="person.id as person in statusArray | filter: name: $select.search">
          <span ng-bind="person.name"></span>
        </ui-select-choices>
      </ui-select>
   </div> 

  <div class="modal-footer">
    <button type="button" id="create-team" class="btn btn-primary" ng-click="savetask('<?php echo site_url(); ?>','<?php echo $task_id;?>')">Save </button>
  </div>

</div>
</div> <!-- .bg-white-wrapper -->
<?php $this->load->view('includes/footer'); ?>
<script  type="text/javascript" src="<?php echo base_url();?>asset/updatetask.js"></script>
<script type="text/javascript">
  $(document).ready(function() 
  {
    $('#date-time-picker-from-date').datetimepicker({
       useCurrent: false //Important! See issue #1075
    });

    $('#date-time-picker-to-date').datetimepicker({
        useCurrent: false, //Important! See issue #1075
        // minDate: new Date()
    });
 
  });
</script>