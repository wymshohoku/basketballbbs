<?php

require_once '../../model/common/util.php';
require_once '../../model/common/captcha.php';
require_once '../../model/common/token.php';
require_once '../../model/mysql/mysql.php';

use model\mysql\Pdo;
use model\util as Util;

$error = "登录";
$admin = false;
$token = getToken();
$username = "";
$password = "";
//  启动会话，这步必不可少
session_start();

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
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
    <title>篮球世界</title>
</head>

<body>
    <div class="container">
        <div>
            <div>
                <!-- Start Sign In Form -->
                <form action="#" method="POST" id="login">
                    <h2><?php echo $error; ?></h2>
                    <div>
                        <input type="hidden" id="userid" name="userid" value="" />
                        <input type="hidden" id="token" name="token" value="<?php $_SESSION["token"] = $token;
                                                                            echo $token ?>" />
                        <label for="username">用户名</label>
                        <input type="text" name="username" placeholder="用户名" autocomplete="off" value="<?php echo $username; ?>" required>
                    </div>
                    <div>
                        <label for="password">密码</label>
                        <input type="password" name="password" id="password" placeholder="密码" autocomplete="off" value="<?php echo $password; ?>" required>
                    </div>
                    <div>
                        <label for="code">验证码图片：</label>
                        <img id="captcha_img" border="1" src="admin/r/<?php echo rand(); ?>" alt="" width="100" height="30" />
                        <a href="javascript:void(0)" onclick="document.getElementById('captcha_img').src='admin/r/'+Math.floor((Math.random()*10000)+1) ">换一个?
                        </a>
                    </div>
                    <div>
                        <label for="inputcode">请输入图片中的内容：</label>
                        <input type="text" id="authcode" name="authcode" value="" required />
                    </div>
                    <div>
                        <input type="submit" value="提交">
                    </div>
                </form>
                <!-- END Sign In Form -->

            </div>
        </div>
    </div>
</body>

</html>