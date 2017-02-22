<?php
function enable_chat(){
    return false;
}
function user_info($field, $user_id){
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select($field);
    $query = $ci->db->get_where('users', array('user_id' => $user_id), null, null);
    if($query->num_rows() > 0){
        $row = $query->row();
        return $row->$field;
    }else{
        return false;
    }
}

function team_info($field, $team_id){
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select($field);
    $query = $ci->db->get_where('team', array('team_id' => $team_id), null, null);

    $row = $query->row();
    return $row->$field;
}

function organ_info($field, $organ_id){
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select($field);
    $query = $ci->db->get_where('organisation', array('organ_id' => $organ_id), null, null);
    if($query->num_rows() > 0){
        $row = $query->row();
        return $row->$field;
    }else{
        return false;
    }

}

function show_attendee_status($meeting_id, $email)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('acceptance_status');
    $ci->db->from('meeting_attendees');
    $ci->db->where('meeting_id', $meeting_id);
    $ci->db->where_in('email', $email);
    $query = $ci->db->get()->result_array();

    if(!empty($query))
    {
        return $query;
    }
    /*
    $ci->db->select($field);
    $query = $ci->db->get_where('meeting_attendees', array('email' => $email), null, null);
    if($query->num_rows() > 0)
    {
        $row = $query->row();
        return $row->$field;
    }
    else
    {
        return false;
    }
    */
}

function get_feedback_status()
{
    $ci=& get_instance();
    $ci->load->database();

    $feedback_status = $ci->session->userdata('feedback_status'); //session for feedback status, default is "";

    if($feedback_status == "")
    {
        $ci->load->model('Feedback_model');
        $query_status = $ci->Feedback_model->get_status();

        if(!empty($query_status))
        {
            $status = $query_status[0]->status_id;
            
            $feedback_status = $ci->session->set_userdata('feedback_status', $status);
        }
    }

    return $feedback_status;
}

function get_organ_logo($organ_id)
{
  $ci=& get_instance();
  $ci->load->database();

  $ci->db->select('*');
  $ci->db->from('documents');
  $ci->db->where('organ_id', $organ_id);
  $ci->db->limit(1);

  $query = $ci->db->get()->result_array();

  return $query;
}

function is_organisation_owner($organ_id)
{
    $ci=& get_instance();
    
    $owner_id = organ_info("owner_id", $organ_id);
    $user_id = $ci->session->userdata("user_id");

    if($owner_id == $user_id)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function get_org_last_logged_in($organ_id = 0)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('organisation_users');
    $ci->db->where('organ_id', $organ_id);
    $ci->db->order_by('last_logged_in', 'DESC');
    $ci->db->limit(1);

    $query = $ci->db->get()->result();

    return $query;
}

function profile_pic($user_id, $profile_pic, $thumb=false){
    if($thumb){
        return base_url().'uploads/'.$user_id.'/thumb/'.$profile_pic;
    }
    return base_url().'uploads/'.$user_id.'/'.$profile_pic;
}

//print_r with <pre> already - ted
function print_me($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


function check_garbage($string="")
{
    if(empty($string))
    {
        return "";
    }
    $english = array();
    foreach (str_split('az019AZ~~~!@#$%^*()_+|}?><": Iñtërnâtiônàlizætiøn') as $char)
    {
        $english[] = ord($char);
    }
    $nonsense = array();
    foreach (str_split($string) as $char)
    {
      $nonsense[] = ord($char);
    }
    if(max($nonsense)>max($english))
    {
        $string = "";
    }
    return $string;
}

/*
/ ------------------------------------------------------------------------------
/ ENCRYPT
/ ------------------------------------------------------------------------------
/ It is dependent on the encrypt library of codeigniter
/ $this->encrypt
/
*/
function encrypt($text="")
{
    $ci =& get_instance();
    $response = $ci->encryption_lib->encode($text);
    return $response;
}

/*
/ ------------------------------------------------------------------------------
/   DECRYPT
/ ------------------------------------------------------------------------------
/ Dependent on encrypt decode library
*/
function decrypt($text="")
{
    $ci =& get_instance();
    $response = "";
    try
    {
        $response   = $ci->encryption_lib->decode($text);
        $response   = check_garbage($response);
    }
    catch(Exception $err)
    {
    }

    return $response;
}



//Custom Encryption helper - ted
function get_hash($id){
    return substr(md5($id.'moreplannerv1'),0,5);
}

function get_code($id){
    return $id.'-'.get_hash($id);
}

function check_code($code){

    $temp = explode('-', $code);
    if(count($temp)!=2) return false;
    $lead_id = $temp[0];
    $checker = $temp[1];
    if(!is_numeric($lead_id)) return false;
    return (get_hash($lead_id) == $checker);
}

function get_id_from_code($code='')
{
    $temp = explode('-', $code);
    return $temp[0];
}

function show_404_page($page = ''){ // page filename

    header("HTTP/1.1 404 Not Found");
    $heading = "404 Page Not Found";
    $message = "The page you requested was not found ";
    $CI =& get_instance();
    $CI->load->view($page);
    
}

function show_no_access_page($page = ''){ // page filename
    $CI =& get_instance();
    $CI->load->view($page);
    
}

function meeting_info($field, $meeting_id){
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select($field);
    $query = $ci->db->get_where('meeting', array('meeting_id' => $meeting_id), null, null);

    $row = $query->row();
    if(!empty($row))
    {
        return $row->$field;
    }
}

function check_hidden_permission($user_id, $org_id, $tab_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('permissions');
    $ci->db->where('user_id', $user_id);
    $ci->db->where('organ_id', $org_id);
    $ci->db->where('tab_id', $tab_id);

    $query = $ci->db->get()->result_array();

    return $query;
}

function check_readonly_permission($user_id, $org_id, $tab_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('permissions');
    $ci->db->where('user_id', $user_id);
    $ci->db->where('organ_id', $org_id);
    $ci->db->where('tab_id', $tab_id);

    $query = $ci->db->get()->result_array();

    return $query;

}

function check_readwrite_permission($user_id, $org_id, $tab_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('permissions');
    $ci->db->where('user_id', $user_id);
    $ci->db->where('organ_id', $org_id);
    $ci->db->where('tab_id', $tab_id);

    $query = $ci->db->get()->result_array();

    return $query;
}

function check_access($user_id, $org_id, $tab_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $query = null; //emptying in case

    $query = $ci->db->get_where('permissions', array(//making selection
        'user_id'   => $user_id,
        'organ_id'  => $org_id,
        'tab_id'    => $tab_id,
    ))->result_array();

    return $query;

}

function has_hidden_rights($user_id, $org_id, $tab_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('permissions');
    $ci->db->where('user_id', $user_id);
    $ci->db->where('organ_id', $org_id);
    $ci->db->where('tab_id', $tab_id);

    $query = $ci->db->get()->result_array();

    if(!empty($query))
    {
        return $query[0]['hidden'];
    }
}

function check_task_status($ntd_id, $linked_id=0)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('status');
    $ci->db->from('tasks');

    if($linked_id == 0)
    {
        $ci->db->where('ntd_id', $ntd_id);
    }
    else
    {
        $ci->db->where('ntd_id', $linked_id);
    }

    $query = $ci->db->get()->result_array();

    if(!empty($query))
    {
        return $query[0]['status'];
    }
}

