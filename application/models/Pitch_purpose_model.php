<?php

class Pitch_purpose_model extends MY_Model{
	
	public $_table = 'pitch_purpose';
	public $primary_key = 'id';
	public $before_update = array( 'timestamps' );
	public $before_create = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['updated'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    
    
}