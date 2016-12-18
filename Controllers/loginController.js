/**
 * Created by HongYang on 2016/12/10.
 */
app.controller('loginCtrl', function ($scope, $location, $rootScope, $http) {
    $scope.registerData = {};
    $scope.loginData = {};
    $scope.login_need_name = "no_need";
    $scope.login_need_psw = "no_need";
    $scope.register_need_name = "no_need";
    $scope.register_need_validate = "no_need";
    $scope.register_need_password = "no_need";

    $scope.loginForm = function() {
        $scope.login_need_name = "no_need";
        $scope.login_need_psw = "no_need";
        $scope.loginData["kind"] = "1";

        if ($scope.loginData.login_name == undefined) {
            $scope.login_need_name = "请填写用户名";
        } else if ($scope.loginData.login_psw == undefined) {
            $scope.login_need_psw = "请填写密码";
        } else {
            $http({
                method  : 'POST',
                url     : '../Logic/loginLogic.php',
                data    : $.param($scope.loginData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                console.log(data);
                switch (data) {
                    case "login_success1":  // 支委
                        $location.path('/branch/' + $scope.loginData.login_name);
                        break;
                    case "login_fail_wrong":
                        $scope.login_need_name = "请确保用户名正确";
                        $scope.login_need_psw = "请确保密码正确";
                        break;
                    case "login_fail_connectionFailure":
                        alert("数据库连接出现故障，请稍后重试");
                        break;
                    default:
                        alert("服务器内部错误，登录失败");
                }
            });
        }
    };
    $scope.registerForm = function () {
        $scope.registerData["kind"] = "2";
        $scope.register_need_name = "no_need";
        $scope.register_need_validate = "no_need";
        $scope.register_need_password = "no_need";

        if ($scope.registerData.rsg_name == undefined) {
            $scope.register_need_name = "请填写姓名";
        } else if ($scope.registerData.rsg_valid == undefined) {
            $scope.register_need_validate = "请填写验证信息";
        } else if ($scope.registerData.rsg_psw == undefined) {
            $scope.register_need_password = "请填写密码";
        } else if ($.trim($scope.registerData.rsg_name) == "") {
            $scope.register_need_name = "姓名不能全为空格";
        } else if ($.trim($scope.registerData.rsg_valid) == "") {
            $scope.register_need_validate = "验证信息不能全为空格";
        } else if ($.trim($scope.registerData.rsg_psw).length != $scope.registerData.rsg_psw.length) {
            $scope.register_need_password = "密码中不能出现空格";
        } else {
            $http({
                method  : 'POST',
                url     : '../Logic/loginLogic.php',
                data    : $.param($scope.registerData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                switch (data) {
                    case "register_success":
                        alert("注册信息提交成功，请等待管理员核准");
                        break;
                    case "register_fail_userAlready":
                        alert("系统中已有同名用户，请联系管理员");
                        break;
                    case "register_fail_stagingAlready":
                        alert("您已提交过注册申请");
                        break;
                    case "register_fail_connectionFailure":
                        alert("数据库连接出现故障，请稍后重试");
                        break;
                    default:
                        alert("服务器内部错误，注册申请提交失败");
                }
            });
        }
    };
});