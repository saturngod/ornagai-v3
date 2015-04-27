<?php

$path = dirname(__FILE__).'/../libs/phpseclib';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include_once('Crypt/RSA.php');
include_once('mail.php');
include_once('comments.php');

class user
{
  private $db;
  private $privateKey;
  private $config;
  function __construct($db,$config)
  {
    $this->db = $db;
    $this->privateKey = $config['privatekey'];
    $this->config = $config;
  }


  function checklogin()
  {
    $login = false;
    if(isset($_SESSION['username']) && isset($_SESSION['id']) && isset($_SESSION['email']))
    {
      $login = true;
    }

    if($login) {
      return array("login"=>$login,"user"=>array("id"=>$_SESSION['id'],"username"=>$_SESSION['username'],"email"=>$_SESSION['email']));
    }
    return array("login"=>$login);
  }

  function updatepwd($password,$newpwd)
  {
    $login = false;
    if(isset($_SESSION['username']) && isset($_SESSION['id']) && isset($_SESSION['email']))
    {
      $login = true;
    }

    if($login == false)
    {
      return array("update"=>false,"error"=>"Need to login");
    }

    $username = $_SESSION['username'];

    $salt = $this->get_salt_by_username($username);

    $pwd = $this->actual_password($salt,$newpwd);



    $current_pwd = $this->actual_password($salt,$password);

    $sql = "SELECT id FROM user WHERE id = '".$_SESSION['id']."' AND `password` = '".$current_pwd."';";
    $result = $this->db->query($sql);

    $row = $result->fetch_array(MYSQLI_ASSOC);
    if(count($row) == 0)
    {
      return array("update"=>false,"error"=>"Your current password is wrong");
    }

    $sql = "Update user SET `password` = '".$pwd."' WHERE id = '".$_SESSION['id']."' AND `password` = '".$current_pwd."';";

    $result = $this->db->query($sql);

    if($result)
    {
      return array("update"=>true,"error"=>"");
    }
    else {
      return array("update"=>false,"error"=>"System Error");
    }

  }

  function confirm($username,$code)
  {
    $sql = "SELECT id,confirm FROM user WHERE `username` = '".$username."' AND `confirm_code` = '".$code."'";

    $result = $this->db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if(count($row) > 0)
    {
      if($row['confirm'] == 0) {

        $sql = "Update user SET `confirm` = 1 WHERE `username` = '".$username."' AND `confirm_code` = '".$code."'";
        $result = $this->db->query($sql);
        if($result)
        {
            return array("confirm"=>true);
        }

      }
      else {
        return array("confirm"=>false,"error"=>"This account already confirmed.");
      }
    }
    else {
      return array("confirm"=>false,"error"=>"Account or confirm code not found.");
    }
  }



  function logout()
  {
     session_unset();
     return array("logout"=>true);
  }

  function login($username,$password)
  {
    //get salt
    //create db password from sha1(salt.password)
    //login

    $salt = $this->get_salt_by_username($username);

    $pwd = $this->actual_password($salt,$password);

    return $this->actual_login($username,$pwd);
  }

  function actual_login($username,$pwd)
  {

    $username = $this->db->real_escape_string($username);
    $sql = "SELECT id,username,email,confirm FROM user WHERE `username` = '".$username."' AND `password` = '".$pwd."' AND ban=0";

    $result = $this->db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if(count($row) > 0)
    {


      if($row['confirm'] == 1) {
          $_SESSION['username'] = $row['username'];
          $_SESSION['id'] = $row['id'];
          $_SESSION['email'] = $row['email'];
          return array("login"=>true,"user"=>array("id"=>$_SESSION['id'],"username"=>$_SESSION['username'],"email"=>$_SESSION['email']));
      }
      else {
          return array("login"=>false,"error" => "Please confirm your account first.");
      }

    }
    else {
      return array("login"=>false,"error" => "Incorrect username or password.");
    }
  }

  function actual_password($salt,$password)
  {
    $rsa = new Crypt_RSA();
    $rsa->loadKey($this->privateKey);

    $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

    $binary = base64_decode($password);

    $pwd = @$rsa->decrypt($binary);
    if($pwd == "")
    {
      echo "ERROR! Can't get the password.";
      exit;
    }

    return sha1($salt.$pwd);
  }