function get_topic_ntd($topic_id, $is_followup='')
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('meeting_note');
    $ci->db->where('meeting_topic_id', $topic_id);
    $ci->db->where('meeting_subtopic_id', NULL);
    $ci->db->order_by('position', 'ASC');

    $query = $ci->db->get()->result_array();

    $html = "";
    $label = "";
    $checkbox = "";
    $task_assign = "";
    $style = "";
    $assignees = "";
    $due = "";

    $task_status = 0;
    $task_completed = "";

    $counter = 0;

    foreach($query as $res)
    {
        $task_status = check_task_status($res['id'], $res['note_id_linked']);

        if($res['type'] == 1)
        {
            $label = "Note";
            $checkbox = "";
            $task_assign = "";
            $style = "height:auto;word-wrap:break-word";
        }
        if($res['type'] == 2)
        {
            $label = "Task";

            $task_id = 0;

            if($is_followup != "")
            {
                $task_id = $res['note_id_linked'];
            }
            else
            {
                $task_id = $res['id'];
            }
            
            $checkbox = (!empty($task_status) && $task_status == 10) ? "<input type='checkbox' checked data-id='".$task_id."' data-toggle='tooltip' title='Completed' data-placement='bottom' class='mark_complete_task' style='cursor:pointer;' />&nbsp" : "<input type='checkbox' data-id='".$task_id."' data-toggle='tooltip' title='Mark as complete' data-placement='bottom' class='mark_complete_task' style='cursor:pointer;' />&nbsp" ;
           
            $task_assign = "";
            $task_assign = "<a href='#' class='show-task-actions' data-toggle='popover' data-placement='bottom' data-content='' data-ntd-value='".$res['text']."' data-ntd-id='".$task_id."' data-topic-id='".$topic_id."'><i class='fa fa-user'></i> Edit task info</a>";
            $style = "height:auto;word-wrap:break-word";
        }
        if($res['type'] == 3)
        {
            $label = "Decision";
            $checkbox = "";
            $task_assign = "";
            $style = "height:auto;word-wrap:break-word";
        }

        $html .= "<li class='list-group-item padding-removed ntd-topic-list-parent-cont' id='item-".$res['id']."' style='border:0px !important'>";
        $html .= "<div class='content-item' style='".$style."'>".$checkbox." ";
        
        if($res['type'] != 2)
        {
            $html .= "<span class='item-label'>".$label."</span>: <span>".$res['text']."</span>";
            $html .= "<div class='topic-ntd-toolbars'>";
            $html .= "<i class='fa fa-trash-o pull-right btn-delete-topic-ntd' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Delete' data-delete-topic-ntd='".$res['id']."'></i> &nbsp <i class='fa fa-times fa-pencil pull-right btn-edit-topic-ntd' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Edit' data-edit-topic-ntd='".$res['id']."'></i> &nbsp";
            $html .= "<i class='fa fa-arrow-down pull-right topic_ntd_move_down' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Move down' data-ntd-meeting-id='".$res['meeting_id']."' data-topic-id='".$res['meeting_topic_id']."' data-position='".$res['position']."' data-noteid='".$res['id']."' data-topic-id='".$res['meeting_topic_id']."'></i> &nbsp";
            $html .= ($counter != 0) ? "<i class='fa fa-arrow-up pull-right topic_ntd_move_up' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Move up' data-ntd-meeting-id='".$res['meeting_id']."' data-topic-id='".$res['meeting_topic_id']."' data-position='".$res['position']."' data-noteid='".$res['id']."' data-topic-id='".$res['meeting_topic_id']."'></i>" : "";
            $html .= "</div>";
        }
        else
        {
            $html .= "<span class='item-label'>".$label."</span>: <span>".$res['text']."</span>";
            $html .= "<div class='topic-ntd-toolbars'>";
            $html .= "<i class='fa fa-trash-o pull-right btn-delete-topic-ntd' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Delete' data-delete-topic-ntd='".$res['id']."'></i> &nbsp";
            $html .= "<i class='fa fa-arrow-down pull-right topic_ntd_move_down' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Move down' data-ntd-meeting-id='".$res['meeting_id']."' data-topic-id='".$res['meeting_topic_id']."' data-position='".$res['position']."' data-noteid='".$res['id']."' data-topic-id='".$res['meeting_topic_id']."'></i> &nbsp";
            $html .= ($counter != 0) ? "<i class='fa fa-arrow-up pull-right topic_ntd_move_up' style='cursor:pointer !important;margin-top:5px;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Move up' data-ntd-meeting-id='".$res['meeting_id']."' data-topic-id='".$res['meeting_topic_id']."' data-position='".$res['position']."' data-noteid='".$res['id']."' data-topic-id='".$res['meeting_topic_id']."'></i>" : "";
            $html .= "</div>";
        }

        $html .= "<p style='margin-top:10px'>".$task_assign."</p>";
        $html .= "</div>";

        $html .= "<div class='topics-ntd-cont".$res['id']."' style='display:none'>";
        $html .= "<input type='hidden' name='meeting_id' value='".$res['id']."' />";

        $html .= "<textarea class='form-control topic-ntd-textarea-field".$res['id']."' name='text' placeholder='Put new text here...'></textarea>";
        $html .= '<div class="toolbar-actions">
                      <div class="controls-actions">
                          <div class="save-as-actions">
                              <button type="button" class="btn-sm btn btn-primary btn-save-updated-topic-ntd" data-id="'.$res['id'].'"> Save</button>
                              <button type="button" class="btn-sm btn btn-danger close-edit-topic-ntd" data-id="'.$res['id'].'"> Close</button>
                          </div>
                      </div>
                  </div>';
        $html .= "</div>";
        $html .= "</li> ";

        $counter++;
    }

    echo $html;

    //$ci->load->view('meeting/includes/needed_files');
}

