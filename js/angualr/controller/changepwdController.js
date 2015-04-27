(function(){
  var app = angular.module('ornagai-changepwd',[]);

  app.controller('changepwdController', ['$scope','$http','userService',function($scope,$http,userService){ 

    this.errorMsg = "";
    this.currentPwd = "";
    this.newPwd = "";
    this.confirmPwd = "";

    var that = this;

    this.hide = function() {
      $("#changePwdFrm").fadeOut();
    }

    this.update = function() {
      this.errorMsg = "";

      if(this.currentPwd == "")
      {
          this.errorMsg = "current password is required";
          return;
      }
      else if(this.newPwd == "")
      {
          this.errorMsg = "new password is required";
          return;
      }
      else if(this.newPwd != this.confirmPwd)
      {
          this.errorMsg = "new password and current password msut same";
          return;
      }
      else {

        this.errorMsg = "Loading...";
        var postData = "pwd="+ encodeURIComponent(userService.encryptPassword(this.currentPwd))  + "&newpwd=" + encodeURIComponent(userService.encryptPassword(this.newPwd));
        $http({
          method : 'POST',
          url : './data/editpassword',
          data: postData,
          headers : {'Content-Type': 'application/x-www-form-urlencoded'}
          }).success(function(resp) {
            
            if(resp.update) {

              that.errorMsg = "";
              that.currentPwd = "";
              that.newPwd = "";
              that.confirmPwd = "";
              $("#changePwdFrm").fadeOut();

            }
            else if(resp.error){
              that.errorMsg = resp.error;
            }
            else {
              that.errorMsg = "System Error";
            }
          });

      }
    }


  }]);


})();
