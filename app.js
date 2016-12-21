/**
 * Created by HongYang on 2016/12/10.
 */
var app = angular.module('app', ['ngRoute']);

app.config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: 'Web/login.html',
            controller: 'loginCtrl'
        })
        .when('/branch/:userName', {
            templateUrl: 'Web/home/branch.html',
            controller: 'branchCtrl'
        })
        .when('/develop/:userName', {
            templateUrl: 'Web/home/develop.html',
            controller: 'developCtrl'
        })
        .when('/dues/:userName', {
            templateUrl: 'Web/home/dues.html',
            controller: 'duesCtrl'
        })
        .when('/result/:wd', {
            templateUrl: 'Web/searchResult.html',
            controller: 'resultCtrl'
        })
        .when('/detail/:ttl', {
            templateUrl: 'Web/detail.html',
            controller: 'detailCtrl'
        })
});

app
    .controller('indexCtrl', function ($scope, $window, $document, $rootScope) {
        $scope.show = false;
        $rootScope.$on('show', function () {
            $scope.show = true;
        });
    })
    .controller('resultCtrl', function ($scope, $http, $routeParams, $location, $window, $document) {
        $scope.word = $routeParams.wd;
        $scope.permission = false;

        $http({
            method: 'POST',
            url: 'https://en.wikipedia.org/w/api.php?' + 'action=opensearch&search='
            + $scope.word + '&limit=max&format=json',
            data: '',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function (data) {
            $scope.result = data;
            $scope.keyword = $scope.result[0];
            $scope.list = $scope.result[1];
            $scope.abstracts = $scope.result[2];
            $scope.links = $scope.result[3];
        });

        $scope.showWholeList = function() {
            $scope.permission = true;
        }

        $scope.moreInfo = function (index) {
            $location.path('/detail/' + $scope.list[index]);
        }
    })
    .controller('detailCtrl', function ($scope, $routeParams, $http) {
        $scope.title = $routeParams.ttl;
        $http.get(
            "http://en.wikipedia.org/w/api.php?action=query&prop=revisions&rvprop=content&format=xml&redirects=1&titles="
            + $scope.title
        ).success(function(data) {
            $scope.content = data;

            var subStr1 = '"preserve">';
            var subStr2 = '</rev>';
            var i = $scope.content.indexOf(subStr1)+11;
            var j = $scope.content.indexOf(subStr2);
            $scope.contentCore = $scope.content.substring(i,j);


            console.log(i);
            console.log($scope.contentCore);
        });
    });
