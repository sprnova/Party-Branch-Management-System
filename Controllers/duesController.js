/**
 * Created by HongYang on 2016/12/21.
 */
app.controller('duesCtrl', function ($routeParams, $scope, $location, $http, $filter) {

    $scope.path_branch = "#branch/" + $routeParams.userName;
    $scope.path_develop = "#develop/" + $routeParams.userName;
    $scope.path_dues = "#dues/" + $routeParams.userName;
});