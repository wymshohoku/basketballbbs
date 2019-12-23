<?php

require_once '../../model/common/util.php';
require_once '../../model/common/captcha.php';
require_once '../../model/common/token.php';
require_once '../../model/mysql/mysql.php';

use model\mysql\Pdo;
use model\util as Util;

$error = "";
$token = "";
$username = "";
$password = "";
//  启动会话，这步必不可少
session_start();

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];

switch ($view) {
    case "r":
        if (Util\isAuthCode()) {
            // 生成验证码图片
            $image = Util\getAuthCodeImg();
            header('content-type:image/png');
            exit($image);
        }
        break;
    case "token":
        break;
    case "admin":
        if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
            //  判断是否登陆
            header('location:/admin/');
            exit();
            // header('Content-Type:application/json; charset=utf-8');
            // $loc['location'] = '/view/admin/admin.html';
            // $json = json_encode($loc);
            // exit($json);
        }
        break;
    case "";
        $error = "登录";
        $_SESSION["admin"] = false;
        $token = getToken();
}

/**
 * 过滤信息
 *
 * @param  mixed $data 需要过滤的字符串
 *
 * @return string
 */
function test_input($data)
{
    return Util\DataVerify::test_input($data);
}

/**
 * 生成token
 *
 * @return string
 */
function getToken()
{
    $t = new Util\Token();
    return $t->api_token("login", date("Y-m-d H:i:s", time()));
}


if (Util\isAuthCode()) {
    // 生成验证码图片
    $image = Util\getAuthCodeImg();
    header('content-type:image/png');
    exit($image);
} else if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    //  判断是否登陆
    header('location:/admin/');
    exit();
    // header('Content-Type:application/json; charset=utf-8');
    // $loc['location'] = '/view/admin/admin.html';
    // $json = json_encode($loc);
    // exit($json);
} else {
    //  验证失败，将 $_SESSION["admin"] 置为 false
    $_SESSION["admin"] = false;

    // 验证用户
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && !empty($_POST)
        && isset($_POST['username'])
        && isset($_POST['authcode'])
    ) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($_POST['authcode'] !== Util\getAuthCode()) {
            $error = "验证码错误！";
            //header('location:../../controller/admin/login.php');
            //exit();
        } else if (isset($_POST['password']) && $_POST['token'] === $_SESSION["token"]) {
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
                        header('location:/admin/');
                        exit();
                    }
                }
            }
            $error = "用户名或密码错误！";
        } else {
            $error = "用户名或密码错误！";
        }
    } else if (isset($_POST['username']) && $_POST['username'] === "") {
        $error = "用户名不能为空！";
    } else if (isset($_POST['password']) && $_POST['password'] === "") {
        $error = "密码不能为空！";
    } else if (isset($_POST['authcode']) && $_POST['authcode'] === "") {
        $error = "验证码不能为空！";
    }

    //header('location:../../view/admin/login.html');
}
?>