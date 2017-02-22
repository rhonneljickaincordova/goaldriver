<?php

class Pitch_published_model extends MY_Model{
	
	public $_table = 'pitch_published';
	public $primary_key = 'id';
	public $before_create = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['entered'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    
    
}