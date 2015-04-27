$(document).ready(function(){


  //auto complete
  autocomplete();


	$("#query").keyup(function(e){
		if(e.keyCode === 13)
		{
			search($(this).val(),function(results){
				showList(results);
			});
		}
	});

  $("#password").keyup(function(e){
    if(e.keyCode === 13)
    {
      loginNow();
    }
  });

	$("#results").on('click',' section',function(){
		var word = $(this).find(".word").text();
		var state = $(this).find(".state").text();
		var def = $(this).find(".def").html();
    console.log(def);
		showDetail(word,state,def);
	});

  checklogin();

  $("#loginout").on('click',function(){

    if($("#loginout").text() == "Logout")
    {
      $("#loginout").html("Logout...");
      logout();
    }
    else {
      //show login form
      showLogin();
    }

    return false;

  });

  $("#addbtn").on('click',function() {

     if($("#loginout").text() == "Logout")
     {
        showAddForm();
     }
     else {
        showLogin();
     }

     return false;

  });

  $("#loginBtn").on('click',function(){
    loginNow();
  });

  $("#addnewword").on('click',function(){
    addword();
  });
});

//login
function checklogin()
{
  //loginout
	$.ajax({
		url :"checklogin",
		success : function(data)
		{
      if(data.login)
      {
        $("#loginout").html("Logout");
      }
      else {
        $("#loginout").html("Login");
      }
    }
  });
}

function encryptPassword(password)
{
  var rsa = new RSAKey();
  rsa.setPublic("PASTE MODDLUS","10001");
  var res = rsa.encrypt(password);
  return hex2b64(res);
}


function loginNow()
{
  var username = $("#username").val();
  var password = $("#password").val();

  if(username == "")
  {
    $("#loginFrm .errmsg").val("Please enter username");
  }
  else if(password == "")
  {
    $("#loginFrm .errmsg").val("Please enter password");
  }
  else {
    login(username,password);
  }

}
function login(username,password)
{
  var encrypt =  encryptPassword(password);

  $.ajax({
    url :"login",
    data : "username="+username  + "&password=" + encodeURIComponent(encrypt),
    type : "POST",
    success : function(data)
    {
      if(data.login)
      {
        closeLogin();
        $("#loginout").html("Logout");
      }
    }
  });
}

function showLogin()
{
  $("#loginFrm").fadeIn();
}
function closeLogin()
{
  $("#loginFrm").fadeOut();
}

function logout()
{
  //loginout
  $.ajax({
    url :"logout",
    success : function(data)
    {
      if(data.logout)
      {
        $("#loginout").html("Login");
      }
    }
  });
}

//end login

//start Add Word
function showAddForm()
{
  $("#addFrm").fadeIn();
}

function closeAddForm()
{
  $("#addFrm").fadeOut();
}
//end Add Word




//start search
function search(value,callback)
{
  var loading = "<section>" +
                 "<div class='detail'>" +
                 "<div class='word'>Searching...</div> " +
                 "</div>" +
                 "</section>";
  $("#results").html(loading);

	$.ajax({
		url :"search/" + value,
		success : function(data)
		{
			if(data)
			{

					callback(data);

			}
		},
		error : function(xhr,options,thrownError)
		{
			callback([]);
		}
	});
}

function showList(results)
{
	$("#results").html("");
  if(results.length == 0)
  {
    var loading = "<section>" +
                   "<div class='detail'>" +
                   "<div class='word'>Result not found</div> " +
                   "</div>" +
                   "</section>";
    $("#results").html(loading);
    return;
  }
	var div = "";
	for(index = 0 ; index < results.length ; index++)
	{
		var objc = results[index];
		div += "<section>" +
                "<div class='detail'>" +
                "<div class='word'>"+objc.Word+"</div> " +
                "<div class='state'>"+objc.state+ "</div>" +
                "<div class='def'>"+objc.def+"</div>"+
                "</div>" +
            "</section>";

	};
	$("#results").html(div);
}

function showDetail(word,state,def)
{
	$("#details").html("");//clear details
	var val = 	"<div class='word'>"+word+"</div>"+
                "<div class='state'>"+state+"</div>"+
                "<div class='def'>"+def+"</div>"+
                "<div class='status'>Approved</div> "+
                "<div class='date'>updated at 12 Jan 2014</div>";

	$("#details").html(val);

}

function autocomplete()
{
  $('#query').typeahead({
    hint: false,
    highlight: true,
    minLength: 1
},
{
name: 'suggestion',
displayKey: 'Word',
source: substringMatcher()
});

}

function addword()
{
  var word = $("#enword").val();
  var state = $("#enstate").val();
  var def = $("#enDef").val();
  console.log(def);
  $.ajax({
    url :"data/add",
    data : "word="+encodeURIComponent(word)  + "&state=" + encodeURIComponent(state) + "&def=" + def,
    type : "POST",
    success : function(result)
    {
      if(result.data)
      {

          closeAddForm();

      }
    },
    error : function(xhr,options,thrownError)
    {

    }
  });
}

var substringMatcher = function(strs) {
return function findMatches(q, cb) {

      $.ajax({
    url :"suggest/"+q,
    success : function(data)
    {
      cb(data);
    },
    fail : function()
    {
      cb([]);
    }
    });

  }
}
