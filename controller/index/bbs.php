<?php

require_once '../../model/index/bbs.php';

use model\index\bbs as CBbs;

session_start();

if (CBbs::isUserClickArticle()) {
    CBbs::setArticleId($_GET['art_id']);
    header('location:../../view/index/bbs.html');
} else if (CBbs::issetArticleId()) {
    $bbs = new CBbs();
    $article_json = array();
    if (CBbs::isUserSubmitComment()) {
        // 提交评论
        $art_id = test_input($_POST['art_id']);
        if ($art_id === $_SESSION['art_id']) {
            $userid = 0;
            $name = test_input($_POST['username']);
            $img = '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp';
            $datetime = test_input($_POST['time']);
            $comment = test_input($_POST['msg']);

            $pdo = new CPDO();
            // 查询用户
            $sql = "SELECT id FROM users WHERE name='" . $name . "'";
            $row = $pdo->querySQL($sql)->fetch();

            // 用户不存在
            if ($row === false) {
                $sql = "INSERT INTO users (name, img) VALUES('" . $name . "','" . $img . "')";
                $stmt = $pdo->querySQL($sql);
                $row = $stmt->fetch();
                $userid = $row['id'];
            } else {
                $userid = $row['id'];
            }

            // 插入新的评论到数据库
            unset($row);
            $sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES('" . $art_id . "','" . $userid . "','" . $comment . "','" . $datetime . "','2')";
            //$sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES('1','2','11111','2019-12-02 18:09:09','2')";
            try {
                $row = $pdo->querySQL($sql)->fetch();
            } catch (PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
            if ($row != false) { // 插入数据成功
                $article_json['art_id'] = $_SESSION['art_id'];
                $article_json['comment_page_count'] = 20;
                $article_json['comment_pages'] = 0;
                $article_json['comment_count'] = $_SESSION['comment_count'] + 1;
                $article_json['comments'][] = array(
                    'commentid' => $row['id'],
                    'userimg' => '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp',
                    'username' => test_input($_POST['username']),
                    'time' => test_input($_POST['time']),
                    'msg' => test_input($_POST['msg']),
                );
            } else { // 插入数据失败

            }
        }
    } else {
        $bbs->getArticle();
    }

    header('Content-Type:application/json; charset=utf-8');
    $json = json_encode($bbs->serialize());
    exit($json);
}
