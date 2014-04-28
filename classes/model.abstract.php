<?PHP
abstract class Model
{
	// This is the base abstract class for model classes. Currently it only supports has-one
	// relationships and not has-many relationships. During future implementation of has-many
	// relationships, the has-one logic should change significantly.

	// $id is used for exposing relationships in the database.
	// Every ID is unique for an single object type but not between multiple object types.
	private $id;

	// save() saves the object data in the database.
	// This method needs a lot more work to improve readability
	// optimization and improve logic.
	public function save()
	{
		// Grab to singleton database instance.
		$db = DB::getInstance();
		
		//If the save was successful
		$saved  = false;

		// What type of query to execute. (Update or insert).
		$option = ""; 

		// Grab the object property names
		$vars = get_object_vars($this);

		// This is assigned the value of 2 because (lack of understanding)
		// PHP adds an unknown key to the $vars array from the assignment above
		// and to mind the 'id' property which we do NOT want to include.
		$skipCount = 2;	

		// Counter
		$x = 0; 

		// Our SQL statement
		$sql = "";

		// get the class name
		$class = get_class( $this );

		// If the object already contains an ID then we know it has already
		// been saved to the database and we should execute an UPDATE statement.
		if( isset($this->id) )
		{
			// UPDATE statement.
			$option = "UPDATE";
			$sql    = "UPDATE " . $class . " SET ";

			// Loop through each property and add it to the update statement.
			foreach( $vars as $k => $v )
			{
				// Do not update the ID and make sure mysqli or hasOne properties are not included
				if($k != "id" && $k != "mysqli" && $k != "hasOne")
				{
					// property=value
					$sql .= $k . "='" . $v . "'";

					// If it not the last property, add a colen for the next property
					$x++;
					if($x < count($vars) - 1)
					{
						$sql .= ", ";
					}
				}

				$x++;
			}
			$sql .= " WHERE id='" . $this->id . "'";
		}
		else
		{
			// INSERT statement
			$option = "INSERT";
			$sql = "INSERT INTO " . $class . " (";

			// Loop through each property name and add it to the INSERT statement within the
			// columns section.
			foreach( $vars as $k => $v )
			{
				// Do not update the ID and make sure mysqli or hasOne properties are not included
				if($k != "id" && $k != "mysqli" && $k != "hasOne")
				{

					// adding the property to the insert columns
					$sql .= $k;

					// If it is not the last property, add a colen for the the next one.
					if($x < count($vars) - count($v) - $skipCount)
					{
						$sql .= ", ";
					}
				}
				$x++;
			}

			$x = 0;
			$sql .= ") VALUES (";

			// Loop through each property value add it the insert statement within the
			// value section.
			foreach( $vars as $k => $v )
			{
				// Do not update the ID and make sure mysqli or hasOne properties are not included
				if($k != "id" && $k != "mysqli" && $k != "hasOne")
				{	
					// property value
					$sql .= "'" . $v . "'";

					// If it is not the last property, add a colen for the the next one.
					if($x < count($vars) - count($v) - $skipCount)
					{
						$sql .= ", ";
					}
				}

				$x++;
			}

			// Ending parenthesis
			$sql .= ")";
		}

		// Execute the query
		$db->query($sql);

		// If the statement type was an UPDATE, get the ID.
		if($option != "UPDATE")
		{
			$this->id = $db->insert_id;
		}
		

		// If the object has any relational properties, they inserted/updated here
		if( isset($this->hasOne) )
  		{	

  			// Get object data
  			$objectData = $db->query("SELECT * FROM " . $class . " WHERE id='" . $this->id . "'");

  			// Turn the data into an array.
  			$row = $objectData->fetch_array(MYSQLI_ASSOC);

  			// Get class properties		
			$vars = get_class_vars( get_class($this) );

			// Get all the keys and values
  			$hasOneKeys = $this->hasOne;
  			

  			// Loop through each relational property and update it within the database.
  			$x=0;
  			foreach( $hasOneKeys as $k => $v )
  			{
  				// A single foreign relationship.
  				$setOneRelationalData = "
  					UPDATE " . $class . "
  					SET " . strtolower($k) . "_id='" . $v['id'] . "'
  					WHERE id='" . $this->id . "'
  				";

  				// If the save was not successful, tell the user.
  				if( !$db->query($setOneRelationalData) )
  				{
  					echo "Did not save.";
  				}	
  			}
  			
  		}

  		//Saved here? Fix this logic!
		$saved = true;
		
		$db->query("START COMMIT");
		
		return $saved;
	}

