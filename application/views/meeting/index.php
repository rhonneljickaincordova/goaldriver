<?php $this->load->view("includes/header") ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>public/dataTables.bootstrap.css">

<style type="text/css">
    .content-container
    {
        margin-top:20px;
    }
    
    .btn-create-meeting
    {
        color:#FFFFFF !important;
    }

    #upcoming_meetings_table_filter > label
    {
        position: absolute;
        top: -40px;
        left: 59.5%;
    }

    #past_meetings_table_filter > label
    {
        position: absolute;
        top: -40px;
        left: 59.5%;
    }
    
    #upload_logo_image
    {
        font-size:18px;
    }
    
    
   
</style>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />
<script type="text/javascript">
    $(document).ready(function(){
        var rights = $('.check_rights').val();

        if(rights == "disabled")
        {
            $('.btn-toolbar a').remove();
            $('.actions-toolbar a').remove();
            $('.action_header').remove();
            $('a.user_actions').remove();
            $('td > a').removeAttr('href');
        }
    
    });
</script>

<?php 
    $user_id = $this->session->userdata('user_id');
?>

<!-- Listing of meetings are according to user_id and organ_id. If the meeting created do not belong to the organ_id, it will not be visible -->

<div class="bg-white-wrapper clearfix">

    <div class="col-sm-12">
        <div id="meetings-header">
            <div class="btn-toolbar">
                <a href="<?php echo base_url() ?>index.php/meeting/workspace" class="btn btn-primary btn-create-meeting"><i class="fa fa-plus"></i> New Meeting</a>  
                <a href="javascript:void(0)" id="upload_logo_image" class="pull-right"><i class="fa fa-cog" data-toggle='tooltip' data-placement='bottom' title='Settings'></i> </a>
            </div>

        </div>
    </div>
    <div class="clearfix"></div>

    <div class="content-container">
        
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li  class="active"><a data-toggle="tab" href="#upcoming_meetings"> Upcoming Meetings</a></li>  
                <li><a data-toggle="tab" href="#past_meetings"> Past Meetings</a></li>
            </ul>
            <div class="tab-content">
                <!-- Table for upcoming meetings -->
                <div id="upcoming_meetings" class="tab-pane fade in active">
                    
                    <table id="upcoming_meetings_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Creator</th>
                                <th>Location</th>
                                <th class="action_header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php
                                $upcomings = get_upcoming_meetings();
                            ?>

                            <?php if(!empty($upcomings)) :?>
                                <?php foreach($upcomings as $upcoming) :?>
                                    <?php 
                                       $meeting_lists = load_upcoming_meetings($upcoming['meeting_id']);
                                    ?>

                                    <?php if(!empty($meeting_lists)) :?>
                                        <tr>
                                            <?php 
                                                $is_followup = "";

                                                if($meeting_lists->is_followup == 1)
                                                {
                                                    $is_followup = "?followup=yes";
                                                }
                                            ?>

                                            <td><?php echo $meeting_lists->when_from_date ?></td>
                                            <td><input type="text" class="form-control inline-edit-title" data-meeting-id="<?php echo $meeting_lists->meeting_id ?>" value="<?php echo $meeting_lists->meeting_title ?>" /> <a href="<?php echo base_url("index.php/meeting/workspace/".encrypt($meeting_lists->meeting_id).'/'.encrypt($meeting_lists->organ_id).$is_followup) ?>" data-toggle="tooltip" data-placement="bottom" title="View / Edit"><i class="fa fa-pencil-square-o"></i> </td> 
                                            <td><?php echo user_info("first_name", $meeting_lists->user_id)." ".user_info("last_name", $meeting_lists->user_id) ?></td>
                                            <td><input type="text" class="form-control inline-edit-location" data-meeting-id="<?php echo $meeting_lists->meeting_id ?>" value="<?php echo $meeting_lists->meeting_location ?>" /></td>
                                            <td>
                                                <div class="actions-toolbar">
                                                    <a href="<?php echo base_url("index.php/meeting/workspace/".encrypt($meeting_lists->meeting_id).'/'.encrypt($meeting_lists->organ_id).$is_followup) ?>" class="btn-view-meeting-info" data-id="<?php echo $meeting_lists->meeting_id ?>" data-toggle="tooltip" data-placement="bottom" title="View / Edit"><i class="fa fa-pencil-square-o"></i></a>
                                                    &nbsp
                                                    <a href="javascript:void(0)" class="btn-delete-meeting" data-id="<?php echo $meeting_lists->meeting_id ?>" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif;?>

                                <?php endforeach ;?>
                            <?php endif;?>

                        </tbody>
                    </table>
                    <!-- END -->
                </div>

                
                <!-- Table for past meetings -->
                <div id="past_meetings" class="tab-pane fade">
                    <table id="past_meetings_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Creator</th>
                                <th>Location</th>
                                <th class="action_header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                                $pasts = get_past_meetings();
                            ?>

                            <?php if(!empty($pasts)) :?>
                                <?php foreach($pasts as $past) :?>
                                    <?php 
                                        $meeting_lists = load_past_meetings($past['meeting_id']);
                                    ?>

                                    <?php if(!empty($meeting_lists)) : ?>
                                        <tr>
                                            <?php 
                                                $is_followup = "";

                                                if($meeting_lists->is_followup == 1)
                                                {
                                                    $is_followup = "?followup=yes";
                                                }

                                            ?>
                                            <td><?php echo $meeting_lists->when_from_date ?></td>
                                            <td><input type="text" class="form-control inline-edit-title" data-meeting-id="<?php echo $meeting_lists->meeting_id ?>" value="<?php echo $meeting_lists->meeting_title ?>" /> <a href="<?php echo base_url("index.php/meeting/workspace/".encrypt($meeting_lists->meeting_id).'/'.encrypt($meeting_lists->organ_id).$is_followup) ?>" data-toggle="tooltip" data-placement="bottom" title="View / Edit"><i class="fa fa-pencil-square-o"></i></a></td> 
                                            <td><?php echo user_info("first_name", $meeting_lists->user_id)." ".user_info("last_name", $meeting_lists->user_id) ?></td>
                                            <td><input type="text" class="form-control inline-edit-location" data-meeting-id="<?php echo $meeting_lists->meeting_id ?>" value="<?php echo $meeting_lists->meeting_location ?>" /></td>
                                            <td>
                                                <div class="actions-toolbar">
                                                    <a href="<?php echo base_url("index.php/meeting/workspace/".encrypt($meeting_lists->meeting_id).'/'.encrypt($meeting_lists->organ_id).$is_followup) ?>" class="btn-view-meeting-info" data-id="<?php echo $meeting_lists->meeting_id ?>" data-toggle="tooltip" data-placement="bottom" title="View / Edit"><i class="fa fa-pencil-square-o"></i></a>
                                                    &nbsp
                                                    <a href="javascript:void(0)" class="btn-delete-meeting" data-id="<?php echo $meeting_lists->meeting_id ?>" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif;?>

                                <?php endforeach;?>
                            <?php endif;?>

                        </tbody>
                    </table>
                    <!-- END -->
                </div>
            </div>
        </div>
    </div>

