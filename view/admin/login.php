<?php

require_once '../../controller/admin/admin.php';

use controller\admin\Admin;

//  启动会话，这步必不可少
session_start();

$admin = new Admin("login");

$error = "";
$token = "";
$username = "";
$password = "";

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
if (isset($_POST["view"]))
    $view = $_POST["view"];

switch ($view) {
    case "token":
        break;
    case "submit":
        if (isset($post['username']) && $post['username'] === "") {
            $error = "用户名不能为空！";
        } else if (isset($post['password']) && $post['password'] === "") {
            $error = "密码不能为空！";
        } else if (isset($post['authcode']) && $post['authcode'] === "") {
            $error = "验证码不能为空！";
        }
        // 验证用户
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $admin->checkAdmin($_POST);
            if ($error === "") {
                exit();
            }
        }

        $username = $_POST["username"];
        $password = $_POST["password"];
        break;
    case "login";
        if (isset($_GET["r"])) {
            $admin->getAuthCode("");
            exit();
        } else {
            $error = "登录";
            $_SESSION["admin"] = false;
            $token = $admin->getToken();
        }
        break;
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
                <form action="login" method="POST" id="login">
                    <h2><?php echo $error; ?></h2>
                    <div>
                        <input type="hidden" id="view" name="view" value="submit" />
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
                        <img id="captcha_img" border="1" src="r/<?php echo rand(); ?>" alt="" width="100" height="30" />
                        <a href="javascript:void(0)" onclick="document.getElementById('captcha_img').src='r/'+Math.floor((Math.random()*10000)+1) ">换一个?
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