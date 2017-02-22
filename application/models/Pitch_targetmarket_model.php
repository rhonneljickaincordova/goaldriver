<?php

class Pitch_targetmarket_model extends MY_Model{
	
	public $_table = 'pitch_target_market';
	public $primary_key = 'id';
	public $before_create = array( 'insert_market_segment', 'timestamps' );
	public $before_update = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['updated'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    protected function insert_market_segment($segment)
    {
    	$segment['entered'] = date('Y-m-d H:i:s');
        return $segment;
    }

    
    
}