</div>

<?php $this->load->view("includes/footer") ?>

<script src="<?php echo base_url() ?>public/dataTables.bootstrap.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function() {
        
        $('#past_meetings_table').DataTable({
            "oLanguage": 
            {
                "sEmptyTable": "You have no scheduled meetings. Click new to get started.",
            },
            "bInfo" : false,
            "bLengthChange"  : false,
        });

        $('#upcoming_meetings_table').DataTable({
            "oLanguage": 
            {
                "sEmptyTable": "You have no scheduled meetings. Click new to get started.",
            },
            "bInfo" : false,
            "bLengthChange"  : false,
        });

        $('.inline-edit-location').bind('blur', function(){
            var meeting_id = $(this).attr('data-meeting-id');
            var location = $(this).val();

            $.ajax({
                type:"POST",
                url:base_url+"index.php/meeting/inline_edit_location",
                data: { meeting_id: meeting_id, location: location, csrf_gd: Cookies.get('csrf_gd') },
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
            });
        });


        $('.inline-edit-title').bind('blur', function(){
            var meeting_id = $(this).attr('data-meeting-id');
            var title = $(this).val();

            $.ajax({
                type:"POST",
                url:base_url+"index.php/meeting/inline_edit_title",
                data: { meeting_id: meeting_id, title: title, csrf_gd: Cookies.get('csrf_gd') },
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
            });
        });
        

    } );
</script>