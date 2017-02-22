<?php

class Pitch_funding_model extends MY_Model{
	
	public $_table = 'pitch_funding';
	public $primary_key = 'id';
	public $before_update = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['updated'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    
    
}