	// delete() removes a "object" instance from the database and not from memory in PHP
	public function delete()
	{
		// Grab the database instance
		$db = DB::getInstance();

		// If the deletion was successful
		$deleted = false;

		$class = get_class($this);

		// If the object has an ID it is stored within the database.
		// If it does NOT have an ID, the do not run a query.
		if( isset($this->id) )
		{
			if( $db->query("DELETE FROM " . $class . " WHERE id='" . $this->id ."'") )
			{
				//The "object" was deleted from the database.
				$deleted = true;
				$db->query("START COMMIT");
			}
		}

		return $deleted;
	}

	// Returns and object by specificing it's ID.
	// This should also be turned into a static method
	public function getByID( $objID )
	{
		// Get the database instance
		$db = DB::getInstance();

		// Get the class name.
		$class = get_class($this);

		// Get the class property names and values.
		$objectData = $db->query("SELECT * FROM ".get_class($this)." WHERE id='" . $objID . "'");

		// Get the class property names.
		$vars = get_class_vars($class);

		$row = $objectData->fetch_array(MYSQLI_ASSOC);

		// Loop through each property
		foreach( $vars as $k => $v )
		{
			// If it a relational property
			if( $k == "hasOne" )
			{
				$x=0;
				while( $x < count($v) )
				{
					foreach( $v as $class => $attr )
					{
						$getOneRelationalData = "
							SELECT
								*
							FROM
								" . strtolower( $attr['className'] ) . "
							WHERE " . " 
								id='" . $row[ strtolower($attr['className'] . "_id") ] . "'
						";
						
						// Get the relationship data
						$result = $db->query($getOneRelationalData);

						// Update the relationship data.
						$this->hasOne[ $attr['className'] ] = $result->fetch_array(MYSQL_ASSOC);
					}
					$x++;

				}
				
			}		
			else
			{
				// If it is not a relationship property, simply just update the property with it's
				// value.
				$this->$k = $row[$k];
			}

			//Loop End
		}

		//No return neccessary
	}

	// Returns an objects ID
	public function getID()
	{
		return $this->id;
	}

	// A method echo a general description of an object and it's properties.
	// This should be overloaded to provide a more specific description.
	public function describe()
	{
		// Get the object propertues
		$vars = get_object_vars($this);

		$class = get_class($this);

		echo "This is a " . $class . " object.<br />It's properties and values are:<br /><br />";
		foreach( $vars as $k => $v )
		{
			//property: value
			echo $k .": " . $v . "<br />";
		}
	}

	// An anonymous getter for property relational properties
	// Needs a bit of work.
	public function __get( $propertyName ) 
	{
		// This logic is incorrect. Should it not return the value??
		if( $propertyName != "id" || "mysqli" )
    	return( $propertyName );
  	}

  	// An anonymous setter for relational properties
  	// Currently needs work
  	public function __set($propertyName, $value) 
  	{
  		// Get the database instance.
  		$db = DB::getInstance();

  		// If the object does contain relational properties.
  		if( isset($this->hasOne) )
  		{
  			// Loop through each relational property
  			$hasOneKeys = $this->hasOne;
  			foreach( $hasOneKeys as $k => $v )
	  		{
	  			// Check to make sure the relational property name
	  			// matches the one being updated.
	  			if( strtolower($k) == $propertyName )
	  			{
	  				// Get the relational data
	  				$getOneRelationalData = "
	  					SELECT
	  						*
	  					FROM 
	  						" . strtolower( $propertyName ) . " 
	  					WHERE
	  						id='" . $value . "'
	  					";

	  					// Execute the statement
						$result = $db->query( $getOneRelationalData );

						// Store the relational data within the hasOne property.
						$this->hasOne[$k] = $result->fetch_array(MYSQL_ASSOC);
	  			}
	  		}
  		}
  	}
}