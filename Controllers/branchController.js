/**
 * Created by HongYang on 2016/12/16.
 */
app.controller('branchCtrl', function ($routeParams, $scope, $location, $http, $filter) {
    $scope.user = $routeParams.userName;
    $scope.path = "#dashBoardOne/"+$routeParams.userName;
    //$scope.isopen=false;//默认升序
    $scope.isopen = {"class":false,"birth_date":false,"develop_date":false,"formal_date":false};

    $http({
        method: 'GET',
        url: '../Logic/infoLogic.php?kind=branchName' + '&name=' + $scope.user,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
        .success(function(data) {
        $scope.branchName = data;
    });

    $http({
        method  : 'GET',
        url     : '../Logic/infoLogic.php?kind=general'+'&name='+$scope.user,
        headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function(data) {
        $scope.infoAll = data;
        $scope.sortBy('develop_date');// 默认按入党时间排序
    });

    /**
     * 表格排序函数
     * @param str：排序的类型
     */
    $scope.sortBy = function(str){
        if (str != 'name') {
            $scope.infoAll=$filter("orderBy")($scope.infoAll,str,$scope.isopen[str]);
            $scope.isopen[str]=!$scope.isopen[str];
        } else {
            $scope.infoAll.sort($scope.by('name'));
        }
        $scope.updatePage();
    };

    /**
     * 姓名排序辅助函数
     * @param kind：排序类型
     * @returns {Function}
     */
    $scope.by = function(kind) {
        return function(o,p) {
            var a, b;
            a = o[kind]; b = p[kind];
            return a.localeCompare(b);
        }
    }

    /**
     * 确定每页显示的数据
     */
    $scope.updatePage = function() {
        $scope.pageSize = 12;
        $scope.pages = Math.ceil($scope.infoAll.length / $scope.pageSize); //页数
        $scope.newPages = $scope.pages > 5 ? 5 : $scope.pages;
        $scope.pageList = [];
        $scope.selPage = 1;

        //设置表格数据源(分页)
        $scope.setData = function () {
            $scope.items = $scope.infoAll.slice(($scope.pageSize * ($scope.selPage - 1)), ($scope.selPage * $scope.pageSize));//通过当前页数筛选出表格当前显示数据
        }
        $scope.items = $scope.infoAll.slice(0, $scope.pageSize);
        //分页要repeat的数组
        for (var i = 0; i < $scope.newPages; i++) {
            $scope.pageList.push(i + 1);
        }
        //打印当前选中页索引
        $scope.selectPage = function (page) {
            //不能小于1大于最大
            if (page < 1 || page > $scope.pages) return;
            //最多显示分页数5
            if (page > 2) {
                //因为只显示5个页数，大于2页开始分页转换
                var newpageList = [];
                for (var i = (page - 3) ; i < ((page + 2) > $scope.pages ? $scope.pages : (page + 2)) ; i++) {
                    newpageList.push(i + 1);
                }
                $scope.pageList = newpageList;
            }
            $scope.selPage = page;
            $scope.setData();
            $scope.isActivePage(page);
            console.log("选择的页：" + page);
        };
    }

    /**
     * 设置当前选中页样式
     * @param page
     * @returns {boolean}
     */
    $scope.isActivePage = function (page) {
        return $scope.selPage == page;
    };
    /**
     * 上一页
     * @constructor
     */
    $scope.Previous = function () {
        $scope.selectPage($scope.selPage - 1);
    }

    /**
     * 下一页
     * @constructor
     */
    $scope.Next = function () {
        $scope.selectPage($scope.selPage + 1);
    };
});