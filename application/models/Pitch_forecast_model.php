<?php

class Pitch_forecast_model extends MY_Model{
	
	public $_table = 'forecast';
	public $primary_key = 'id';
	public $before_create = array( 'timestamps' );


	protected function timestamps($forecast)
    {
        $forecast['entered'] = date('Y-m-d H:i:s');
        return $forecast;
    }

    function download_forecast($forecast_id, $user_id)
    {
    	$query = $this->db->query("SELECT a.file, a.url, a.file_url, b.organ_id, b.user_id FROM forecast a, organisation_users b where a.organ_id = b.organ_id AND b.user_id = ".$user_id." AND a.id = ".$forecast_id);
    	return $query->result();
    }

}