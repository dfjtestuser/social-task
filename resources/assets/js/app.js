require('./bootstrap');

const app = angular.module('postApp', ['infinite-scroll']);

app.controller('PostCtrl', ['$scope','$http','$timeout', function($scope,$http,$timeout) {

    $scope.loading = true;
    $scope.loadingError = false;
    $scope.offset = 0;
    $scope.posts = [];
    $scope.srcword = '';
    $scope.scroll = { busy: false };


    $scope.loadPosts = function() {
        $http.get('/post/'+$scope.offset).then(function(response) {
            $scope.posts = response.data;
            $scope.loading = false;

        });
    }


    $scope.loadMore = function() {
        // if(!$scope.loadingError) {
        //     return;
        // }
        var offset = $scope.posts.length;
        if(!$scope.scroll.busy) {
            $scope.scroll.busy = true;
            setTimeout(function(){
                $http.get('/post/'+offset+'/'+$scope.srcword).success(function(data) {
                    if(data.length > 0) {
                        $scope.posts = $scope.posts.concat(data);
                        $scope.scroll.busy = false;
                    } else {
                        $scope.scroll.busy = false;
                    }
                });
            },300);
        }
        return;
    }


    $scope.filterPosts = function () {
        if($scope.srcword.length > 2) {
            $scope.scroll.busy = false;
            $http.get('/post/0/'+$scope.srcword).success(function(data) {
                $scope.posts = data;
            });
        }
    }



    $scope.checkUpdate = function() {
        $http.get('/checkupdate').then(
            function(response) {
                if(response.data.status == 'error') {
                    $scope.loading = false;
                    $scope.scroll.busy = false;
                    $scope.loadingError = true;
                    $scope.errorText = response.data.message;
                }

                if(response.data.status == 'processing') {
                    $scope.loading = true;
                    $scope.loading = true;
                    $timeout($scope.checkUpdate(),60000);
                }

                if(response.data.status == 'done') {
                    $timeout($scope.loadPosts(),1000);
                }
            },
            function(httpError) {


            }
        )
    }
    $scope.checkUpdate();
}]);

