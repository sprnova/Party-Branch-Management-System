<?php
/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/16
 * Time: 8:50
 */

require_once("../DataAccess/DataProcessor.php");

$kind = $_POST['kind'];
if ($kind == 1) {
    $login_name = $_POST["login_name"];
    $login_pswd = $_POST["login_psw"];

    authenticate($login_name, $login_pswd);
} elseif ($kind == 2) {
    $rsg_name = $_POST["rsg_name"];
    $rsg_vald = $_POST["rsg_valid"];
    $rsg_pswd = $_POST["rsg_psw"];

    register($rsg_name,$rsg_vald,$rsg_pswd);
} else {
    echo "Error in judging form type!";
}

function authenticate($userName, $password) {
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();
        $sql = "SELECT user_name, user_password, user_type FROM users WHERE user_name='".$userName."'AND user_password='".$password."'";
        $result = $conn->query($sql);

        if($result->num_rows == 1) {
            if($row = $result->fetch_assoc()) {
                echo "login_success".$row["user_type"];
            }
            $dp->closeConn();
            return true;
        } else {
            $dp->closeConn();
            echo "login_fail_wrong";
            return false;
        }
    }
    echo "login_fail_connectionFailure";
    return false;
}

function register($userName, $validation, $password) {
    $dp = new DataProcessor();
    if ($dp->connectMySQL()) {
        $conn = $dp->getConn();

        $sql_check_one = "SELECT user_name FROM users WHERE user_name='".$userName."'";
        $sql_check_two = "SELECT stage_name FROM register_staging WHERE stage_name='".$userName."'";
        $sql_insert = "INSERT INTO register_staging (stage_id, stage_name, stage_validation, stage_password) VALUES (0,"."'".$userName."'".","."'".$validation."'".","."'".$password."')";

        // Check whether there is any duplicate
        if ($conn->query($sql_check_one)->num_rows > 0) {
            $dp->closeConn();
            echo "register_fail_userAlready";
            return false;
        } elseif ($conn->query($sql_check_two)->num_rows > 0) {
            $dp->closeConn();
            echo "register_fail_stagingAlready";
            return false;
        } elseif ($conn->query($sql_insert) == true) {
            $dp->closeConn();
            echo "register_success";
            return true;
        } else {
            $dp->closeConn();
            echo "register_fail3";
            return false;
        }
    }
    echo "register_fail_connectionFailure";
    return false;
}