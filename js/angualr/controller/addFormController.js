(function(){
  var app = angular.module('ornagai-addform', []);


app.controller('addFormController', ['$scope','$http',function($scope,$http){ 

  this.word = "";
  this.state = "";
  this.def = "";
  this.errorMsg = "";
  var that = this;

  this.hide = function() {
    that.word = "";
    that.state = "";
    that.def = "";
    this.errorMsg = "";

    $("#addFrm").fadeOut();
  }

  this.insert = function() {


    if(this.word.trim() == "") {
      this.errorMsg = "Word is required";
      return;
    }
    else if(this.state.trim() == "") {
      this.errorMsg = "State is required";
      return;
    }
    else if(this.def.trim() == "") {
      this.errorMsg = "Def is required";
      return;
    }
    this.errorMsg = "Loading...";

    var postData = "word="+ encodeURIComponent(this.word)  + "&state=" + encodeURIComponent(this.state) + "&def=" + encodeURIComponent(this.def);
    $http({
      method : 'POST',
      url : './data/add',
      data: postData,
      headers : {'Content-Type': 'application/x-www-form-urlencoded'}
      }).success(function(resp) {

        if(resp.data)
        {
          //done
          swal("Thank you!", "We will review your new word,soon.", "success")
          //hide it
          that.word = "";
          that.state = "";
          that.def = "";
          that.errorMsg = "";

          $("#addFrm").fadeOut();
        }
        else if(resp.error)
          {
            that.errorMsg = resp.error;
          }


      });

  }

}]);


})();
