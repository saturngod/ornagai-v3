(function(){
  var app = angular.module('ornagai',[ 'ngRoute','ornagai-changepwd','ornagai-search','ornagai-comment','ornagai-result','ornagai-usermenu','ornagai-user','ornagai-addform']);

  app.config(['$routeProvider', function($routeProvider) {
  $routeProvider.
  when('/', {
    templateUrl: './view/resultView.html',
    controller : 'resultController',
    controllerAs : "resultCtrl"
  }).
  when('/search/:query', {
    templateUrl: './view/resultView.html',
    controller : 'resultController',
    controllerAs : "resultCtrl"
  }).
  otherwise({
    redirectTo: '/'
  });
}]);

  //service style, probably the simplest one
  app.service('ttsService', function() {

      this.isMyanmar = function(text) {

        var patt = new RegExp("[က-႟]");
        return patt.test(text);
      }
      this.speak = function(text) {

          // Initialize speech synthesis, we use polyfill only when speech synthesis is not available
          var fallbackSpeechSynthesis = window.getSpeechSynthesis();
          var fallbackSpeechSynthesisUtterance = window.getSpeechSynthesisUtterance();

          // To use polyfill directly call
          // var fallbackSpeechSynthesis = window.speechSynthesisPolyfill;
          // var fallbackSpeechSynthesisUtterance = window.SpeechSynthesisUtterancePolyfill;

          var u = new fallbackSpeechSynthesisUtterance(text);
          u.corsProxyServer = "./tts.php?csurl=";
          u.lang = 'en-US';
          u.volume = 1.0;
          u.rate = 1.0;
          // u.onend = function(event) {
          //   console.log('Finished in ' + event.elapsedTime + ' seconds.');
          // };
          fallbackSpeechSynthesis.speak(u);

      };
  });

  app.factory('currentDataService', function ($rootScope) {

    var sharedService = {};

    sharedService.message = '';

    sharedService.prepForBroadcast = function(msg) {
      this.message = msg;
      this.broadcastItem();
    };

    sharedService.broadcastItem = function() {
      $rootScope.$broadcast('handleBroadcast');
    };

    return sharedService;

  });

  app.factory('userService',function($rootScope) {

    var userService = {};

    userService.userlogin = false;
    userService.username = "";
    userService.email = "";

    userService.encryptPassword = function(password) {
      var rsa = new RSAKey();
      rsa.setPublic("C9AF2D1CBB4F823C46457DA5EAE24F3422BE0E2B3E81E3CF04F5C00B16487DDF96BD901F39577F7F3650882A9292BBB0272D872A28E867FA0A89A06DEE4373DF","10001");
      var res = rsa.encrypt(password);
      return hex2b64(res);
    }


    userService.broadcastItem = function() {
      $rootScope.$broadcast('clientLoginBroadcast'); //just for UI, server side check the session for data
    };

    return userService;

  });


  app.directive('loginForm', [function($http){
	// Runs during compile
	return {
		restrict : 'E',
		templateUrl : 'view/loginform.html',
    controller : 'userController',
		controllerAs : "userCtr"
	};


  }]);


  app.directive('addForm', [function($http){
  // Runs during compile
  return {
    restrict : 'E',
    templateUrl : 'view/addform.html',
    controller : 'addFormController',
    controllerAs : "addCtr"
  }
  }]);

  app.directive('changepwdForm', [function($http){
  // Runs during compile
  return {
    restrict : 'E',
    templateUrl : 'view/changepassword.html',
    controller : 'changepwdController',
    controllerAs : "chPwdCtr"
  }
  }]);


app.run(['$route', '$rootScope', '$location', function ($route, $rootScope, $location) {
    var original = $location.path;
    $location.path = function (path, reload) {
        if (reload === false) {
            var lastRoute = $route.current;
            var un = $rootScope.$on('$locationChangeSuccess', function () {
                $route.current = lastRoute;
                un();
            });
        }
        return original.apply($location, [path]);
    };
}])


  //future
  app.directive('commentBox',[function() {

    return {
      restrict : 'E',
      templateUrl : 'view/commentbox.html',
      scope: {
        postid: '=postid',
        word: '=word',
        login: '=login',
        username : '=username',
        type : '=type'
      },
      controller : 'commentController',
      controllerAs : "commentCtr"
    }

  }]);


})();
