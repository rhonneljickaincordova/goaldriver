<?php

class Pitch_positioning_model extends MY_Model{
	
	public $_table = 'pitch_positioning';
	public $primary_key = 'id';
	public $before_update = array( 'timestamps' );
	public $before_create = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['updated'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    
    
}