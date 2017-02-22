<?php

class Pitch_problem_model extends MY_Model{
	
	public $_table = 'pitch_problems';
	public $primary_key = 'id';
	public $before_update = array( 'timestamps' );

	protected function timestamps($pitch)
    {
        $pitch['updated'] = date('Y-m-d H:i:s');
        return $pitch;
    }

    
    
}