<?php 
/**
* 
*/

include "db.php";

class test extends Db
{
	
	function __construct()
	{
		Db::__construct("","","",""); //$server,$username,$pwd,$db
	}

	public function insert($hkid,$lastname,$location,$time){
		if($this->checkHkId($hkid)){
			
		}
	}

	private function checkHkId($hkId){

	}

	private function checkLastname($lastname){

	}

	private function checkTime($time){

	}

}

?>