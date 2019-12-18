<?php

session_start();

require_once '../../model/index/bbs.php';

use model\index\bbs as CBbs;


if (CBbs::isUserClickArticle()) { // 用户点击文章链接
    if (CBbs::checkToken($_GET['art_id'], $_GET['token'])) {
        CBbs::setArticleId($_GET['art_id']);
        header('location:../../view/index/bbs.html');
    } else {
        header('location:../../index.html');
    }
} else if (CBbs::isAuthCode()) {
    $image = CBbs::getAuthCodeImg();

    header('content-type:image/png');
    exit($image);
} else if (CBbs::isStoreArticleId()) {
    $bbs = new CBbs();
    if (CBbs::isUserClickPage()) { // 用户点击翻页
        $bbs->getArticle($_GET['page_index']);
    } else if (CBbs::isUserSubmitComment()) {
        // 验证提交评论的验证码
        if ($bbs->checkAuthCode($_POST['authcode'])) {
            // 提交评论
            $bbs->submitComment(
                $_POST['art_id'],
                $_POST['username'],
                $_POST['msg'],
                $_POST['time'],
                $_POST['id'],
                $_POST['pwd'],
                $_POST['token']
            );
        }
    } else {
        // 用户点击了文章链接跳转，获取该文章和所有评论
        $bbs->getArticle(1);
    }

    header('Content-Type:application/json; charset=utf-8');
    $json = json_encode($bbs->serialize());
    exit($json);
}
