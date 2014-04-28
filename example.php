<?php
//Include the modeler
require_once('modeler.php');

//Create a factory
$factory = new Factory();
$factory->name = "Bikes Ltd.";

//Save it to the database
$factory->save();

//Create a new bike!
$bike = new Bike();
$bike->colour = "Blue";
$bike->num_of_seats  = 3;
$bike->num_of_gears  = 13;
$bike->num_of_wheels = 5;

//Define which facctory produces this bike
$bike->factory = $factory->getID();

//Save the bike.
$bike->save();

//Echo the descripton of the bike)
$bike->describe();