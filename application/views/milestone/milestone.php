<?php $this->load->view('includes/header'); ?>

<div class="bg-white-wrapper">
<div ng-controller="milestoneupdateCtrl" ng-init="get_milestone('<?php echo site_url(); ?>','<?php echo $milestone_id;?>')">
  <div class="modal-body">

           <div class="form-group">
            <label>Owner</label>  <br/>
            <ui-select ng-model="_owner" on-select="onChangeOwner($select.selected.user_id)"  >
              <ui-select-match>
                <span ng-bind="$select.selected.first_name"></span>
                <span ng-bind="$select.selected.last_name"></span>
              </ui-select-match>
              <ui-select-choices repeat="person.id as person in user | filter: first_name: $select.search">
                <div ng-bind-html="person.first_name +' '+ person.last_name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div> 
          <div class="form-group" >
              <label>Milestone Name</label>  <br/>
                  <input class="form-control" placeholder="Milestone Name" name="meeting_tags" ng-model="name" />
          </div>

              <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea id="description" class="form-control" name="description"  ng-model="description" ></textarea>
          </div>
         <div class="form-group">
            <div class="row">
                <div class='col-sm-12'>
                    <label>Due Date</label>  <br/>
                    <input type='text' class="form-control" id='datetimepicker5' />
                </div>
                <script type="text/javascript">
                    $(function () {
                        $('#datetimepicker5').datetimepicker();
                    });
                </script>
            </div>
        </div>

              </div>
              <div class="modal-footer">
                <button type="button" id="create-team" class="btn btn-primary" ng-click="savemilestone('<?php echo site_url(); ?>','<?php echo $milestone_id;?>')">Save </button>
            </div>
</div>
</div> <!-- .bg-white-wrapper -->
<?php $this->load->view('includes/footer'); ?>

<script  type="text/javascript" src="<?php echo base_url(); ?>asset/milestone.js"></script>
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