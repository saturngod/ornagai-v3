(function(){
  var app = angular.module('ornagai-result',[]);

  app.controller('resultController', ['$scope','$rootScope','$http','$routeParams','currentDataService','ttsService','userService',function($scope,$rootScope,$http,$routeParams,currentDataService,ttsService,userService){ 

    this.selectedData = {};

    var that = this;

    this.isLogin = false;
    this.username = "";

    if($routeParams.query)
    {
      //search it
      console.log("Search ... " +$routeParams.query);
      $rootScope.$broadcast("searchQuery", {query:  $routeParams.query});
    }


    $scope.$on('handleBroadcast', function() {
      //$scope.message = 'ONE: ' + sharedService.message;
      that.selectedData = currentDataService.message;
    });

    //shouldn't write like that
    //but for now, I don't know what is the best way
    //this one is for temp

    $rootScope.$on('clientLoginBroadcast', function() {

      that.isLogin = userService.userlogin;
      that.username = userService.username;
      console.log('resultView');

    });

    this.tts = function(data) {

      if(!ttsService.isMyanmar(data.Word))
      {
        ttsService.speak(data.Word);
      }
      else {
        ttsService.speak(data.def);
      }
    }

  }]);


})();