function get_topic_ntd_pdf($topic_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('meeting_note');
    $ci->db->where('meeting_topic_id', $topic_id);
    $ci->db->where('meeting_subtopic_id', NULL);

    $query = $ci->db->get()->result_array();

    $html = "";
    $label = "";
    $checkbox = "";
    $task_assign = "";
    $style = "";
    $assignees = "";
    $due = "";

    foreach($query as $res)
    {
        $ci->db->select('*');
        $ci->db->from('meeting_note as mn');
        $ci->db->join('tasks as t', 'mn.id=t.ntd_id');
        $ci->db->where('mn.id', $res['id']);
        $ci->db->where('t.ntd_id', $res['id']);
        
        $q = $ci->db->get()->result_array();

        if($res['type'] == 1)
        {
            $label = "Note";
            $checkbox = "";
            $task_assign = "";
            $style = "height:40px";
        }
        if($res['type'] == 2)
        {
            $label = "Task";
            $checkbox = "";
            $task_assign = "";
            
            if(!empty($q))
            {
                $task = $q[0];

                //startDate
                $start_date = $task['task_startDate'];
                $datetime_string = $start_date;
                $date = strtok($datetime_string, " ");
                $format = str_replace('/', '-', $date);
                $formatted_date = date('d-m-Y', strtotime($format));

                //dueDate
                $due_date = $task['task_dueDate'];
                $due_datetime_string = $due_date;
                $date_due = strtok($due_datetime_string, " ");
                $due_format = str_replace('/', '-', $date_due);
                $formatted_due_date = date('d-m-Y', strtotime($due_format));

                //owner
                $owner = user_info("first_name", $task['owner_id'])." ".user_info("last_name", $task['owner_id']);

                //description
                $description = $task['task_description'];

                //priority
                if($task['priority'] == 0)
                {
                    $priority = "None";
                }
                elseif($task['priority'] == 1)
                {
                    $priority = "Low";
                }
                elseif($task['priority'] == 2)
                {
                    $priority = "Medium";
                }
                elseif($task['priority'] == 3)
                {
                    $priority = "High";
                }
                else
                {
                    $priority = "NA";
                }


                //status
                if($task['status'] == 0)
                {
                    $status = "0%";
                }
                elseif($task['status'] == 1)
                {
                    $status = "10%";
                }
                elseif($task['status'] == 2)
                {
                    $status = "20%";
                }
                elseif($task['status'] == 3)
                {
                    $status = "30%";
                }
                elseif($task['status'] == 4)
                {
                    $status = "40%";
                }
                elseif($task['status'] == 5)
                {
                    $status = "50%";
                }
                elseif($task['status'] == 6)
                {
                    $status = "60%";
                }
                elseif($task['status'] == 7)
                {
                    $status = "70%";
                }
                elseif($task['status'] == 8)
                {
                    $status = "80%";
                }
                elseif($task['status'] == 9)
                {
                    $status = "90%";
                }
                elseif($task['status'] == 10)
                {
                    $status = "100%";
                }
                else
                {
                    $status = "NA";
                }

            }
            
            $style = "height:55px";
        }
        if($res['type'] == 3)
        {
            $label = "Decision";
            $checkbox = "";
            $task_assign = "";
            $style = "height:40px";
        }

        $html .= "<div class='content-item' style=''>".$checkbox." ";

        if($res['type'] != 2)
        {
            $html .= "<span class='' style=''>" .$label."</span> :  <span class='item-content'>" .$res['text']."</span> ";
        }
        else
        {
            if(!empty($q))
            {
                $html .= "<span class='' style=''>" .$label."</span> :  <span class='item-content'>" .$res['text']."</span><br />";
                $html .= "<span class='item-label'>Description </span> : <span class='item-content'>".$description."</span><br />";
                $html .= "<span class='item-label'>Due Date </span> : <span class='item-content'>".$formatted_due_date."</span><br /> ";
                $html .= "<span class='item-label'>By Who </span> : <span class='item-content'>".$owner."</span><br /> ";
                $html .= "<span class='item-label'>Priority </span> : <span class='item-content'>".$priority."</span><br /> ";
                $html .= "<span class='item-label'>Status </span> : <span class='item-content'>".$status."</span><br /> ";
            }
            else
            {
                $html .= "<span class='' style=''>" .$label."</span> :  <span class='item-content'>" .$res['text']."</span>";
            }
        }

        $html .= "<p>".$task_assign."</p>";
        $html .= "</div>";
    }

    echo $html;

    //$ci->load->view('meeting/includes/needed_files');
}


