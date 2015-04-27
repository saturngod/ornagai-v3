<?php

class comments
{

  function addComments($comment,$user_id,$dict_id,$type="en",$db) {

    $sql =  "INSERT INTO `comments`(comment,user_id,dict_id,type) VALUES (";
    $sql .= "'". $db->real_escape_string($comment) . "',";
    $sql .= "'".$user_id."',";
    $sql .= "'".$dict_id."',";
    $sql .= "'". $db->real_escape_string($type) . "'";
    $sql .= ")";

    //echo $sql;exit;
    $result = $db->query($sql);

    if($result) {
      //comment has been insert

      //get the latest comment

      $sql = "SELECT comments.comment ,comments.date_time , user.id , user.username,MD5(user.email) as avatar ";
      $sql .= "FROM `comments`";
      $sql .= " INNER JOIN user on user.id = comments.user_id ";
      $sql .= "WHERE comments.type = '".$type."' AND dict_id = ".$dict_id;
      $sql .= " AND user.id = ".$user_id;
      $sql .= " ORDER BY comments.date_time DESC";

      $result = $db->query($sql);
      if($result) {
        $obj = $result->fetch_all(MYSQLI_ASSOC);
        if(count($obj) > 0) {
          $res = $obj[0];
          return $res;
        }
      }


    }
    return false;
  }

  function getComments($dict_id,$type="en",$db) {
    $sql = "SELECT comments.comment ,comments.date_time , user.id , user.username,MD5(user.email) as avatar ";
    $sql .= "FROM `comments`";
    $sql .= " INNER JOIN user on user.id = comments.user_id ";
    $sql .= "WHERE comments.type = '".$type."' AND dict_id = ".$dict_id;
    $sql .= " ORDER BY comments.date_time ASC";


    $result = $db->query($sql);
    if($result) {
      $obj = $result->fetch_all(MYSQLI_ASSOC);
      return $obj;
    }
    else {
      return array();
    }
  }
}
