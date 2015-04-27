(function(){
  var app = angular.module('ornagai-user',[]);

  app.controller('userController', ['$scope','$http','userService',function($scope,$http,userService){ 

    //this.selectedData = {};

    this.showRegisterForm = false;
    this.username = "";
    this.password = "";
    this.errorMsg = "";
    this.email = "";
    var that = this;

    // $scope.$on('handleBroadcast', function() {
    //   //$scope.message = 'ONE: ' + sharedService.message;
    //   //that.selectedData = currentDataService.message;
    // });

    //check the login first
    //and brodcast it
    $http.get('./checklogin').success(function(resp){

      if(resp.login) {
        userService.userlogin = true;
        userService.username = resp.user.username;
        userService.email = resp.user.email;
        userService.broadcastItem();
      }

    });

    this.login = function () {

      if(this.username.trim() == "") {

          this.errorMsg = "Please enter username";
          return;

      }
      else if(this.password.trim() == "") {

        this.errorMsg = "Please enter password";
        return;

      }
      else {
        //login
        this.errorMsg = "";
        var that = this;
        var pwd = userService.encryptPassword(this.password);
        var postData = "username="+ this.username  + "&password=" + encodeURIComponent(pwd);

        this.errorMsg = "Loading...";
        $http({
          method : 'POST',
          url : './login',
          data: postData,
          headers : {'Content-Type': 'application/x-www-form-urlencoded'}
          }).success(function(resp) {
            if(resp.login) {
              userService.userlogin = true;
              userService.username = resp.user.username;
              userService.email = resp.user.email;
              userService.broadcastItem();
              $("#loginFrm").fadeOut();
              that.username = "";
              that.password = "";
              that.errorMsg = "";
              that.email = "";
            }
            else if(resp.error)
              {
                that.errorMsg = resp.error;
              }
            else {
              that.errorMsg = "Wrong username or password";
            }
        });

      }
    }


    this.hideForm = function() {


      this.username = "";
      this.password = "";
      this.errorMsg = "";
      this.email = "";
      $("#loginFrm").fadeOut();
      this.showRegisterForm = false;
    }

    this.register = function ()
    {
      if(this.email == "") {

          this.errorMsg = "Please email username";
          return;

      }
      else if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) == false)
      {
          this.errorMsg = "Email format is invalid";
          return;
      }
      if(this.username.trim() == "") {

          this.errorMsg = "Please enter username";
          return;

      }
      else if(this.password == "") {

        this.errorMsg = "Please enter password";
        return;

      }
      else if(this.confirmpassword == "")
      {
          this.errorMsg = "Please enter confirm password";
          return;
      }
      else if(this.password != this.confirmpassword)
      {
          this.errorMsg = "Password and confirm password are not same";
          return;
      }
      else {
        //login
        this.errorMsg = "Loading...";
        var that = this;
        var pwd = userService.encryptPassword(this.password);
        var postData = "email="+ this.email + "&username="+ this.username  + "&password=" + encodeURIComponent(pwd);
        $http({
          method : 'POST',
          url : './register',
          data: postData,
          headers : {'Content-Type': 'application/x-www-form-urlencoded'}
          }).success(function(resp) {
            if(resp.register) {

              swal("Registration has been completed!", "Please check your email to confirm your account.", "success")

              $("#loginFrm").hide();

              that.username = "";
              that.password = "";
              that.errorMsg = "";
              that.email = "";

            }
            else if(resp.error) {
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
