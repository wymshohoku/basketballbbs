<?php

namespace model\index {

    require_once '../../model/common/util.php';
    require_once '../../model/mysql/mysql.php';

    use model\mysql\Pdo;
    use model\util as Util;

    \define('COMMENT_PAGE_COUNT', 5);
    function test_input($data)
    {
        return Util\DataVerify::test_input($data);
    }

    class bbs
    {
        private $art;

        public function serialize()
        {
            return $this->art->serialize();
        }
        public function __construct()
        {
            $this->art = new article();
        }
        public static function setArticleId($id)
        {
            $_SESSION['art_id'] = test_input($id);
        }

        public static function isStoreArticleId()
        {
            if (isset($_SESSION['art_id'])) {
                return true;
            }
            return false;
        }

        public function getArticle($comment_current_page_index)
        {
            $index = test_input($comment_current_page_index);
            if ($index === "prev") {
                $_SESSION['comment_current_page_index'] -= 1;
            } else if ($index === "next") {
                $_SESSION['comment_current_page_index'] += 1;
            } else {
                $_SESSION['comment_current_page_index'] = (int)$index;
            }
            // 从数据库读取当前文章的评论
            if ($this->art->getRecordById($_SESSION['art_id'], $_SESSION['comment_current_page_index']) === false) {
                header('location:../../view/error/404.html');
                exit();
            }
        }

        public static function getArticleId()
        {
            if (isset($_SESSION['art_id'])) {
                return $_SESSION['art_id'];
            }

            return false;
        }

