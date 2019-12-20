<?php

require_once '../../model/admin/admin.php';
use model\admin\Admin;

session_start();

//  判断是否登陆
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    $json = "";
    if(isset($_POST["LoginOut"])){
        // 更新用户的token
        $_SESSION["admin"] = false;
        header('Content-Type:application/json; charset=utf-8');
        $json = json_encode(array('location'=>'../../controller/admin/login.php'));
        exit($json);
    }
    if (isset($_POST["src"]) && isset($_POST["func"])) {
        $admin = new Admin($_POST["src"]);
        if ($_POST["func"] === "select_all") {
            $admin->getTable();
        } elseif ($_POST["func"] === "delete") {
            if (isset($_POST["index"])) {
                $admin->deleteRecord($_POST["index"], $_POST["id"]);
            }
        } elseif ($_POST["func"] === "approval") {
            if (isset($_POST["index"])) {
                $admin->updateCommentApproval($_POST["index"], $_POST["id"]);
            }
        }

        $json = json_encode($admin->serialize());
    }

    header('Content-Type:application/json; charset=utf-8');
    exit($json);
} else {
    //header('location:../../view/admin/login.html');
    exit("");
}
