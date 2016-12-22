/**
 * Created by HongYang on 2016/12/21.
 */
app.controller('developCtrl', function ($routeParams, $scope, $location, $http, $filter) {

    $scope.path_branch = "#branch/" + $routeParams.userName;
    $scope.path_develop = "#develop/" + $routeParams.userName;
    $scope.path_dues = "#dues/" + $routeParams.userName;

    $scope.dec = false; // 默认升序
    $http({
        method  : 'GET',
        url     : '../Logic/developLogic.php?kind=getFormal' + '&name=' + $routeParams.userName,
        headers : {'Content-Type': 'application/x-www-form-urlencoded'}
    }).success(function(data){
        console.log(data);
        $scope.formalItems = data;
        $scope.formalItems = $filter("orderBy")($scope.formalItems, 'formal_date', $scope.dec);
        $scope.dec = !$scope.dec;
    });

    $scope.onClick = function() {
        $http({
            method  : 'GET',
            url     : '../Logic/testFile.php?filename=test.txt',
            headers : {'Content-Type': 'application/x-www-form-urlencoded'}
        });
    }

});