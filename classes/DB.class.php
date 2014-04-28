<?php
class DB extends mysqli
{
	// A database connection class.
	// This class is a singleton.

	// Sington instance	
	private static $instance = null;
	
	// The private constructor
	private function __construct()
	{
		$instance = parent::__construct(
			$this->'localhost',
			$this->'root',
			$this->'',
			$this->'modeler'
		);
	}

	// getInstance static method.
	// Used to retireve the database singleton
	public static function getInstance()
	{
		if(self::$instance === null) {
			self::$instance = new DB();
		}
		
		return self::$instance;
	}

	// DESTROY! the singleton database object
	public function destroyinstance()
	{
		unset($this->database);
		unset($this->conn);
		self::$instance = null;
	}
}