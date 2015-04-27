<?php
class dictionary
{
  private $db;

  function __construct($db)
  {
    $this->db = $db;
  }

  function add($word,$state,$def)
  {
  	
  	$sql = "INSERT INTO `dblist`(Word,state,def,approve,usr_id) VALUES (";
    $sql .= "'".$this->db->real_escape_string($word)."',";
    $sql .= "'".$this->db->real_escape_string($state)."',";

    $sql .= "'".$this->db->real_escape_string($def)."',";
    $sql .= "'0',";
    $sql .= "'".$this->db->real_escape_string($_SESSION['id'])."')";

    $result = $this->db->query($sql);
    if($result)
    {
    	return array("data"=>true);
    }
    else {
    	return array("data"=>false);	
    }
  }
}