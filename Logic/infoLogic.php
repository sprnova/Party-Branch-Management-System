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
        $user_name = $_GET['name'];
        echo getInfo($user_name);
        break;
    case 'branchName': // 获取支部名称
        $user_name = $_GET['name'];
        echo getBranchName($user_name);
        break;
    case 'newMember':  // 添加支部成员
        $name = $_GET['name'];
        $class = $_GET['class'];
        $birth_date = $_GET['birth_date'] == 'undefined' ? "NULL" : "'".$_GET['birth_date']."'";
        $nation = $_GET['nation'];
        $place_of_origin = $_GET['place_of_origin'];
        $state = $_GET['state'];
        $develop_date = $_GET['develop_date'] == 'undefined' ? "NULL" : "'".$_GET['develop_date']."'";
        $formal_date = $_GET['formal_date'] == 'undefined' ? "NULL" : "'".$_GET['formal_date']."'";
        $type = $_GET['type'];
        $major = $_GET['major'];
        $phone = $_GET['phone'];
        $post = $_GET['post'] == 'undefined' ? "NULL" : "'".$_GET['post']."'";
        $gender = $_GET['gender'];
        $card = "'".$_GET['card']."'";
        $branch_id = "'".$_GET['branchId']."'";

        $newMember = new BranchMember($name, $class, $birth_date, $nation, $place_of_origin,
            $state, $develop_date, $formal_date, $type, $major, $phone, $post, $gender, $card);

        echo addMember($newMember,$branch_id);
        break;
    case 'modifyMember': // 修改成员信息
        $memberName = $_GET['memberName'];
        $fieldName = $_GET['fieldName'];
        $val = $_GET[$fieldName];
        echo modifyMember($memberName, $fieldName, $val);
        break;
    case 'deleteMember': // 删除成员记录
        $memberName = $_GET['memberName'];
        echo deleteMember($memberName);
        break;
    default:
}

/**获取支部名称及id
 * @param $user 书记姓名
 * @return null
 */
function getBranchName($user)
{
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_getBranchName = "SELECT DISTINCT b.branch_name,b.branch_id FROM branch_member m, branch b
            WHERE m.branch_id = b.branch_id AND b.branch_id =
		    (SELECT branch_id FROM branch_member WHERE member_name='" . $user . "');";
        $result = $conn->query($sql_getBranchName);
        if ($row = $result->fetch_assoc()) {
            $objJSon = json_encode([$row["branch_name"],$row["branch_id"]]);
            return $objJSon;
        }
        $dp->closeConn();
    }
    return null;
}

/**获取支部概要信息
 * @param $user 书记姓名
 * @return null|string
 */
function getInfo($user)
{
    $rst = array();
    $i = 0;
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_getInfo = "SELECT m.member_name, i.class, i.birth_date, i.nation, i.place_of_origin, i.state, i.develop_date, i.formal_date, i.type, i.major, i.phone, i.post, i.gender, i.card
                        FROM branch_member m, branch_member_info i
                        WHERE m.member_id = i.member_id
		                    AND m.branch_id = (SELECT branch_id FROM branch_member WHERE member_name = '" . $user . "');";
        $result = $conn->query($sql_getInfo);
        while ($row = $result->fetch_assoc()) {
            $rst[$i] = new BranchMember($row["member_name"], $row["class"], $row["birth_date"], $row["nation"], $row["place_of_origin"], $row["state"],
                $row["develop_date"], $row["formal_date"], $row["type"], $row["major"], $row["phone"], $row["post"], $row["gender"], $row["card"]);
            $i++;
        }
        $dp->closeConn();
        $objJSon = json_encode($rst);
        return $objJSon;
    }
    return null;
}

/**添加一条记录
 * @param $newMember 待添加的对象
 * @param $branchId 支部id
 * @return str
 */
function addMember($newMember,$branchId)
{
    $result_str = "add_success";
    $member_id = 0;
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_add_primary = "INSERT INTO branch_member (member_id, member_name, branch_id) VALUES (0,"."'".$newMember->name."'".",".$branchId.")";
        $sql_get_id = "SELECT member_id FROM branch_member WHERE member_name='".$newMember->name."' AND branch_id=".$branchId;

        try {
            $conn->query($sql_add_primary);
            $result = $conn->query($sql_get_id);
            if ($row = $result->fetch_assoc()) {
                $member_id = $row['member_id'];
                $sql_add_foreign = "INSERT INTO branch_member_info (member_id,class,birth_date,nation,place_of_origin,state,type,develop_date,formal_date,major,phone,post,gender,card)VALUES ("
                    .$member_id.",'".$newMember->class."',".$newMember->birth_date.",'".$newMember->nation
                    ."','".$newMember->place_of_origin."','".$newMember->state."','".$newMember->type."',".$newMember->develop_date
                    .",".$newMember->formal_date.",'".$newMember->major."','".$newMember->phone."',".$newMember->post.",".$newMember->gender.",".$newMember->card.")";
                $conn->query($sql_add_foreign);
            } else {
                return "Fatal_fail";
            }
        } catch(Exception $e) {
            $result_str = $e;
        } finally {
            $dp->closeConn();
        }
    } else {
        $result_str = "add_fail_connectDB";
    }
    return $result_str;
}

/**修改一条记录
 * @param $name 姓名
 * @param $field 字段
 * @param $value 修改后的值
 * @return string
 */
function modifyMember($name, $field, $value)
{
    $result_str = "modify_success";
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_modify = "";
        if ($field == "name") {
            $sql_modify = "UPDATE branch_member SET member_name='" . $value . "' WHERE member_name='" . $name . "'";
        } else {
            $sql_modify = "UPDATE branch_member_info SET " . $field . "='" . $value
                . "' WHERE member_id=(SELECT member_id FROM branch_member WHERE member_name='" . $name . "')";
        }
        try {
            mysqli_query($conn, $sql_modify);
        } catch(Exception $e) {
            $result_str = $e->getMessage();
        } finally {
            $dp->closeConn();
        }
    } else {
        $result_str = "modify_fail_connectDB";
    }
    return $result_str;
}

/**删除一条记录
 * @param $name 记录所有者姓名
 * @return string
 */
function deleteMember($name)
{
    $result_str = "delete_success";
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql_delete_foreign = "DELETE FROM branch_member_info WHERE member_id = (SELECT member_id FROM branch_member WHERE member_name='" . $name . "')";
        $sql_delete_primary = "DELETE FROM branch_member WHERE member_name='".$name."'";
        try {
            mysqli_query($conn, $sql_delete_foreign);
            mysqli_query($conn, $sql_delete_primary);
        } catch(Exception $e) {
            $result_str = $e->getMessage();
        } finally {
            $dp->closeConn();
        }
    } else {
        $result_str = "delete_fail_connectDB";
    }
    return $result_str;
}