  function get_salt_by_username($username)
  {
    //get salt
    $username = $this->db->real_escape_string($username);
    $sql = "SELECT salt FROM user WHERE `username` = '".$username."'";
    $result = $this->db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if(count($row) > 0)
    {
      return $row['salt'];
    }
    return "";
  }

  // function createUser($username,$email,$password)
  // {
  //   $salt = MD5(mt_rand(0,999));
  //
  //   $sql = "INSERT INTO `dblist`(Word,state,def,approve,usr_id) VALUES (";
  //   $sql .= "'".$this->db->real_escape_string($word)."',";
  //   $sql .= "'".$this->db->real_escape_string($state)."',";
  //
  //   $sql .= "'".$this->db->real_escape_string($def)."',";
  //   $sql .= "'0',";
  //   $sql .= "'".$this->db->real_escape_string($_SESSION['id'])."')";
  //
  //   $result = $this->db->query($sql);
  //   if($result)
  //   {
  //     return array("data"=>true);
  //   }
  //   else {
  //     return array("data"=>false);
  //   }
  // }

  function register($email,$username,$password)
  {



    $salt = MD5(mt_rand(0,999));
    $actual_password = "";

    //check email
    //if email format is incorrect show error



    $error = "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Invalid email format";
    }
    else if(strlen($username) < 3 )
    {
      $error = "Username should be at least 3 characters.";
    }
    else if(strlen($password) == 0)
    {
      $error = "Password can't empty";
    }
    else if(strlen($password) > 0)
    {
      $actual_password = $this->actual_password($salt,$password);
    }


    $escape_username = $this->db->real_escape_string($username);

    if($error == "") {
      //check email or password
      $sql = "SELECT id FROM user WHERE `username` = '".$escape_username."' OR `email` = '".$email."'";
      $result = $this->db->query($sql);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      if(count($row) > 0)
      {
        $error = "Username or Email already exist";
      }
    }


    if($error != "")
    {
      return array("register"=>false,"error"=>$error);
    }




    $confirmCode = sha1($username.$salt);

    $sql = "INSERT INTO `user`(username,email,salt,password,join_date,confirm_code) VALUES (";
    $sql .= "'". $escape_username ."',";
    $sql .= "'". $email ."',";
    $sql .= "'". $salt."',";
    $sql .= "'". $actual_password ."',";
    $sql .= "now(),";
    $sql .= "'". $confirmCode."')";



    $result = $this->db->query($sql);
    if($result) {
      //user has been insearted
      //send the email

      $message  = "<h2>Thank you for registration</h2>Please <a href='". $this->config['base_url'] ."confirm/". $username. "/". $confirmCode ."'>Click Here</a> to confirm or ";
      $message .= $this->config['base_url']."confirm/". $username. "/". $confirmCode ." copy and paste URL to your borwser.";


      $mail = new mail();
      $mail->send("noreply@ornagai.com",$email,"Registration Completed",$message);
      return array("register"=>true,"error"=>"");
    }
    else {
      //show error can't inseart
      return array("register"=>false,"error"=>"Sorry can't register now.");
    }


  }

  function addComment($comment,$dict_id,$type) {
    $user_id = $_SESSION['id'];
    if(isset($_SESSION['id']) && $user_id !="") {

      //time to add, before adding , please check it
      if($comment == "") {
        return array("comment"=>false,"error"=>"Sorry, comment is missing");
      }
      else if($dict_id == "") {
        return array("comment"=>false,"error"=>"Sorry, dict_id is missing");
      }

      else if($type == "") {
        return array("comment"=>false,"error"=>"Sorry, type is missing");
      }

      $commentObj = new comments();
      $resp = $commentObj->addComments($comment,$user_id,$dict_id,$type,$this->db);
      if($resp != false) {
        return array("comment"=>true,"error"=>"","obj"=>$resp);
      }
      else {
        return array("comment"=>false,"error"=>"cannot insert comment");
      }

    }
    else {
      return array("comment"=>false,"error"=>"Sorry can't add comment now");
    }
  }

  function getComments($dict_id,$type="en") {
    $user_id = $_SESSION['id'];
    if(isset($_SESSION['id']) && $user_id !="") {


      if($dict_id == "") {
        return array("comment"=>false,"error"=>"Sorry, dict_id is missing");
      }

      $commentObj = new comments();
      return $comments = $commentObj->getComments($dict_id,$type,$this->db);
    }
    else {
      return array();
    }
  }


}
