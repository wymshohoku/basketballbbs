<?php

namespace model\index {

    require_once '../common/util.php';
    require_once '../mysql/mysql.php';

    use model\mysql\Pdo;
    use model\util as Util;

    function test_input($data)
    {
        return Util\DataVerify::test_input($data);
    }

    class bbs
    {
        private $art;

        public function __construct()
        {
            $this->art = new article();
        }
        public static function setArticleId($id)
        {
            $_SESSION['art_id'] = test_input($id);
        }

        public static function issetArticleId()
        {
            if (isset($_SESSION['art_id'])) {
                return true;
            }
            return false;
        }

        public static function getArticleId()
        {
            if (isset($_SESSION['art_id'])) {
                return $_SESSION['art_id'];
            }

            return false;
        }

        public static function isUserClickArticle()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET) && isset($_GET['art_id'])) {
                return true;
            }
            return false;
        }
        public static function isUserSubmitComment()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST'
                && !empty($_POST) && isset($_POST['art_id'])) {
                return true;
            }
            return false;
        }

        public function getArticle()
        { 
            // 从数据库读取当前文章的评论
            $pdo = new Pdo();
            $sql = "SELECT title, text FROM article WHERE id='" . $_SESSION['art_id'] . "'";
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                $row = $stmt->fetch();
                $article_json['art_id'] = $_SESSION['art_id'];
                $article_json['art_title'] = $row['title'];
                $article_json['art_text'] = $row['text'];
                $article_json['comment_page_count'] = 20;
                $article_json['comment_pages'] = 0;
                $article_json['comment_count'] = 0;

                // SELECT distinct users.img, users.name, comments.date, comments.comment FROM article, users, comments WHERE comments.articleid='1' and comments.userid=users.id
                $sql = "SELECT distinct users.img, users.name, comments.id, comments.date, comments.comment, comments.approval FROM article, users, comments WHERE comments.articleid='" . $_SESSION['art_id'] . "' and comments.userid=users.id";
                $stmt = $pdo->querySQL($sql);

                if ($stmt != false) {
                    while ($row = $stmt->fetch()) {
                        if ($row['approval'] === '2') {
                            $article_json['comment_count'] += 1;
                            $article_json['comments'][] = array(
                                'commentid' => $row['id'],
                                'userimg' => $row['img'],
                                'username' => $row['name'],
                                'time' => $row['date'],
                                'msg' => $row['comment'],
                            );
                        }
                        if ($article_json['comment_count'] > $article_json['comment_page_count']) {
                            $article_json['comment_pages'] = 4;
                        }
                    }
                    $_SESSION['comment_count'] = $article_json['comment_count'];
                }
            } else {
                header('location:../../view/error/404.html');
                exit();
            }
        }
    }

    class user
    {
        private $id;
        private $userimg = '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp';
        private $username;
    }
    class article
    {
        private $art_id;
        private $art_title;
        private $art_text;
        private $comment_page_count = 20;
        private $comment_pages = 0;
        private $comment_count = 0;
        private $comment = array();

        public function serialize()
        {
            $article_json['art_id'] = $this->art_id;
            $article_json['art_title'] = $this->art_title;
            $article_json['art_text'] = $this->art_text;
            $article_json['comment_page_count'] = $this->comment_page_count;
            $article_json['comment_pages'] = $this->comment_pages;
            $article_json['comment_count'] = $this->comment_count;
            $article_json['comments'][] = new commnet();
            return $article_json;
        }
        public function selectRecord()
        {}
        public function insertRecord()
        {}
    }

    class comment
    {
        private $id;
        private $articleid;
        private $userid;
        private $datetime;
        private $msg;
        private $approval;

        public function selectRecord()
        {}
        public function insertRecord()
        {}
        public function serialize()
        {
            return array('commentid' => $this->commentid,
                'userimg' => $this->userimg,
                'username' => $this->username,
                'time' => $this->datetime,
                'msg' => $this->msg,
                'approval' => $this->approval,
            );
        }
    }
}
