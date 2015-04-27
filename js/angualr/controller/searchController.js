(function(){
  var app = angular.module('ornagai-search', [ ]);


app.controller('searchController', ['$location','$scope','$http','currentDataService','userService','ttsService',function($location,$scope,$http,currentDataService,userService,ttsService){ 

  this.query= "";
  this.results = [];

  this.selectedData = {};
  this.showloading = false;

  this.isLogin = false;

  this.username = "";

  var that = this;

  this.tts = function(data) {

    if(!ttsService.isMyanmar(data.Word))
    {
      ttsService.speak(data.Word);

    }
    else {
      ttsService.speak(data.def);
    }
  }

  this.showLoginForm = function()
  {
    $("#loginFrm").fadeIn();
    $("#usermenu").css("opacity",'0');
    $("#usermenu").css("transform",'scale(0.35) translate3d(0px, 0px, 0px)');
  }

  this.showChangePassword = function()
  {
    $("#changePwdFrm").fadeIn();
    $("#usermenu").css("opacity",'0');
    $("#usermenu").css("transform",'scale(0.35) translate3d(0px, 0px, 0px)');
  }

  this.showAdd = function() {
    $("#addFrm").fadeIn();
    $("#usermenu").css("opacity",'0');
    $("#usermenu").css("transform",'scale(0.35) translate3d(0px, 0px, 0px)');
  }

  this.searchKeyup = function(keyCode)
  {
      if(keyCode == 13)
      {
          $(".tt-dropdown-menu").hide();
          this.search();
      }
  }


  $scope.$on("searchQuery", function (event, args) {
    that.query = args.query;
    that.search();
  });

  this.search = function() {

    if(that.query.trim() == "") return;
    this.showloading = true;

    $http.get('./search/'+that.query).success(function(data){

      that.showloading = false;
      that.results = data;

    });

  }

  this.clickResult = function(result) {

    this.selectedData = result;
    
    currentDataService.prepForBroadcast(result);

    $location.path("/search/"+result.Word,false);

  }



  $scope.$on('clientLoginBroadcast', function() {

    that.isLogin = userService.userlogin;
    that.username = userService.username;
    console.log('search login');

  });


  this.logout = function() {
      this.isLogin = false;
      this.username = "";

      $("#usermenu").css("opacity",'0');
      $("#usermenu").css("transform",'scale(0.35) translate3d(0px, 0px, 0px)');

      $http.get('./logout');
  }

}]); 


})();
