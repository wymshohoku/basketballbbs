<?php

namespace model\comment {
    require_once '../../model/mysql/mysql.php';
    require_once '../../model/user/user.php';

    use model\mysql\Pdo;
    use model\user\user;

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
                "WHERE comments.articleid='" . $articleid . "'";
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                $row = $stmt->fetch();
                return (int) $row[0];
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
                "ORDER BY date DESC " .
                "LIMIT " . $limit . " " .
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
