<?php
/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/22
 * Time: 15:16
 */
require_once("../DataAccess/DataProcessor.php");

$kind = $_GET['kind'];

switch ($kind) {
    case 'getFormal':
        echo getFormal("'".$_GET['name']."'");
        break;
}

function getFormal($name) {
    $rst=Array();
    $i = 0;
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $sql_getCount = "SELECT m.member_name,i.develop_date FROM branch_member_info i, branch_member m
            WHERE i.member_id=m.member_id AND (i.formal_date IS NULL OR i.formal_date = '')
            AND m.branch_id = (SELECT branch_id FROM branch_member WHERE member_name=".$name.")";
        try {
            $conn = $dp->getConn();
            $result = $conn->query($sql_getCount);
            while ($row = $result->fetch_assoc()) {
                $member_name = $row["member_name"];
                $develop_date = $row["develop_date"];
                $formal_date = strval((int)substr($develop_date,0,4)+1).substr($develop_date,4);
                $days_remain = intval((strtotime($formal_date)-time())/86400);
                $rst[$i] = new Formal($member_name,$formal_date,$days_remain);
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

class Formal
{
    public $member_name;
    public $formal_date;
    public $days_remain;

    function __construct($param1,$param2,$param3)
    {
        $this->member_name = $param1;
        $this->formal_date = $param2;
        $this->days_remain = $param3;
    }
}
