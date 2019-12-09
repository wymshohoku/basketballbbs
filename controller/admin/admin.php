<?php

require_once '../../model/admin/admin.php';
use model\admin\Admin;

session_start();

//  判断是否登陆
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {

    $json = "";
    if (isset($_POST["func_name"])) {
        $admin = new Admin($_POST["func_name"]);
        $admin->getTable();

        $json = json_encode($admin->serialize());
    }

    header('Content-Type:application/json; charset=utf-8');
    exit($json);
} else {
    //header('location:../../view/admin/login.html');
    exit("");
}
