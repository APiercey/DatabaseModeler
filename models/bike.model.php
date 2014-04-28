<?php
class Bike extends Model
{
	//A bike class model used for example and test purposes.
	
	public $colour = "";
	public $num_of_gears  = 0;
	public $num_of_wheels = 0;
	public $num_of_seats  = 0;

	//Relationships
	public $hasOne = array(
					'Factory' => array(
						'className' => 'Factory',
		)
	);

	//Overloaded describe method
	public function describe()
	{
		$factory = new Factory();
		$factory->getByID( $this->hasOne['Factory']['id'] );

		echo "This bike is a ".$this->num_of_gears
			." ".$this->colour." and has ". $this->num_of_wheels
 			." wheels and " . $this->num_of_seats . " seats "
 			." and is produced by " . $factory->name . ".";
	}
}