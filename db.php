<?php 

/**
* CRUD class
*/
class Db
{
	private $host,$user,$password,$dbname,$conn,$error;

	function __construct($server,$username,$pwd,$db)
	{
		$this->host=$server;
		$this->user=$username;
		$this->password=$pwd;
		$this->dbname=$db;
		$this->mysqli_conn();

	}
	private function mysqli_conn(){
		return $this->conn=mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
	}

	private function ckConn(){
		if (!$this->conn) {
		    die("Connection failed: " . mysqli_connect_error());
		    return false;
		}else{
			return true;
		}
	}

	//By default fomat="assocative array", "json"
	public function find($table="",$query="*",$arg="",$format="array"){

		$sql=$this->sqlQueryStatment($table,$query,$arg);
		if(!$sql){
			return "Miss Table";
			//return $sql->error;
		}
		$result=$this->exe($sql);
		
		$results=array();
		try{
			if($result){
				while($row = mysqli_fetch_assoc($result)) {
		    		array_push($results,$row);
			    }
			    if($format=="json"){
			    	return json_encode($results,JSON_UNESCAPED_UNICODE);
			    }else{
			    	return $results;
			    }
			}else{

			}
		}catch(Exception $e){
            //parent::CustomError("Error Processing Request");
		}
		$this->closedb();
	}

	public function count($table="",$arg=""){
		if(sizeOf($arg)>0)
		{
			$arg=$this->shotArr($arg);
		}
		$sql="SELECT count(*) AS 'count' FROM ".$table." ";
		$result=$this->exe($sql);
		while($row = mysqli_fetch_assoc($result)) {
		   	return $row['count'];
		}
		
	}

	//Update function $table:String, $set:Associative Array, $where:Associative Array
	public function update($table,$set,$where){ 
		$sql=$this->sqlUpdateStatment($table,$set,$where);
		if ($this->exe($sql)=== TRUE) {
		    echo "Record updated successfully";
		} else {
		    echo "Error update record: " . $this->conn->error;
		}
	}

	public function delete($table,$where=""){
		$sql=$this->sqlDeleteStatment($table,$where);
		if ($this->exe($sql)=== TRUE) {
		    echo "Record deleted successfully";
		} else {
		    echo "Error delete record: " . $this->conn->error;
		}
	}

	public function add($table,$field){
		$sql=$this->sqlInsertStatment($table,$field);
		if ($this->exe($sql)=== TRUE) {
		    //echo "Record added successfully: ".$sql;
		    
		} else {
		    echo "Error add record: " . $this->conn->error;
		}
		
	}

	private function exe($sql){
		$this->mysqli_conn();
		//mysql_set_charset('utf8');
		mysqli_set_charset($this->conn,"utf8");
		return mysqli_query($this->conn,$sql);
	}

	private function closedb(){
		mysqli_close($this->conn);
	}

	private function sqlQueryStatment($table,$arg,$query="*"){ //
		if($table==""){
			return false;
		}
		if($query==""){
			$query="*";
		}
		if($arg!=""){
			$argquery=$this->shotArr($arg);
		}

		
		if($table!==""&&$argquery!=""){
			$result="SELECT ".$query." FROM ".$table." WHERE ".$argquery;
			return $result;
		}else{
			return "SELECT ".$query." FROM ".$table."";
		
		}
	}

	private function sqlUpdateStatment($table,$set,$arg){ //Update Statment
		if($table==""){
			return false;
		}
		if($set!=""&&$arg!=""){
			$argquery=$this->shotArr($arg);
			$setArr=$this->shotArr($set,true);
			$result="UPDATE ".$table." SET ".$setArr." WHERE ".$argquery;
			return $result;
		}
	}

	private function sqlDeleteStatment($table,$arg){
		if($table==""){
			return false;
		}
		if($arg!=""){
			$argquery=$this->shotArr($arg);
			$result="DELETE FROM ".$table." WHERE ".$argquery;
			return $result;
		}else{
			$result="DELETE FROM ".$table;
			return $result;
		}
	}

	private function sqlInsertStatment($table,$field){
		if($table==""){
			return false;
		}
		if($field!=""){
			$shot=$this->shotArr($field,false,true);
			return "INSERT INTO ".$table." (".$shot['field'].") VALUES (".$shot['values'].")";
		}
	}

	private function shotArr($arr,$update=false,$insert=false){ //Use to shot associative array and shot update set case
		$insertfield="";
		$field="";
		$val="";
		if(!is_array($arr)){
			return "Not Associative Array";
		}else{
			$i=0;
			foreach ($arr as $key => $value) {
				if(!$insert&&$i==0){
					$field=$field.$key."='".$value."'";
				}else if(!$insert&&!$update&&$i>0){
					$field=$field." AND ".$key."='".$value."'";
				}else if(!$insert&&$update&&$i>0){
					$field=$field.",".$key."='".$value."'";
				}else if($insert&&$i>0){
					$insertfield=$insertfield.",".$key;
					$val=$val.",'".$value."'";
				}else if($insert&&$i==0){
					$insertfield=$key;
					$val="'".$value."'";
				}
				$i++;
			}
			if($insert==true){
				return array("field"=>$insertfield,"values"=>$val);
			
			}else{
				return $field;	
			}
			
		}
	}
}

?>
