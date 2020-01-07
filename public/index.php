<?php

use app\admin\controller\Admin;
use app\bbs\view\Index;

require '../vendor/autoload.php';

$path = $_SERVER['PATH_INFO'];
$arr = explode('/', $path);

if ($arr[1] == 'articles') {
} elseif ($arr[1] == 'bbs') {
} elseif ($arr[1] == 'admin') {
    $admin = new Admin($arr[2]);
} else {
    $index = new Index();
}
