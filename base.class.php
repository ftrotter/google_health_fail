<?php


abstract class base {

	abstract var $id_name;
	abstract var $table_name;
	var $array;

	function fromArray($array){
		$this->array = $array;
	}
	

	function save(){
		if(!isset($this->array)){
			//then I do not actually exist yet... thats a problem...	
			$class = get_class($this);
			echo "ERROR: base.class.php: class = $class the array has not been set!! saving will not work..";
			exit();
		}

		if(isset($this->array[$this->id_name)){
			//Then this an old record that needs to be updated


		        $link = $GLOBALS['db_link'];
			$table_name = $this->table_name;
			$input_array = $this->array;
		        $SQL = "INSERT INTO $table_name ";
		        $fields = "(";
       		 	$values = "(";
      	 	 	foreach ($input_array as $k => $v) {
       		         	$fields .= "`$k` ,";
                		$values .= "'$v' ,";
        		}
		        $fields .="#";
		        $values .="#";
		        $fields = explode(",#", $fields);
		        $fields = $fields[0];
		        $values = explode(",#", $values);
		        $values = $values[0];
		        $fields .= ")";
		        $values .= ")";
		        $SQL .= $fields . " VALUES " . $values;
			$class = get_class($this);
		  //      mysql_query($SQL) 
		//	or die("ERROR base.class.php: class: $class INSERT with: $sql but got ".mysql_error()); 

			echo $SQL;
		}

	}

}





}







?>
