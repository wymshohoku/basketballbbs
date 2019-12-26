<?php
require_once '../../controller/admin/admin.php';

use controller\admin\Admin;

session_start();


$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
if (isset($_POST["view"]))
    $view = $_POST["view"];

$admin = new Admin($view);
switch ($view) {
    case "logout":
        $admin->logoutAdmin();
        header('Content-Type:application/json; charset=utf-8');
        $json = json_encode(array('location' => 'login'));
        exit($json);
        break;
    case "article":
    case "comment":
    case "user":
        if ($admin->isLogin() === false) {
            header('location:login');
            exit();
        }
        if ($_POST['func'] === "select_all") {
            $admin->getTable();
        } elseif ($_POST['func'] === "delete") {
            $admin->deleteRecord($_POST['index'], $_POST['id']);
        } elseif ($_POST['func'] === "approval") {
            $admin->updateCommentApproval($_POST['index'], $_POST['id']);
        } else {
            exit();
        }
        header('Content-Type:application/json; charset=utf-8');
        $json = json_encode($admin->serialize());
        exit($json);
        break;
    case "";
        if ($admin->isLogin() === false) {
            header('location:login');
            exit();
        }
        break;
}

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <link href="https://fonts.font.im/css?family=Open+Sans" rel="stylesheet" type="text/css">
    <title>篮球世界</title>
    <script src="/scripts/admin.js"></script>

    <style>
        body {
            margin: 0;
        }

        body {
            margin: 0;
        }

        .pg-header {
            height: 48px;
            background-color: #2459a2;
            color: white;
            line-height: 48px;
        }

        .pg-header .logo {
            width: 200px;
            text-align: center;
        }

        .pg-header .user {
            width: 160px;
            height: 48px;
            position: relative;
            cursor: pointer;
        }

        .username {
            width: 50px;
            height: 48px;
            line-height: 48px;
            color: white;
            position: absolute;
            top: 0;
            right: 62px;

        }

        .pg-header .user:hover {
            background-color: #428bca;
        }

        .pg-header .user:hover .user_info {
            display: block;
        }

        .pg-header .user a img {
            width: 40px;
            height: 40px;
            margin-top: 4px;
            border-radius: 50%;
        }

        .user_info {
            position: absolute;
            top: 48px;
            right: 20px;
            width: 140px;
            background-color: #6e6067;
            z-index: 20;
            display: none;

        }

        .pg-content .nav-menu {
            position: fixed;
            top: 48px;
            left: 0;
            bottom: 0;
            width: 200px;
            background-color: #2b3643;
            display: block;
            color: floralwhite;
        }

        .pg-content .menu {
            /* position: relative;
            top: 10px;
            left: 0;
            bottom: 0; */
            /* width: 200px; */
            display: block;
            cursor: pointer;
            padding: 10px;
        }

        .pg-content .content {
            position: fixed;
            top: 48px;
            right: 0;
            bottom: 0;
            left: 200px;
            background-color: #dddddd;
            overflow: auto;
            z-index: 9;
        }

        .pg-footer {
            position: fixed;
            top: 100px;
            right: 0;
            bottom: 0;
            left: 200px;
            background-color: #2459a2;
            overflow: auto;
        }

        .left {
            float: left;
        }

        .right {
            float: right;
        }

        .block {
            display: block;
        }

        .manager {
            display: none;
        }

        .manager table {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }
    </style>

</head>

<body>
    <header class="pg-header">
        <div class="logo left">
            篮球世界管理后台
        </div>
        <div class="user right">
            <div>
                <a href="#">
                    <img src="/images/beachball.png">
                </a>
            </div>
            <div class="username">
                <span>admin</span>
            </div>
            <div class="user_info">
                <a class="block" onclick="onChangeArticle(4)">我的资料</a>
                <a class="block" onclick="onLoginOut()">注 销</a>
            </div>
        </div>
    </header>

    <main class="pg-content">
        <nav class="nav-menu">
            <div id="user_menu_item" class="menu" onclick="onChangeArticle(1)">用户管理</div>
            <div id="article_menu_item" class="menu" onclick="onChangeArticle(2)">文章管理</div>
            <div id="comment_menu_item" class="menu" onclick="onChangeArticle(3)">评论管理</div>
            <div id="my_menu_item" class="menu" onclick="onChangeArticle(4)">我的资料</div>
        </nav>
        <section class="content">
            <section class="manager" id="user">
                <h1>用户管理</h1>

                <table border="1">
                    <tr>
                        <th>序号</th>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>头像</th>
                        <th>操作</th>
                    </tr>
                </table>
            </section>
            <section class="manager" id="article">
                <h1>文章管理</h1>

                <table border="1">
                    <tr>
                        <th>序号</th>
                        <th>ID</th>
                        <th>标题</th>
                        <th>内容</th>
                        <th>操作</th>
                    </tr>
                </table>
            </section>
            <section class="manager" id="comment">
                <h1>评论管理</h1>
                <table border="1">
                    <tr>
                        <th>序号</th>
                        <th>ID</th>
                        <th>文章标题</th>
                        <th>用户名</th>
                        <th>内容</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    <!-- 
                    <tr>
                        <td class="text-center">1</td>
                        <td>$100</td>
                        <td>January</td>
                        <td>January</td>
                        <td class="text-center">未审核</td>
                        <td class="text-center">
                            <a href="#">删除</a>
                            <a href="#">审核通过</a>
                        </td>
                    </tr> -->
                </table>
            </section>
            <section class="manager" id="my">
                <h1>我的信息</h1>
                <p>
                    用户名：
                </p>
                <p>
                    修改密码
                </p>
            </section>
        </section>
    </main>
    <!-- <footer class="pg-footer" style="height: 30px;">
        Copyright W3School.com.cn
    </footer> -->

</body>

</html>