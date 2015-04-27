<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ornagai</title>

    <!-- For Google -->
    <meta name="description" content="Ornagai , English to Myanmar , Myanamr To English Dictionary.">
    <meta name="keywords" content="Ornagai,Myanmar,English,Dictinary,MMDictionary,Zawgyi">

    <!-- For Facebook -->
    <meta property="og:title" content="Ornagai">
    <meta property="og:site_name" content="Ornagai">
    <meta property="og:description" content="Ornagai , English to Myanmar , Myanamr To English Dictionary." />

    <!-- For Twitter -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Ornagai" />
    <meta name="twitter:description" content="Ornagai , English to Myanmar , Myanamr To English Dictionary." />

    <link rel="search" type="application/opensearchdescription+xml" title="Ornagai Dictionary" href="./opensearch.xml">

    <link rel="stylesheet" type="text/css" href="style/style.css">

    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href='http://mmwebfonts.comquas.com/fonts/?font=zawgyi' />

	  <script src="./js/jquery-2.1.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.10/angular.js"></script>
    <script src="https://code.angularjs.org/1.3.10/angular-route.min.js"></script>
    <script type="text/javascript" src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
    <script src="./js/jsbn.js"></script>
    <script src="./js/rsa.js"></script>
    <script src="./js/base64.js"></script>
    <script src="./js/prng4.js"></script>
    <script src="./js/rng.js"></script>
    <script src="./js/extra.js"></script>

    <!-- markdown -->
    <script src="./js/marked.js"></script>

    <link rel="stylesheet" href="./fonts/css/webfont.css">
    <!--[if IE 7]><link rel="stylesheet" href="css/webfont-ie7.css"><![endif]-->

    <!-- alert -->
    <script src="./js/sweetalert/lib/sweet-alert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./js/sweetalert/lib/sweet-alert.css">

    <!-- text to speech -->
    <script src="./js/speech/polyfill.min.js"></script>



    <!-- angular js files -->
    <script src="./js/angualr/app.js"></script>
    <script src="./js/angualr/controller/searchController.js"></script>
    <script src="./js/angualr/controller/resultController.js"></script>
    <script src="./js/angualr/controller/usermenuController.js"></script>
    <script src="./js/angualr/controller/addFormController.js"></script>
    <script src="./js/angualr/controller/changepwdController.js"></script>
    <script src="./js/angualr/controller/commentController.js"></script>

    <!-- put in the last for checking login boradcast -->
    <script src="./js/angualr/controller/userController.js"></script>

</head>
<body ng-app="ornagai">

    <div id="left" ng-controller="searchController as searchCtrl">
        <header>
            <div id="topheader">
                <img src="images/logo.jpg" class="logo">
                <i id="search-icon">&nbsp;</i>
                <input ng-model="searchCtrl.query" ng-keyup="searchCtrl.searchKeyup($event.keyCode)" id="query" type="text" placeholder="Search">
            </div>
        </header>
        <div id="results">
            <!--<section>
                <div class='detail'>
                <div class='word'>sample</div>
                <div class='state'>n</div>
                <div class='def'>စမ်းသပ်</div>
                </div>
            </section>
			-->
            <div class='loading' ng-show="searchCtrl.showloading">Loading...</div>
            <section ng-repeat="result in searchCtrl.results">
              <div class="detail" ng-click="searchCtrl.clickResult(result)">
                <div class='word'>{{result.Word}} <a href="" class='speak' ng-click="searchCtrl.tts(result)"><i class="icon-volume"></i></a></div>

                <div class='state'>{{result.state}}</div>
                <div class='def'>{{result.def}}</div>
              </div>
            </section>
        </div>
        <footer ng-controller="userMenuController as usermenuCtrl">
            <ul id="usermenu">
              <li><a href="" ng-click="searchCtrl.showChangePassword()">Change Password</a></li>
              <li><a href="" ng-click="searchCtrl.logout()">Logout</a></li>
            </ul>
            <div id="bottom">
                <a href="" id="loginout" class='btn' ng-click="searchCtrl.showLoginForm()" ng-show="!searchCtrl.isLogin">Login</a>
                <a href="" id="userprofile" ng-click="usermenuCtrl.showmenu()" class='btn' ng-show="searchCtrl.isLogin">{{searchCtrl.username}}</a>
                <a href="" id="addbtn" class='btn add' ng-show="searchCtrl.isLogin" ng-click="searchCtrl.showAdd()">Add</a>
            </div>
        </footer>
    </div>

    <!-- <div id="right" ng-controller="resultController as resultCtrl">

    </div> -->
    <div id="right">
     <div ng-view></div>
   </div>
     <login-form></login-form>
     <add-form></add-form>
     <changepwd-form><changepwd-form>




<!-- google analystic -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-2358448-18', 'auto');
  ga('send', 'pageview');

</script>


</body>
</html>
