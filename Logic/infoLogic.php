<?php
/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/18
 * Time: 10:16
 */
require_once("../DataAccess/DataProcessor.php");
require_once("../DataAccess/BranchMember.php");

switch ($_GET['kind']) {
    case 'general': // 获取基本信息
        $user_name =  $_GET['name'];
        echo getInfo($user_name);
        break;
    case 'branchName': // 获取支部名称
        $user_name =  $_GET['name'];
        echo getBranchName($user_name);
        break;
    case 'newMember':  // 添加支部成员
        $name = $_GET['name'];
        $class = $_GET['class'];
        $birth_date = $_GET['birth_date']=='nil'?null:$_GET['birth_date'];
        $nation = $_GET['nation'];
        $place_of_origin = $_GET['place_of_origin'];
        $state = $_GET['state'];
        $develop_date = $_GET['develop_date']=='nil'?null:$_GET['develop_date'];
        $formal_date = $_GET['formal_date']=='nil'?null:$_GET['formal_date'];
        $type = $_GET['type'];
        $major = $_GET['major'];
        $phone = $_GET['phone'];
        $post = $_GET['post']=='nil'?null:$_GET['post'];

        $newMember = new BranchMember($name,$class,$birth_date,$nation,$place_of_origin,
            $state,$develop_date,$formal_date,$type,$major,$phone,$post);

        addMember($newMember);

        break;
    case 'modifyMember': // 修改成员信息
        $memberName = $_GET['memberName'];
        $fieldName = $_GET['fieldName'];
        $val = $_GET[$fieldName];
        echo modifyMember($memberName,$fieldName,$val);
        break;
    case 'deleteMember': // 删除成员记录
        $memberName = $_GET['memberName'];
        echo $memberName;
        break;
    default:
}

function getBranchName($user) {
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_getBranchName = "SELECT DISTINCT b.branch_name FROM branch_member m, branch b
            WHERE m.branch_id = b.branch_id AND b.branch_id =
		    (SELECT branch_id FROM branch_member WHERE member_name='".$user."');";
        $result = $conn->query($sql_getBranchName);
        if($row = $result->fetch_assoc()) {
            return $row["branch_name"];
        }
        $dp->closeConn();
    }
    return null;
}

function getInfo($user) {
    $rst = array();
    $i = 0;
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_getInfo = "SELECT m.member_name, i.class, i.birth_date, i.nation, i.place_of_origin, i.state, i.develop_date, i.formal_date, i.type, i.major, i.phone, i.post
                        FROM branch_member m, branch_member_info i
                        WHERE m.member_id = i.member_id
		                    AND m.branch_id = (SELECT branch_id FROM branch_member WHERE member_name = '".$user."');";
        $result = $conn->query($sql_getInfo);
        while($row = $result->fetch_assoc()) {
            $rst[$i] = new BranchMember($row["member_name"],$row["class"],$row["birth_date"],$row["nation"],$row["place_of_origin"],$row["state"],
                $row["develop_date"],$row["formal_date"],$row["type"],$row["major"],$row["phone"],$row["post"]);
            $i++;
        }
        $dp->closeConn();
        $objJSon = json_encode($rst);
        return $objJSon;
    }
    return null;
}

function addMember($newMember) {
    echo $newMember->name."\n".
        $newMember->class."\n".$newMember->birth_date."\n".$newMember->nation."\n".$newMember->place_of_origin.
        "\n".$newMember->state."\n".$newMember->develop_date ."\n".$newMember->formal_date."\n"
        .$newMember->type."\n".$newMember->major."\n".$newMember->phone."\n".$newMember->post;
}

function modifyMember($name,$field,$value) {
    $result = "modify_success";
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_modify = "";
        if ($field=="name") {
            $sql_modify = "UPDATE branch_member SET member_name='".$value."' WHERE member_name='".$name."'";
        } else {
            $sql_modify = "UPDATE branch_member_info SET ".$field."='".$value
                ."' WHERE member_id=(SELECT member_id FROM branch_member WHERE member_name='".$name."')";
        }
        mysqli_query($conn,$sql_modify);
        $dp->closeConn();
    } else {
        $result = "modify_fail_connectDB";
    }
    return $result;
}