function list_subtopics($topic_id, $is_followup='')
{
    $ci=& get_instance();
    $ci->load->database();
    $user_id = $ci->session->userdata("user_id");

    $ci->db->select('*');
    $ci->db->from('meeting_subtopics as ms');
    $ci->db->join('meeting_topics as mt','ms.topic_id=mt.topic_id', 'left');
    $ci->db->where('ms.topic_id', $topic_id);
    $ci->db->order_by('ms.s_position', 'ASC');

    $query = $ci->db->get()->result_array();

    $html = "";
    $count = "A";

    $counter = 0;

    $html .= "<ul id='subtopic-item-".$topic_id."' class='meeting_subtopic_list list-group'>";

    foreach($query as $res)
    {
        $html .= "<li id='item-".$res['subtopic_id']."' class='list-group-item subtopic-list-parent-cont'><h3 class='subtopic_title_heading'><i class='fa fa-file-o'></i>&nbsp ".$res['subtopic_title']."&nbsp";
        $html .= "<div class='subtopic-toolbars'>";
        $html .= ($counter != 0) ? "<i class='fa fa-arrow-up icon-toolbars s_move_up' style='font-size:14px;cursor:pointer;padding:3px' data-topicid='".$topic_id."' data-position='".$res['s_position']."' data-subtopicid='".$res['subtopic_id']."' data-toggle='tooltip' data-placement='bottom' title='Move up'></i> &nbsp" : ""; 
        $html .= "<i class='fa fa-arrow-down icon-toolbars s_move_down' style='font-size:14px;cursor:pointer;padding:3px' data-topicid='".$topic_id."' data-position='".$res['s_position']."' data-subtopicid='".$res['subtopic_id']."'  data-toggle='tooltip' data-placement='bottom' title='Move down'></i> &nbsp";  
        $html .= "<i class='fa fa-pencil edit-subtopic-link' style='font-size:14px;cursor:pointer;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Edit Subtopic' data-edit-subtopic-id='".$res['subtopic_id']."'></i>  &nbsp";
        $html .= "<i class='fa fa-trash delete-subtopic-link' style='font-size:14px;cursor:pointer;padding:3px' data-toggle='tooltip' data-placement='bottom' title='Delete Subtopic' data-delete-subtopic-id='".$res['subtopic_id']."'></i> </div> </h3>";
        $html .= "<input type='text' class='form-control edit-subtopic-title inline-edit-subtopic".$res['subtopic_id']."' data-subtopic-id='".$res['subtopic_id']."' style='width: 35%;margin-left: 9%; display:none' />";

        // $html .= "<div class='view-subtopic-ntd' style='margin-left:70px'> <a href='javascript:void(0)' class='show-subtopic-ntd' data-subtopic-ntd-id='".$res['subtopic_id']."'><i class='fa fa-eye'></i> View Notes, Decision or Task</a> </div>";


        /** QUERY for subtopics note, task or decision **/
        $ci->db->select('*');
        $ci->db->from('meeting_note');
        $ci->db->where('meeting_subtopic_id', $res['subtopic_id']);
        $ci->db->order_by('position','ASC');

        $query2 = $ci->db->get()->result_array();

        $html2 = "";
        $label = "";
        $checkbox = "";
        $task_assign = "";
        $style = "";
        $assignees = "";
        $due = "";

        $task_status = 0;
        $task_completed = "";

        $counter2 = 0;

        foreach($query2 as $q)
        {
            $task_status = check_task_status($q['id'], $q['note_id_linked']);

            if($q['type'] == 1)
            {
                $label = "Note";
                $checkbox = "";
                $task_assign = "";
                $style = "height:auto;word-wrap:break-word";
            }
            if($q['type'] == 2) 
            {
                $label = "Task";

                $task_id = 0;

                if($is_followup != "")
                {
                    $task_id = $q['note_id_linked'];
                }
                else
                {
                    $task_id = $q['id'];
                }
                
                $checkbox = (!empty($task_status) && $task_status == 10) ? "<input type='checkbox' checked data-id='".$task_id."' data-toggle='tooltip' title='Completed' data-placement='bottom' class='mark_complete_task' style='cursor:pointer;' />&nbsp" : "<input type='checkbox' data-id='".$task_id."' data-toggle='tooltip' title='Mark as complete' data-placement='bottom' class='mark_complete_task' style='cursor:pointer;' />&nbsp";
                $task_assign = "<a href='#' class='show-task-actions' data-toggle='popover' data-placement='bottom' data-content='' data-ntd-value='".$q['text']."' data-ntd-id='".$task_id."' data-subtopic-id='".$res['subtopic_id']."'><i class='fa fa-user'></i> Edit task info</a>";
                $style = "height:auto;word-wrap:break-word";
            }
            if($q['type'] == 3)
            {
                $label = "Decision";
                $checkbox = "";
                $task_assign = "";
                $style = "height:auto;word-wrap:break-word";
            }

            $html2 .= "<li class='list-group-item padding-removed ntd-subtopic-list-parent-cont' id='item-".$q['id']."' style='border:0px !important'> <div class='content-item' style='".$style."'>".$checkbox." ";
            
            if($q['type'] != 2)
            {
                $html2 .= "<span class='item-label'>".$label."</span>: <span>".$q['text']."</span>";
                $html2 .= "<div class='subtopic-note-toolbars'>";
                $html2 .= "<i class='fa fa-trash-o pull-right btn-delete-subtopic-ntd' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Delete' data-delete-subtopic-ntd='".$q['id']."'></i> &nbsp";
                $html2 .= "<i class='fa fa-pencil pull-right btn-edit-subtopic-ntd' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Edit' data-edit-subtopic-ntd='".$q['id']."'></i> &nbsp ";
                $html2 .= "<i class='fa fa-arrow-down pull-right subtopic_ntd_down' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Move down' data-ntd-meeting-id='".$q['meeting_id']."' data-subtopic-id='".$q['meeting_subtopic_id']."' data-position='".$q['position']."' data-noteid='".$q['id']."' data-topic-id='".$q['meeting_topic_id']."'></i> &nbsp";
                $html2 .= ($counter2 != 0) ? "<i class='fa fa-arrow-up pull-right subtopic_ntd_up' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Move up' data-ntd-meeting-id='".$q['meeting_id']."' data-subtopic-id='".$q['meeting_subtopic_id']."' data-position='".$q['position']."' data-noteid='".$q['id']."' data-topic-id='".$q['meeting_topic_id']."'></i>" : "";
                $html2 .= "</div>";
            }
            else
            {
                $html2 .= "<span class='item-label'>".$label."</span>: <span>".$q['text']."</span>";
                $html2 .= "<div class='subtopic-note-toolbars'>";
                $html2 .= "<i class='fa fa-trash-o pull-right btn-delete-subtopic-ntd' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Delete' data-delete-subtopic-ntd='".$q['id']."'></i> &nbsp";
                $html2 .= "<i class='fa fa-arrow-down pull-right subtopic_ntd_down' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Move down' data-ntd-meeting-id='".$q['meeting_id']."' data-subtopic-id='".$q['meeting_subtopic_id']."' data-position='".$q['position']."' data-noteid='".$q['id']."' data-topic-id='".$q['meeting_topic_id']."'></i> &nbsp";
                $html2 .= ($counter2 != 0) ? "<i class='fa fa-arrow-up pull-right subtopic_ntd_up' style='cursor:pointer !important;margin-top:5px;padding:5px' data-toggle='tooltip' data-placement='bottom' title='Move up' data-ntd-meeting-id='".$q['meeting_id']."' data-subtopic-id='".$q['meeting_subtopic_id']."' data-position='".$q['position']."' data-noteid='".$q['id']."' data-topic-id='".$q['meeting_topic_id']."'></i>" : "";
                $html2 .= "</div>";
            }

            $html2 .= "<p style='margin-top:10px'>".$task_assign."</p>";
            $html2 .= "</div>"; 

            $html2 .= "<div class='subtopics-ntd-cont".$q['id']."' style='display:none'>";
            $html2 .= "<input type='hidden' name='meeting_id' value='".$q['id']."' />";

            $html2 .= "<textarea class='form-control subtopic-ntd-textarea-field".$q['id']."' name='text' placeholder='Put new text here...'></textarea>";
            $html2 .= '<div class="toolbar-actions">
                          <div class="controls-actions">
                              <div class="save-as-actions">
                                  <button type="button" class="btn-sm btn btn-primary btn-save-updated-subtopic-ntd" data-id="'.$q['id'].'"> Save</button>
                                  <button type="button" class="btn-sm btn btn-danger close-edit-subtopic-ntd" data-id="'.$q['id'].'"> Close</button>
                              </div>
                          </div>
                      </div>';
            $html2 .= "</div>";


            $html2 .= "</li>";

            $counter2++;
        }

        $html .= "<div class='ntd-subtopic-container".$res['subtopic_id']."' style='height:auto;margin-left:55px;'><ul class='list-group meeting_subtopics_ntds'>".$html2."</ul></div>";

        $html .= "<div class='' style='padding-right:0px'> <div class='subtopic-items-cont-input".$res['subtopic_id']."'><input type='text' class='form-control subtopic-input-field' data-subtopic-input-field='".$res['subtopic_id']."' placeholder='Write note, task or decision' style='width:92%;margin-bottom:20px;margin-left:55px' /></div> <div class='subtopic-items-cont".$res['subtopic_id']."' style='display:none;margin-bottom:20px;margin-left:55px'>

                  <form method='POST' id='add-subtopic-note-task-decision".$res['subtopic_id']."'>

                  <input type='hidden' name='meeting_id' value='".$res['meeting_id']."' />
                  <input type='hidden' name='meeting_topic_id' value='".$res['topic_id']."' />
                  <input type='hidden' name='meeting_subtopic_id' value='".$res['subtopic_id']."' />
                  <input type='hidden' name='entered_by' value='".$user_id."' />

                  <textarea class='form-control subtopic-textarea-field".$res['subtopic_id']."' placeholder='Write note, decision or task' name='text'></textarea>
                  <div class='toolbar-actions'>
                      <div class='controls-actions'>

                        <div class='col-sm-12'>
                          <div class='save-as-actions'>
                            <p>Save As: &nbsp
                              <input type='radio' name='type' value='1' /> Note &nbsp
                              <input type='radio' name='type' value='2' /> Task &nbsp
                              <input type='radio' name='type' value='3' /> Decision &nbsp
                              <button type='button' class='btn btn-primary btn-sm btn-save-saveas-actions-subtopic' data-subtopic-id='".$res['subtopic_id']."'> Save</button>
                            </p>
                          </div>
                        </div>

                      </div>
                  </div>
                  </form>

                </div></div></li>";
        $count++;

        $counter++;
    }

    $html .= "</ul>";

    echo $html;
    //$ci->load->view('meeting/includes/needed_files_subtopic');
}

