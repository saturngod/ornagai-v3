(function(){
  var app = angular.module('ornagai-usermenu',[]);

  app.controller('userMenuController', ['$scope',function($scope){ 

    this.menuhide = true;
    this.showmenu = function() {

      if(this.menuhide) {
        //show menu
        $("#usermenu").css("opacity",'1');
        $("#usermenu").css("transform",'scale(1) translate3d(0px, 0px, 0px)');
      }
      else {
        //hide menu
        $("#usermenu").css("opacity",'0');
        $("#usermenu").css("transform",'scale(0.35) translate3d(0px, 0px, 0px)');
      }

      this.menuhide = !this.menuhide;

    }



  }]);


})();
