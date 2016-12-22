<?php
/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/21
 * Time: 20:36
 */
require_once("../DataAccess/DataProcessor.php");

$kind = $_GET['kind'];

switch ($kind) {
    case 'monthlyDues':
        $name = "'".$_GET['name']."'";
        echo getMemberCount($name);
        break;
    case 'duesCheck':
        $name = "'".$_GET['name']."'";
        $start = $_GET['start'];
        $finish = $_GET['finish'];
        echo checkDues($name,$start,$finish);
        break;
    default:
}

/**获取支部人数
 * @param $name 书记姓名
 * @return string
 */
function getMemberCount($name) {
    $count = 'error';
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $sql_getCount = "SELECT COUNT(i.member_id) FROM branch_member_info i, branch_member m
            WHERE i.member_id=m.member_id AND (i.develop_date IS NOT NULL AND i.develop_date != '')
            AND m.branch_id = (SELECT branch_id FROM branch_member WHERE member_name=".$name.")";
        try {
            $conn = $dp->getConn();
            $result = $conn->query($sql_getCount);
            if ($row = $result->fetch_assoc()) {
                $count = $row["COUNT(i.member_id)"];
            }
        } catch(Exception $e) {
            $count = $e->getMessage();
        } finally {
            $dp->closeConn();
        }
    }
    return $count;
}

/**党费核查
 * @param $name
 * @param $startDate
 * @param $finishDate
 * @return string
 */
function checkDues($name,$startDate,$finishDate) {
    $rst=Array();
    $i = 0;
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $sql_getCount = "SELECT m.member_name,i.develop_date FROM branch_member_info i, branch_member m
            WHERE i.member_id=m.member_id AND (i.develop_date IS NOT NULL AND i.develop_date != '')
            AND m.branch_id = (SELECT branch_id FROM branch_member WHERE member_name=".$name.")";
        try {
            $conn = $dp->getConn();
            $result = $conn->query($sql_getCount);
            while ($row = $result->fetch_assoc()) {
                $member_name = $row["member_name"];
                $develop_date = $row["develop_date"];
                $due = sprintf("%.1f", generateDue($startDate,$finishDate,$develop_date));
                $rst[$i] = new Dues($member_name,$develop_date,$due);
                $i++;
            }
            $dp->closeConn();
            $objJSon = json_encode($rst);
            return $objJSon;
        } catch(Exception $e) {
            $dp->closeConn();
            return $e->getMessage();
        }
    }
    return "error";
}
function generateDue($startDate,$finishDate,$develop_date) {
    $due = 0; $monthlyDue = 0.2;
    $startDate_unix = strtotime($startDate);
    $finishDate_unix = strtotime($finishDate);
    $develop_date_unix = strtotime($develop_date);
    if ($develop_date_unix >= $finishDate_unix){                           // 发展时戳在上限之后或等于上限
        // finishDate - startDate
        if (date("Y",$develop_date_unix)==date("Y",$finishDate_unix)
            && date("m",$develop_date_unix)==date("m",$finishDate_unix)) { // 同年同月，交一个月
            $due = 1.0 * $monthlyDue;
        } else {                                                           // 不同月，不交
            $due = 0.0;
        }
    } elseif ($develop_date_unix <= $startDate_unix) {                     // 发展时戳在下限之前或等于下限，交上限-下限+1
        $due = ((date("Y",$finishDate_unix)-date("Y",$startDate_unix))*12
                +(date("m",$finishDate_unix)-date("m",$startDate_unix)+1))*$monthlyDue;
    } else {                                                               // 发展时戳在下限之后上限之前
        $due = ((date("Y",$finishDate_unix)-date("Y",$develop_date_unix))*12
                +(date("m",$finishDate_unix)-date("m",$develop_date_unix)+1))*$monthlyDue;
    }
    return $due;
}

class Dues {
    public $member_name;
    public $develop_date;
    public $due;

    function __construct($param1,$param2,$param3)
    {
        $this->member_name = $param1;
        $this->develop_date = $param2;
        $this->due = $param3;
    }
}