function get_subtopic_ntd($subtopic_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('meeting_note');
    $ci->db->where('meeting_subtopic_id', $subtopic_id);

    $query = $ci->db->get()->result_array();

    $html = "";
    $label = "";
    $checkbox = "";
    $task_assign = "";
    $style = "";
    $assignees = "";
    $due = "";

    foreach($query as $res)
    {
        if($res['type'] == 1)
        {
            $label = "Note";
            $checkbox = "";
            $task_assign = "";
            $style = "height:40px";
        }
        if($res['type'] == 2)
        {
            $label = "Task";
            $checkbox = "<input type='checkbox'>";
            $task_assign = "<a href='#' class='show-task-actions-subtopic' data-toggle='popover' data-placement='bottom' data-content='' data-subtopic-ntd-id='".$res['id']."' data-subtopic-id='".$subtopic_id."'><i class='fa fa-user'></i> Edit task info</a>";
            $style = "height:55px";
        }
        if($res['type'] == 3)
        {
            $label = "Decision";
            $checkbox = "";
            $task_assign = "";
            $style = "height:40px";
        }

        $html .= "<div class='content-item' style='".$style."'>".$checkbox." ";
        $html .= "<span class='item-label'>".$label."</span>: <span>".$res['text']."</span>";
        $html .= "<p>".$task_assign."</p>";
        $html .= "</div> <br/>";
    }

    echo $html;
}


