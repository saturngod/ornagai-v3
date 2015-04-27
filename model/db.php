<?php

include("zawgyi.php");
class db
{

	private $mysqli = "";

	/**
	* Constructor , init the database
	**/
	function __construct($config)
	{

    $this->mysqli = new mysqli($config['db_host'],  $config['db_username'], $config['db_password'], $config['db_name']);

    if($this->mysqli->connect_errno)
    {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      exit;
    }
    $this->mysqli->set_charset('utf8');
	}

  function real_escape_string($text)
  {
    return $this->mysqli->real_escape_string($text);
  }

	function search($query)
	{
    $query = $this->mysqli->real_escape_string($query);
		//check zawgyi or english
		if(preg_match_all("/[က-႟]+/",$query))
    {
      //Myanmar
      return $this->query_myanmar($query);
    }
    else {
     //English
 		 return $this->query_english($query);
    }
	}

  function query($sql)
  {
    return $this->mysqli->query($sql);
  }

  function suggest($query)
  {
    $query = $this->mysqli->real_escape_string($query);
    //check zawgyi or english
    if(preg_match_all("/[က-႟]+/",$query))
    {
      //Myanmar
      $suggest = $this->query_myanmar($query,true);
    }
    else {
     //English
     $suggest = $this->query_english($query,true);
    }


    return $suggest;
  }


  function query_english($query,$suggest=false)
  {
    $select = "";
    $limit = "";
    if($suggest)
    {
      $select = "SELECT Word, IF( `Word` = '$query', 1, IF( `Word` LIKE '$query%', 2, IF( `Word` LIKE '%$query', 4, 3 ) ) ) AS `sort`";
      $limit =  "5";
    }
    else {
      $select = "SELECT id,Word,state,def,approve , IF( `Word` = '$query', 1, IF( `Word` LIKE '$query%', 2, IF( `Word` LIKE '%$query', 4, 3 ) ) ) AS `sort` , 'en' as `type`";
      $limit = "20";
    }
		 $sql = $select . " FROM `dblist`
WHERE `Word` LIKE '%$query%'
ORDER BY `sort` , `Word` Limit ".$limit;

    if($result = $this->mysqli->query($sql))
    {
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $obj;
    }
    else {
     return array();
    }
  }

  function query_myanmar($query,$suggest=false)
  {
    $zawgyi = new Zawgyi();
    $query = $zawgyi->normalize($query,"|",true);
    //query need to normalize and syllable break
    $select = "";
    $limit = "";
    if($suggest)
    {
      $select = "SELECT REPLACE(Word,\"|\",\"\") as Word, IF( `Word` = '$query', 1, IF( `Word` LIKE '$query%', 2, IF( `Word` LIKE '%$query', 4, 3 ) ) ) AS `sort`";
      $limit = "5";
    }
    else {
      $select = "SELECT id,REPLACE(Word,\"|\",\"\") as Word,state,REPLACE(def,\"/\",\"<br/>\") as def ,approve, IF( `Word` = '$query', 1, IF( `Word` LIKE '$query%', 2, IF( `Word` LIKE '%$query', 4, 3 ) ) ) AS `sort` , 'mm' as `type`";
      $limit = "20";
    }
		$sql = $select. " FROM `myen`
WHERE `Word` LIKE '%$query%'
ORDER BY `sort` , `Word` Limit ".$limit;

    if($result = $this->mysqli->query($sql))
    {
      $obj = $result->fetch_all(MYSQLI_ASSOC);
      if(count($obj) < 10)
      {
        $newObj = $this->query_myanmar_en($query,$suggest);
        return array_merge($obj,$newObj);
      }
      return $obj;
    }
    else {
     return array();
    }
  }

  function query_myanmar_en($query,$suggest=false)
  {

     $select = "";
    $limit = "";
    if($suggest)
    {
      $select = "SELECT REPLACE(def,\"|\",\"\") as Word, IF( `def` = '$query', 1, IF( `def` LIKE '$query%', 2, IF( `def` LIKE '%$query', 4, 3 ) ) ) AS `sort`";
      $limit = "5";
    }
    else {
      $select = "SELECT id,REPLACE(def,\"|\",\"\") as Word,state,Word as def, IF( `def` = '$query', 1, IF( `def` LIKE '$query%', 2, IF( `def` LIKE '%$query', 4, 3 ) ) ) AS `sort` , 'my' as `type`";
      $limit = "20";
    }

		$sql = $select." FROM `mydblist`
WHERE `def` LIKE '%$query%'
ORDER BY `sort` , `def` Limit ".$limit;


    if($result = $this->mysqli->query($sql))
    {
      $obj = $result->fetch_all(MYSQLI_ASSOC);
      return $obj;
    }
  }



}
