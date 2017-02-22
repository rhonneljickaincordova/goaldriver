<?php

class Pitch_solution_model extends MY_Model{
	
	public $_table = 'pitch_solution';
	public $primary_key = 'id';
	public $before_update = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['updated'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    
    
}