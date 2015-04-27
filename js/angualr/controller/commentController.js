(function(){
  var app = angular.module('ornagai-comment',[]);

  app.controller('commentController', ['$scope','$rootScope','$http','$sce','userService','currentDataService',function($scope,$rootScope,$http,$sce,userService,currentDataService){ 

    this.newcomment = "";
    var that = this;
    this.isLogin = $scope.login;
    this.username =$scope.username;
    this.selectedWord = {};
    this.loading = true;

    this.renderHTML = function(markdown) {
      return $sce.trustAsHtml(marked(markdown));
    }


    this.comments = [];


    $scope.$on('handleBroadcast', function() {


      that.selectedWord =  currentDataService.message;
      var type = currentDataService.message.type;
      var postid = currentDataService.message.id;
      $http.get('./data/comments/type/'+type+'/id/'+postid)
      .success(function(resp){
        that.loading = false;
        if(resp.length > 0) {
          that.comments = resp;

        }
        else {
          that.comments = [];
        }

      });



    });

    $rootScope.$on('clientLoginBroadcast', function() {

      that.isLogin = userService.userlogin;
      that.username = userService.username;

    });


    
    this.addComment = function() {


      var postData = "comment="+ this.newcomment  + "&dict_id=" + that.selectedWord.id + "&type="+that.selectedWord.type;


      this.loading = true;


      $("#comment-text").attr("disabled", "disabled");

      $http({
        method : 'POST',
        url : './data/comment',
        data: postData,
        headers : {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(resp) {

          $("#comment-text").removeAttr("disabled");

          that.loading = false;
          if(resp.error && resp.error!="") {

          }
          else if(resp.obj) {
            that.newcomment = "";

            that.comments.push(resp.obj);

          }

        });
    }

  }]);

})();
