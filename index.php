<?php
  session_start();
	include 'vendor/autoload.php';
  include 'config.php';
	include 'model/db.php';
  include 'model/user.php';
  include 'model/dictionary.php';

  function show_json($data,$app)
  {
    //$response = $app->response();
    //$app->contentType('application/json');
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }

	$app = new \Slim\Slim();

	$db = new db($config);
  $user = new user($db,$config);
  $dict = new dictionary($db);

	$app->get('/',function() use ($db){
		require('view/home.php');
	});

  $app->post('/login',function() use ($db,$config,$app,$user){

    show_json($user->login($_POST['username'],$_POST['password']),$app);
  });

  $app->post('/register',function() use ($db,$config,$app,$user){

    show_json($user->register($_POST['email'],$_POST['username'],$_POST['password']),$app);
  });

  $app->get('/logout',function() use ($app,$user){

    show_json($user->logout(),$app);
  });

  $app->get('/checklogin',function() use ($db,$app,$user){
    show_json($user->checklogin(),$app);
  });

	$app->get('/search/:query', function ($name) use ($db,$app){
		show_json($db->search($name),$app);
	});

  $app->get('/suggest/:query', function ($name) use ($db,$app){
      show_json($db->suggest($name),$app);
  });

  $app->get('/confirm/:username/:confirmCode',function($username,$confirmCode) use ($db,$app,$user,$config) {

    //confirm first
    //after confirm redirect to home
    $res = $user->confirm($username,$confirmCode);
    if($res["confirm"])
    {
      $app->redirect($config['base_url']);
    }
    else if(isset($res['error'])) {
      echo $res['error'];
    }
    else {
      echo "Something Wrong";
    }
    exit;

  });

  $app->group('/data', function () use ($app,$db,$user,$dict) {

    $req = $app->request;
    $rootUri = $req->getRootUri();
    $resourceUri = $req->getResourceUri();
    $auth = true;

    if(substr($resourceUri, 0,6) == "/data/")
    {
      $data = $user->checklogin();
      if($data['login'] == false)
      {
        $auth = false;
        show_json($data,$app);
      }
    }
    if($auth) {
        $app->post('/add',function() use ($db,$app,$dict){


        $str = "";
        if(!isset($_POST['word']) || $_POST['word'] =="")
        {
          $str = "Word is required";
        }
        else if(!isset($_POST['state']) || $_POST['state'] == "")
        {
        $str = "State is required";
        }
        else if(!isset($_POST['def']) || $_POST['def'] == "")
        {
        $str = "def is required";
        }
        if($str != "")
        {
          show_json(array("data"=>false,"error"=>$str),$app);
          return;
        }
        else {
          show_json($dict->add(urldecode($_POST['word']),urldecode($_POST['state']),urldecode($_POST['def'])),$app);
        }

        });

        $app->post('/editpassword',function() use ($db,$app,$user){

          //check pwd
          if(!isset($_POST['pwd']) || $_POST['pwd'] == "")
          {
            show_json(array("error"=>"password is required"),$app);
            return;
          }
          if(!isset($_POST['newpwd']) || $_POST['newpwd'] == "")
          {
            show_json(array("error"=>"new password is required"),$app);
            return;
          }
          else {
            //update password
            show_json($user->updatepwd($_POST['pwd'],$_POST['newpwd']),$app);
            return;
          }


        });

        $app->post('/comment',function() use ($db,$app,$user) {

          //check pwd
          if(!isset($_POST['comment']) || $_POST['comment'] == "")
          {
            show_json(array("error"=>"comment is required"),$app);
            return;
          }
          else if(!isset($_POST['dict_id']) || $_POST['dict_id'] == "") {
            show_json(array("error"=>"dict_id is required"),$app);
            return;
          }

          else if(!isset($_POST['type']) || $_POST['type'] == "") {
            show_json(array("error"=>"type is required"),$app);
            return;
          }

          else {

            $comment = $_POST['comment'];
            $dict_id = $_POST['dict_id'];
            $type = $_POST['type'];

            show_json($user->addComment($comment,$dict_id,$type),$app);
            return;
          }

        });

        $app->get('/comments/type/:type/id/:dict_id/',function($type,$dict_id) use ($db,$app,$user) {

          if($dict_id == "") {
            show_json(array("error"=>"dict_id is required"),$app);
            return;
          }

          else if($type == "") {
            show_json(array("error"=>"type is required"),$app);
            return;
          }


          show_json($user->getComments($dict_id,$type),$app);
          return;
        });
    }


  });


	$app->run();