        public static function isUserClickPage()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET) && isset($_GET['page_index'])) {
                return true;
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

        public function submitComment()
        {
            $art_id = test_input($_POST['art_id']);
            // 提交的评论的文章id是否是当前访问的文章id
            if ($art_id === $_SESSION['art_id']) {
                $name = test_input($_POST['username']);
                $msg = test_input($_POST['msg']);
                $datetime = test_input($_POST['time']);
                $this->art->insertComment($art_id, $name, $msg, $datetime);
            }
        }
    }

    class article
    {
        private $art_id;
        private $art_title;
        private $art_text;
        private $comment_page_count = COMMENT_PAGE_COUNT; // 每一页评论的数量
        private $comment_page_index = 0;
        private $comment_pages = 0; // 总共的评论页数
        private $comment_count = 0; // 总共的评论
        private $comment_array = array();

        public function serialize()
        {
            $article_json['art_id'] = $this->art_id;
            $article_json['art_title'] = $this->art_title;
            $article_json['art_text'] = $this->art_text;
            $article_json['comment_page_count'] = $this->comment_page_count;
            $article_json['comment_page_index'] = $this->comment_page_index;
            $article_json['comment_pages'] = $this->comment_pages;
            $article_json['comment_count'] = $this->comment_count;
            $article_json['comments'] = $this->comment_array;
            return $article_json;
        }
        public function getRecordById($artid, $comment_current_page_index)
        {
            $pdo = new Pdo();
            $sql = "SELECT title, text FROM article WHERE id='" . $artid . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }

            $row = $stmt->fetch();
            $this->art_id = $_SESSION['art_id'];
            $this->art_title = $row['title'];
            $this->art_text = $row['text'];
            $this->comment_pages = 0;
            $this->comment_count = 0;
            $this->comment_page_index = $comment_current_page_index;

            $this->comment_count = comment::getAllRecordsByArticleId($this->art_id);
            $offset = ($this->comment_page_index - 1) * COMMENT_PAGE_COUNT;
            $this->comment_page_count = comment::getRecordForCurrentPageByArticleId($this->art_id, COMMENT_PAGE_COUNT, $offset);// 获取当前页评论的数量
            $_SESSION['comment_count'] = $this->comment_count;
            $this->comment_array = comment::getComments();

            $this->comment_pages = (int)($this->comment_count / COMMENT_PAGE_COUNT + (($this->comment_count % COMMENT_PAGE_COUNT) > 0 ? 1 : 0));
            return true;
        }
        public function insertRecord()
        {}

        public function insertComment($artid, $name, $msg, $datetime)
        {
            $comment = new comment($artid, $name, $msg, $datetime);
            $result = $comment->insertRecord();

            if ($result != false) { // 插入数据成功
                $this->art_id = $_SESSION['art_id'];
                $this->comment_page_count = COMMENT_PAGE_COUNT;
                $this->comment_pages = 0;
                $_SESSION['comment_count'] += 1;
                $this->comment_count = $_SESSION['comment_count'];
                $this->comment_array[] = $comment->serialize();

                $this->comment_pages = $this->comment_count % $this->comment_page_count;
            } else { // 插入数据失败

            }
        }
    }

    class user
    {
        private $id;
        private $userimg = '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp';
        private $username;

        public function __construct()
        {
            $a = func_get_args();
            $i = count($a);
            if (method_exists($this, $f = '__construct' . $i)) {
                call_user_func_array(array($this, $f), $a);
            }
        }
        public function serialize()
        {
            return array('name' => $this->username, 'img' => $this->userimg);
        }
        public function __construct1($id)
        {
            $this->id = $id;
        }
        public function __construct2($name, $img)
        {
            $this->username = $name;
            // 图片使用默认图片
            //$this->userimg = $img;
        }
        public function isUserExist($name)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT id FROM users WHERE name='" . $name . "'";
            $row = $pdo->querySQL($sql)->fetch();
            if ($row === false) {
                return false;
            }
            return $row;
        }
        public function getUserNameAndImage()
        {
            $pdo = new Pdo();
            $sql = "SELECT img, name FROM users WHERE id='" . $this->id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }

            $row = $stmt->fetch();
            $this->username = $row['name'];
            $this->userimg = $row['img'];
        }
        public function selectUserByNameOrInsertUser()
        {
            // 查询用户
            $row = $this->isUserExist($this->username);

            // 用户不存在
            if ($row === false) {
                $pdo = new Pdo();
                $sql = "INSERT INTO users (name, img) VALUES('" . $this->username . "','" . $this->userimg . "')";
                $stmt = $pdo->prepareSQL($sql);
                if ($stmt != false) {
                    //$pdo->closeConnect();
                    $this->id = $this->isUserExist($this->username)['id'];
                } else {
                    return false;
                }
            } else {
                $this->id = $row['id'];
            }

            return $this->id;
        }
    }
    class comment
    {
        private $id;
        private $articleid;
        private $user;
        private $datetime;
        private $msg;
        private $approval;
        private static $comments = array();

        public function __construct()
        {
            $a = func_get_args();
            $i = count($a);
            if (method_exists($this, $f = '__construct' . $i)) {
                call_user_func_array(array($this, $f), $a);
            }
        }
        public function __construct4($artid, $name, $msg, $datetime)
        {
            $this->articleid = $artid;
            $this->user = new user($name, 'img');
            $this->datetime = $datetime;
            $this->msg = $msg;
            $this->approval = 2; // 0，已删除 1，未审核 2，已审核
        }

        public function __construct5($id, $userid, $datetime, $msg, $approval)
        {
            $this->user = new user($userid);
            $this->user->getUserNameAndImage();
            $this->id = $id;
            $this->datetime = $datetime;
            $this->msg = $msg;
            $this->approval = $approval;
        }
        public function serialize()
        {
            return array('commentid' => $this->id,
                'userimg' => $this->user->serialize()['img'],
                'username' => $this->user->serialize()['name'],
                'time' => $this->datetime,
                'msg' => $this->msg,
            );
        }
        public static function getComments()
        {
            return self::$comments;
        }
        public static function getAllRecordsByArticleId($articleid)
        {
            //$_SESSION['comment_current_page_index'] * $this->comment_page_count
            $pdo = new Pdo();
            // SELECT distinct users.img, users.name, comments.date, comments.comment FROM article, users, comments WHERE comments.articleid='1' and comments.userid=users.id
            $sql = "SELECT COUNT(*) FROM comments " .
                "WHERE comments.articleid='" . $articleid . "'" ;
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                $row = $stmt->fetch();
                return (int)$row[0];
            }
            return 0;
        }
        public static function getRecordForCurrentPageByArticleId($articleid, $limit, $offset)
        {
            //$_SESSION['comment_current_page_index'] * $this->comment_page_count
            $pdo = new Pdo();
            // SELECT distinct users.img, users.name, comments.date, comments.comment FROM article, users, comments WHERE comments.articleid='1' and comments.userid=users.id
            $sql = "SELECT id, userid, date, comment, approval FROM comments " .
                "WHERE comments.articleid='" . $articleid . "' " .
                "ORDER BY id LIMIT " . $limit . " " .
                "OFFSET " . $offset;
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                $count = 0;
                while ($row = $stmt->fetch()) {
                    if ($row['approval'] === '2') {
                        $count += 1;
                        $comment = new comment($row['id'], $row['userid'], $row['date'], $row['comment'], $row['approval']);
                        self::$comments[] = $comment->serialize();
                    }
                }
                return $count;
            }
            return 0;
        }
        public function insertRecord()
        {
            $userid = $this->user->selectUserByNameOrInsertUser();

            if ($userid != false) {
                // 插入新的评论到数据库
                $pdo = new Pdo();
                $sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES('" . $this->articleid . "','" . $userid . "','" . $this->msg . "','" . $this->datetime . "','2')";
                //$sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES('1','2','11111','2019-12-02 18:09:09','2')";
                try {
                    $stmt = $pdo->prepareSQL($sql);
                    return $stmt;

                } catch (PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                    return false;
                }
            }
            return false;
        }
    }
}
