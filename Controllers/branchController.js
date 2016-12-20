/**
 * Created by HongYang on 2016/12/16.
 */
app.controller('branchCtrl', function ($routeParams, $scope, $location, $http, $filter) {
    $scope.user = $routeParams.userName;

    $scope.path = "#dashBoardOne/" + $routeParams.userName;

    /**************************************************************************************
     **************************************************************************************
     *                                    初始数据获取                                     *
     **************************************************************************************
     *************************************************************************************/
    $http({
        method: 'GET',
        url: '../Logic/infoLogic.php?kind=branchName' + '&name=' + $scope.user,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
        .success(function (data) {
            $scope.branchName = data[0];
            $scope.branchId = data[1];
        });

    $http({
        method: 'GET',
        url: '../Logic/infoLogic.php?kind=general' + '&name=' + $scope.user,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).success(function (data) {
        $scope.infoAll = data;
        $scope.sortBy('develop_date');// 默认按入党时间排序
    });
    /**************************************************************************************
     **************************************************************************************
     *                                    初始数据获取                                      *
     **************************************************************************************
     *************************************************************************************/


    /**************************************************************************************
     **************************************************************************************
     *                                     表格增删改                                      *
     **************************************************************************************
     *************************************************************************************/
    $scope.memberInfoAdd = {};                                              // 对象类型，储存新增成员的信息
    $scope.standard = ['birth_date','develop_date','formal_date','post'];   // 添加操作中可能为空的四个字段
    $scope.standardText = ['','','',''];                                    // 标记上述数组中的字段是否为空
    $scope.add_success = false; $scope.add_error = false;                   // 初始化添加成功和添加错误标记
    /**
     * 添加成员
     */
    $scope.addMember = function () {
        // 判断是否有未填的必填项
        if ($scope.memberInfoAdd['name'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写姓名"
        }
        else if ($scope.memberInfoAdd['class'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写班级"
        }
        else if ($scope.memberInfoAdd['nation'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写民族"
        }
        else if ($scope.memberInfoAdd['place_of_origin'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写籍贯"
        }
        else if ($scope.memberInfoAdd['state'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写当前状态"
        }
        else if ($scope.memberInfoAdd['type'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写学生类型"
        }
        else if ($scope.memberInfoAdd['major'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写专业"
        }
        else if ($scope.memberInfoAdd['phone'] == undefined) {
            $scope.add_error = true;
            $scope.error_msg = "请填写电话号码"
        }
        else {
            // 对为空的项进行'nil'标注
            //for (var i = 0; i < $scope.standard.length; i++) {
            //    if ($scope.memberInfoAdd[$scope.standard[i]] == undefined) {
            //        console.log($scope.standard[i]);
            //        $scope.standardText[i] = 'nil';
            //    } else {
            //        $scope.standardText[i] = $scope.memberInfoAdd[$scope.standard[i]];
            //    }
            //}

            // 发送请求,修改后台数据
            $http({
                method  : 'GET',
                url     : '../Logic/infoLogic.php?kind=newMember&name='+$scope.memberInfoAdd.name
                +'&class='+$scope.memberInfoAdd.class+'&birth_date='+$scope.memberInfoAdd.birth_date
                +'&nation='+$scope.memberInfoAdd.nation+'&place_of_origin='+$scope.memberInfoAdd.place_of_origin
                +'&state='+$scope.memberInfoAdd.state+'&develop_date='+$scope.memberInfoAdd.develop_date
                +'&formal_date='+$scope.memberInfoAdd.formal_date+'&type='+$scope.memberInfoAdd.type
                +'&major='+$scope.memberInfoAdd.major+'&phone='+$scope.memberInfoAdd.phone
                +'&post='+$scope.memberInfoAdd.post+'&branchId='+$scope.branchId,
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data) {
                if (data == 'add_success') {
                    // 在视图中添加记录
                    $scope.infoAll.push($scope.memberInfoAdd);
                    $scope.sortBy('develop_date'); $scope.sortBy('develop_date'); // 排序两次保证默认次序

                    $scope.add_error = false;
                    $scope.add_success = true;
                } else {
                    switch (data) {
                        default:
                            console.log(data);
                    }
                }
            });
        }
    };

    $scope.modify_success = false;      // 初始化修改成功标记
    $scope.modify_error = false;        // 初始化修改失败标记
    $scope.modifyTopic = '';            // 记录待修改字段名称
    $scope.modifyTarget = '';           // 记录待修改字段的值
    $scope.modifyReverse = '';          // 记录字段初值，便于撤销
    $scope.hasSthToDelete = true;       // 标记删除是否成功，若成功，同一模态框中的提交按钮将被隐去，防止误操作
    /**
     * 修改与删除成员信息1.1 -- 点击某个表项后弹出模态框，显示待修改内容的原始值
     * @param j 行号
     * @param topic 待修改属性/删除标志
     * @param originVal 待修改属性/删除的属性原值
     */
    $scope.tableClick = function (j, topic, originVal) {
        $scope.modify_success = false;
        $scope.modify_error = false;
        $scope.hasSthToDelete = true;
        $scope.index = j;
        $scope.fieldName = topic;

        var realIndex = ($scope.currentPage-1)*12+$scope.index; // 真正的记录序号 = 页数*每页条数+当前页中的序号
        $scope.targetName = $scope.infoAll[realIndex]['name']; // 获取字段所有者的姓名
        switch (topic) {
            case 'name':
                $scope.modifyTopic = '姓名';
                break;
            case 'class':
                $scope.modifyTopic = '班级';
                break;
            case 'nation':
                $scope.modifyTopic = '民族';
                break;
            case 'place_of_origin':
                $scope.modifyTopic = '籍贯';
                break;
            case 'major':
                $scope.modifyTopic = '专业';
                break;
            case 'phone':
                $scope.modifyTopic = '手机号码';
                break;
            case 'birth_date':
                $scope.modifyTopic = '出生年月';
                break;
            case 'develop_date':
                $scope.modifyTopic = '发展时间';
                break;
            case 'formal_date':
                $scope.modifyTopic = '转正时间';
                break;
            case 'state':
                $scope.modifyTopic = '当前状态';
                break;
            case 'type':
                $scope.modifyTopic = '学生分类';
                break;
            case 'post':
                $scope.modifyTopic = '职务';
                break;
            default:
        }
        $scope.modifyTarget = originVal;
        $scope.modifyReverse = $scope.modifyTarget;
    };

    $scope.modifyUrl = '';  // 服务器端php程序的地址
    $scope.index = 0;       // 指示当前页面中的记录序号，在tableClick函数中赋值
    $scope.targetName = ''; // 储存待修改记录所有者的姓名，在tableClick函数中赋值
    $scope.fieldName = '';  // 绑定视图中待修改的字段名
    $scope.currentPage = 1; // 当前页号，默认从最前一页开始
    /**
     * 修改与删除成员信息1.2 -- 对某条记录的某个字段进行修改
     */
    $scope.modifyField = function () {
        // 检查是否有不允许为空的数据被修改为空
        // 若为空，修改modify_error状态并终止
        // 不为空，根据情况确定请求的URL
        if (!($scope.fieldName == 'birth_date'||$scope.fieldName == 'develop_date'
            ||$scope.fieldName == 'formal_date'||$scope.fieldName == 'post')) {
            if ($.trim($scope.modifyTarget) == "") {
                $scope.modify_error = true;
                return;
            }
            $scope.modifyUrl = '../Logic/infoLogic.php?kind=modifyMember&memberName='+$scope.targetName+'&fieldName='
                +$scope.fieldName+'&'+$scope.fieldName+'='+$scope.modifyTarget;
            console.log($scope.modifyUrl);
        } else {
            $scope.modifyUrl = '../Logic/infoLogic.php?kind=modifyMember&memberName='+$scope.targetName+'&fieldName='
                +$scope.fieldName+'&'+$scope.fieldName+'='+$scope.modifyTarget;
        }

        // 后台数据更新
        $http({
            method  : 'GET',
            url     : $scope.modifyUrl,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data == 'modify_success') {
                console.log(data);
                // 前台数据更新 -- 必须在后台确认成功后
                var realIndex = ($scope.currentPage-1)*12+$scope.index;            // 真正的记录序号 = 页数*每页条数+当前页中的序号
                $scope.infoAll[realIndex][$scope.fieldName] = $scope.modifyTarget;
                $scope.sortBy('develop_date'); $scope.sortBy('develop_date');      // 排序两次保证与默认排序次序一致

                // 改变状态
                $scope.modify_success = true; // 显示提示信息
                $scope.modify_error = false;
            } else {
                switch (data) {
                    default:
                        alert(data);
                }
            }
        });
    };

    /**
     * 修改与删除成员信息2 -- 删除成员信息记录
     * @param memberName 记录所有人姓名
     */
    $scope.deleteRecord = function (memberName) {
        // 后台数据更新
        $http({
            method  : 'GET',
            url     : '../Logic/infoLogic.php?kind=deleteMember&memberName='+memberName,
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data) {
            if (data == 'delete_success') {
                // 前台数据更新 -- 必须在后台确认成功后
                var realIndex = ($scope.currentPage-1)*12+$scope.index;       // 真正的记录序号 = 页数*每页条数+当前页中的序号
                $scope.infoAll.splice(realIndex,1);                           // 删除realIndex指向的那条记录
                $scope.sortBy('develop_date'); $scope.sortBy('develop_date'); // 排序两次保证与默认排序次序一致

                // 改变状态 -- 禁止本次使用同一模态框进行第二次删除操作
                $scope.hasSthToDelete = false;
            } else {
                switch (data) {
                    default:
                        alert(data);
                }
            }
        });
    };

    /**************************************************************************************
     **************************************************************************************
     *                                     表格增删改                                      *
     **************************************************************************************
     *************************************************************************************/

    /**************************************************************************************
     **************************************************************************************
     *                                    表格排序                                         *
     **************************************************************************************
     *************************************************************************************/
    $scope.isopen = {"class": false, "birth_date": false, "develop_date": false, "formal_date": false};

    /**
     * 表格排序函数
     * @param str：排序的类型
     */
    $scope.sortBy = function (str) {
        if (str != 'name') {
            $scope.infoAll = $filter("orderBy")($scope.infoAll, str, $scope.isopen[str]);
            $scope.isopen[str] = !$scope.isopen[str];
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
    $scope.by = function (kind) {
        return function (o, p) {
            var a, b;
            a = o[kind];
            b = p[kind];
            return a.localeCompare(b);
        }
    };

    /**
     * 确定每页显示的数据
     */
    $scope.updatePage = function () {
        $scope.pageSize = 12;
        $scope.pages = Math.ceil($scope.infoAll.length / $scope.pageSize); //页数
        $scope.newPages = $scope.pages > 5 ? 5 : $scope.pages;
        $scope.pageList = [];
        $scope.selPage = 1;

        //设置表格数据源(分页)
        $scope.setData = function () {
            $scope.items = $scope.infoAll.slice(($scope.pageSize * ($scope.selPage - 1)), ($scope.selPage * $scope.pageSize));//通过当前页数筛选出表格当前显示数据
        };
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
                for (var i = (page - 3); i < ((page + 2) > $scope.pages ? $scope.pages : (page + 2)); i++) {
                    newpageList.push(i + 1);
                }
                $scope.pageList = newpageList;
            }
            $scope.selPage = page;
            $scope.setData();
            $scope.isActivePage(page);
            $scope.currentPage = page;
            console.log("选择的页：" + page);
        };
    };

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
    };

    /**
     * 下一页
     * @constructor
     */
    $scope.Next = function () {
        $scope.selectPage($scope.selPage + 1);
    };
    /**************************************************************************************
     **************************************************************************************
     *                                    表格排序                                         *
     **************************************************************************************
     *************************************************************************************/
});