function get_subtopic_ntd_pdf($subtopic_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('meeting_note');
    $ci->db->where('meeting_subtopic_id', $subtopic_id);

    $query = $ci->db->get()->result_array();

    $html = "";
    $label = "";
    $checkbox = "";
    $task_assign = "";
    $style = "";
    $assignees = "";
    $due = "";

    foreach($query as $res)
    {
        $ci->db->select('*');
        $ci->db->from('meeting_note as mn');
        $ci->db->join('tasks as t', 'mn.id=t.ntd_id');
        $ci->db->where('mn.id', $res['id']);
        $ci->db->where('t.ntd_id', $res['id']);
        
        $q = $ci->db->get()->result_array();

        if($res['type'] == 1)
        {
            $label = "Note";
            $checkbox = "";
            $task_assign = "";
            $style = "height:40px";
        }
        if($res['type'] == 2)
        {
            $label = "Task";
            $checkbox = "";
            $task_assign = "";
            
            if(!empty($q))
            {
                $task = $q[0];

                //startDate
                $start_date = $task['task_startDate'];
                $datetime_string = $start_date;
                $date = strtok($datetime_string, " ");
                $format = str_replace('/', '-', $date);
                $formatted_date = date('d-m-Y', strtotime($format));

                //dueDate
                $due_date = $task['task_dueDate'];
                $due_datetime_string = $due_date;
                $date_due = strtok($due_datetime_string, " ");
                $due_format = str_replace('/', '-', $date_due);
                $formatted_due_date = date('d-m-Y', strtotime($due_format));

                //owner
                $owner = user_info("first_name", $task['owner_id'])." ".user_info("last_name", $task['owner_id']);

                //description
                $description = $task['task_description'];

                //priority
                if($task['priority'] == 0)
                {
                    $priority = "None";
                }
                elseif($task['priority'] == 1)
                {
                    $priority = "Low";
                }
                elseif($task['priority'] == 2)
                {
                    $priority = "Medium";
                }
                elseif($task['priority'] == 3)
                {
                    $priority = "High";
                }
                else
                {
                    $priority = "NA";
                }


                //status
                if($task['status'] == 0)
                {
                    $status = "0%";
                }
                elseif($task['status'] == 1)
                {
                    $status = "10%";
                }
                elseif($task['status'] == 2)
                {
                    $status = "20%";
                }
                elseif($task['status'] == 3)
                {
                    $status = "30%";
                }
                elseif($task['status'] == 4)
                {
                    $status = "40%";
                }
                elseif($task['status'] == 5)
                {
                    $status = "50%";
                }
                elseif($task['status'] == 6)
                {
                    $status = "60%";
                }
                elseif($task['status'] == 7)
                {
                    $status = "70%";
                }
                elseif($task['status'] == 8)
                {
                    $status = "80%";
                }
                elseif($task['status'] == 9)
                {
                    $status = "90%";
                }
                elseif($task['status'] == 10)
                {
                    $status = "100%";
                }
                else
                {
                    $status = "NA";
                }

            }
            
            $style = "height:55px";
        }
        if($res['type'] == 3)
        {
            $label = "Decision";
            $checkbox = "";
            $task_assign = "";
            $style = "height:40px";
        }

        $html .= "<div class='content-item' style=''>".$checkbox." ";

        if($res['type'] != 2)
        {
            $html .= "<span class='' style='margin-left:5% !important'>" .$label."</span> :  <span class='item-content'>" .$res['text']."</span> ";
        }
        else
        {
            if(!empty($q))
            {
                $html .= "<span class='' style='margin-left:5% !important'>" .$label."</span> :  <span class='item-content'>" .$res['text']."</span><br />";
                $html .= "<span class='item-label'>Description </span> : <span class='item-content'>".$description."</span><br />";
                $html .= "<span class='item-label'>Due Date </span> : <span class='item-content'>".$formatted_due_date."</span><br /> ";
                $html .= "<span class='item-label'>By Who </span> : <span class='item-content'>".$owner."</span><br /> ";
                $html .= "<span class='item-label'>Priority </span> : <span class='item-content'>".$priority."</span><br /> ";
                $html .= "<span class='item-label'>Status </span> : <span class='item-content'>".$status."</span><br /> ";
            }
            else
            {
                $html .= "<span class='' style='margin-left:5% !important'>" .$label."</span> :  <span class='item-content'>" .$res['text']."</span>";
            }
        }

        $html .= "<p>".$task_assign."</p>";
        $html .= "</div>";
    }

    echo $html;
}


