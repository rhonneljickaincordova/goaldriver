<?php

class Pitch_competition_model extends MY_Model{
	
	public $_table = 'pitch_competition';
	public $primary_key = 'id';
	public $before_create = array( 'insert_competitor', 'timestamps' );
	public $before_update = array( 'timestamps' );

	protected function timestamps($competitor)
    {
        $competitor['updated'] = date('Y-m-d H:i:s');
        return $competitor;
    }

    protected function insert_competitor($competitor)
    {
    	$competitor['entered'] = date('Y-m-d H:i:s');
        return $competitor;
    }

    
    
}