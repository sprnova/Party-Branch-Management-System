/**
 * Created by HongYang on 2016/12/21.
 */
app.controller('duesCtrl', function ($routeParams, $scope, $location, $http) {

    $scope.path_branch = "#branch/" + $routeParams.userName;
    $scope.path_develop = "#develop/" + $routeParams.userName;
    $scope.path_dues = "#dues/" + $routeParams.userName;

    $http({
        method: 'GET',
        url: '../Logic/duesLogic.php?kind=monthlyDues' + '&name=' + $routeParams.userName,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).success(function (data) {
        $scope.memberCount = data;
    });

    $scope.total = '-';
    $scope.checkDues = function () {
        if ($scope.startDate > $scope.finishDate) {
            alert('起始日期必须不晚于结束日期！');
            return;
        }
        $http({
            method: 'GET',
            url: '../Logic/duesLogic.php?kind=duesCheck' + '&name=' + $routeParams.userName + '&start=' + $scope.startDate
            + '&finish=' + $scope.finishDate,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function (data) {
            $scope.dueItems = data;
            $scope.total = 0;
            for (var i = 0; i < $scope.dueItems.length; i++) {
                $scope.total += parseFloat($scope.dueItems[i].due);
            }
            $scope.total = $scope.total.toFixed(1); // 取一位小数
        });
    };
});