function check_if_meeting_participant($user_id, $meeting_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $session_userid = $ci->session->userdata('user_id');

    $ci->db->select('meeting_participants, user_id');
    $ci->db->from('meeting');
    $ci->db->where('meeting_id', $meeting_id);
    $query = $ci->db->get()->result_array();

    foreach($query as $row)
    {       
        $ids = unserialize($row['meeting_participants']);
        $creator_id = $row['user_id'];
       
        if(in_array($user_id, $ids) || $session_userid == $creator_id)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

}

function load_past_meetings($meeting_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $user_id = $ci->session->userdata("user_id");
    $organ_id = $ci->session->userdata("organ_id");

    $query = $ci->db->query("CALL past_meeting_load($user_id, $meeting_id, $organ_id)");
    $query->next_result();
    return ($query->num_rows() > 0) ?  $query->row() : false;  
}

function load_upcoming_meetings($meeting_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $user_id = $ci->session->userdata("user_id");
    $organ_id = $ci->session->userdata("organ_id");

    $query = $ci->db->query("CALL upcoming_meeting_load($user_id, $meeting_id, $organ_id)");
    $query->next_result();
    return ($query->num_rows() > 0) ?  $query->row() : false;  
}

function get_past_meetings()
{
    $ci=& get_instance();
    $ci->load->database();

    $user_id = $ci->session->userdata('user_id');
    $organ_id = $ci->session->userdata('organ_id');

    $ci->db->select('*');
    $ci->db->from('meeting');
    //$ci->db->where('user_id', $user_id);
    $ci->db->where('organ_id', $organ_id);
    $ci->db->order_by('meeting_id', 'DESC');

    $query = $ci->db->get()->result_array();

    foreach($query as $row)
    {
        $datetime = $row['when_from_date'];

        $datetime_string = $datetime;
        $date = strtok($datetime_string, " ");

        $format = str_replace('/', '-', $date);
        $formatted_date = date('Y-m-d', strtotime($format));

        $ci->db->select('*');
        $ci->db->from('meeting');
        //$ci->db->where('user_id', $user_id);
        $ci->db->where('organ_id', $organ_id);
        $ci->db->where('formatted_when_from_date <', date("Y-m-d"));
        $ci->db->order_by('meeting_id', 'DESC');

        $query2 = $ci->db->get()->result_array();

        return $query2;

    }
}

function get_upcoming_meetings()
{
    $ci=& get_instance();
    $ci->load->database();

    $user_id = $ci->session->userdata('user_id');
    $organ_id = $ci->session->userdata('organ_id');

    $ci->db->select('*');
    $ci->db->from('meeting');
    //$ci->db->where('user_id', $user_id);
    $ci->db->where('organ_id', $organ_id);
    $ci->db->order_by('meeting_id', 'DESC');

    $query = $ci->db->get()->result_array();

    foreach($query as $row)
    {
        $datetime = $row['when_from_date'];

        $datetime_string = $datetime;
        $date = strtok($datetime_string, " ");

        $format = str_replace('/', '-', $date);
        $formatted_date = date('Y-m-d', strtotime($format));

        $ci->db->select('*');
        $ci->db->from('meeting');
        //$ci->db->where('user_id', $user_id);
        $ci->db->where('organ_id', $organ_id);
        $ci->db->where('formatted_when_from_date >=', date("Y-m-d"));
        $ci->db->order_by('meeting_id', 'DESC');

        $query2 = $ci->db->get()->result_array();

        return $query2;

    }
}

function get_subtopics_for_email($topic_id)
{
    $ci=& get_instance();
    $ci->load->database();
    $user_id = $ci->session->userdata("user_id");

    $ci->db->select('*');
    $ci->db->from('meeting_subtopics as ms');
    $ci->db->join('meeting_topics as mt','ms.topic_id=mt.topic_id', 'left');
    $ci->db->where('ms.topic_id', $topic_id);
    $ci->db->order_by('ms.s_position', 'ASC');

    $query = $ci->db->get()->result_array();

    return $query;
}

function check_is_exists_in_task($ntd_id)
{
    $ci=& get_instance();
    $ci->load->database();

    $ci->db->select('*');
    $ci->db->from('tasks');
    $ci->db->where('ntd_id', $ntd_id);

    $query = $ci->db->get()->result_array();

    return $query;
}

function timeAgo($time_ago)
{
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        return "just now";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            return "one minute ago";
        }
        else{
            return "$minutes minutes ago";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            return "an hour ago";
        }else{
            return "$hours hrs ago";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            return "yesterday";
        }else{
            return "$days days ago";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            return "a week ago";
        }else{
            return "$weeks weeks ago";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            return "a month ago";
        }else{
            return "$months months ago";
        }
    }
    //Years
    else{
        if($years==1){
            return "one year ago";
        }else{
            return "$years years ago";
        }
    }
}

