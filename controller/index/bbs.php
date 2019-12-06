<?php

require_once '../../model/index/bbs.php';

use model\index\bbs as CBbs;

session_start();

if (CBbs::isUserClickArticle()) {// 用户点击文章链接
    CBbs::setArticleId($_GET['art_id']);
    header('location:../../view/index/bbs.html');
} else if (CBbs::isStoreArticleId()) {
    $bbs = new CBbs();
    if(CBbs::isUserClickPage()){// 用户点击翻页
        $bbs->getArticle($_GET['page_index']);
    } else if (CBbs::isUserSubmitComment()) {
        // 提交评论
        $bbs->submitComment();
    } else {
        // 用户点击了文章链接跳转，获取该文章和所有评论
        $bbs->getArticle(1);
    }

    header('Content-Type:application/json; charset=utf-8');
    $json = json_encode($bbs->serialize());
    exit($json);
}
