<?php

require_once '../../model/common/util.php';
require_once '../../model/mysql/mysql.php';

use model\mysql\Pdo;
use model\util as Util;

$admin = false;
//  启动会话，这步必不可少
session_start();

function test_input($data)
{
    return Util\DataVerify::test_input($data);
}
//  判断是否登陆
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    //header('location:../../view/admin/admin.html');
    header('Content-Type:application/json; charset=utf-8');
    $loc['location'] = '/view/admin/admin.html';
    $json = json_encode($loc);
    exit($json);
} else {
    //  验证失败，将 $_SESSION["admin"] 置为 false
    $_SESSION["admin"] = false;

    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && !empty($_POST)
        && isset($_POST['username'])
    ) {

        if (isset($_POST['password'])) {
            $isLogin = false;

            $username = test_input($_POST['username']);

            $pdo = new Pdo();
            // $pwd = password_hash($_POST['password'], PASSWORD_DEFAULT);
            // $sql = "UPDATE admin SET passwd='" . $pwd . "'  WHERE name='$username'";
            // $stmt = $pdo->querySQL($sql);

            $sql = "SELECT passwd FROM admin WHERE name='$username'";
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                while ($row = $stmt->fetch()) {
                    $hashedPassword = $row[0];
                    $isLogin = password_verify(test_input($_POST['password']), $hashedPassword);

                    if ($isLogin) {
                        $_SESSION["admin"] = true;
                        header('location:../../view/admin/admin.html');
                        exit();
                    }
                }
            }
        }
    }

    header('location:../../view/admin/login.html');
}