function is_account_owner($user_id){
    $ci=& get_instance();
    $ci->load->database();

    $query = $ci->db->query('select u.user_id, u.master_account_id, a.account_id, a.account_owner_id FROM users u, account a WHERE u.user_id = account_owner_id AND a.account_id = u.master_account_id AND u.user_id = '.$user_id);
    if($query->num_rows() > 0)
        return true;
    return false;
}

if(! function_exists('milestone_name'))
{
    function milestone_name($id)
    {
        $ci=& get_instance();
        $ci->load->database();

        $query = $ci->db->query("SELECT name FROM milestones WHERE id = {$id}");
        if($query->num_rows() > 0)
        {
            return $query->row()->name;
        }else{
            return "";
        }
    }
}

if(! function_exists('csrf_name')) {
    function csrf_name(){
        $ci=& get_instance();
        echo $ci->security->get_csrf_token_name();
    }
}

if(! function_exists('csrf_hash')) {
    function csrf_hash(){
        $ci=& get_instance();
        echo $ci->security->get_csrf_hash();
    }
}


if ( ! function_exists('timezone_menu_gd'))
    {
    function timezone_menu_gd($default = 'UTC', $class = "", $name = 'timezones')
    {
        $CI =& get_instance();
        $CI->lang->load('date');

        if ($default == 'GMT')
        {
            $default = 'UTC';
        }

        $menu = '<select name="'.$name.'"';

        if ($class != '')
        {
            $menu .= ' class="'.$class.'"';
        }

        $menu .= ">\n";

        // get list of available timezones (PHP >= 5.2.0!!!)
        $timezone_identifiers = DateTimeZone::listIdentifiers();

        $continent = "";

        foreach($timezone_identifiers as $value)
        {
            if (preg_match( '/^(Africa|America|Antartica|Arctic|Asia|Atlantic|Australia|Europe|Indian|Pacific)\//', $value))
            {
                $ex = explode("/", $value); //obtain continent, city

                if ($continent != $ex[0])
                {
                    if ($continent != "")
                    {
                        $menu .= '</optgroup>';
                    }

                    $menu .= '<optgroup label="'. $ex[0].'">';
                }

                $city = $ex[1];
                $continent = $ex[0];

                $selected = ($default == $value) ? ' selected="selected"' : '';

                $menu .= '<option value="'.$value.'"'. $selected. '>'.$city.'</option>';
            }
        }

        $menu .= "</select>";

        return $menu;

    }
} // end timezone_menu


if(! function_exists('gd_date'))
{
    function gd_date($_date, $format=null)
    {
        $ci=& get_instance();

        $date_format = $format;
        if(is_null($format))
        {
            $date_format = 'F j, Y, g:i a';
        }

        $date = new DateTime($_date);
        
        $user_timezone = new DateTimeZone($ci->session->userdata('timezone'));

        $date->setTimeZone($user_timezone);
     
        return $date->format($date_format);
    }
}
