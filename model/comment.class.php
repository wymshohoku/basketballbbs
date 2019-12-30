<?php

namespace model {

    use model\Pdo;
    use model\User;

    require_once 'autoload.php';

    class Comment
    {
        /**
         * 评论ID
         *
         * @var integer
         */
        private $id;

        /**
         * 文章ID
         *
         * @var integer
         */
        private $articleid;

        /**
         * 用户
         *
         * @var [type]
         */
        private $user;

        /**
         * 评论时间
         *
         * @var string
         */
        private $datetime;

        /**
         * 评论内容
         *
         * @var string
         */
        private $msg;

        /**
         * 审核：0，已删除；1，未审核；2，已审核
         *
         * @var integer
         */
        private $approval;

        /**
         * 存放所有记录
         *
         * @var array
         */
        private $records;

        /**
         * 是否有错误
         *
         * @var bool
         */
        private $bError;

        /**
         * 是否返回所有记录
         *
         * @var [type]
         */
        private $bAllRecord;

        /**
         * 存放评论内容
         *
         * @var array
         */
        private static $comments = array();

        /**
         * 构造函数重载调用
         *
         * @return void
         */
        public function __construct()
        {
            $this->bError = false;
            $this->bAllRecord = false;
            $a = func_get_args();
            $i = count($a);
            if (method_exists($this, $f = '__construct' . $i)) {
                call_user_func_array(array($this, $f), $a);
            }
        }

        /**
         * 4个参数的构造函数
         *
         * @param  mixed $artid 文章ID
         * @param  mixed $name 用户名
         * @param  mixed $msg 评论内容
         * @param  mixed $datetime 评论时间
         *
         * @return void
         */
        public function __construct4($artid, $name, $msg, $datetime)
        {
            $this->articleid = $artid;
            $this->user = new user($name, 'img');
            $this->datetime = $datetime;
            $this->msg = $msg;
            $this->approval = 1; // 0，已删除 1，未审核 2，已审核
        }

        /**
         * 5个参数的构造函数
         *
         * @param  mixed $id 评论ID
         * @param  mixed $userid 用户ID
         * @param  mixed $datetime 评论时间
         * @param  mixed $msg 评论内容
         * @param  mixed $approval 审核状态
         *
         * @return void
         */
        public function __construct5($id, $userid, $datetime, $msg, $approval)
        {
            $this->user = new user($userid);
            $this->user->getUserNameAndImage();
            $this->id = $id;
            $this->datetime = $datetime;
            $this->msg = $msg;
            $this->approval = $approval;
        }

        /**
         * 6个参数的构造函数
         *
         * @param  mixed $artid 文章ID
         * @param  mixed $name 用户名
         * @param  mixed $msg 评论内容
         * @param  mixed $datetime 评论时间
         * @param  mixed $userid 用户ID
         * @param  mixed $approval 审核状态
         *
         * @return void
         */
        public function __construct6($artid, $name, $msg, $datetime, $userid, $approval)
        {
            $this->user = new user($userid, $name, 'img');
            $this->articleid = $artid;
            $this->datetime = $datetime;
            $this->msg = $msg;
            $this->approval = $approval;
        }

        /**
         * 序列化返回内容
         *
         * @return array
         */
        public function serialize()
        {
            if ($this->bAllRecord || $this->bError) {
                return $this->records;
            }
            return array(
                'commentid' => $this->id,
                //'userid' => $this->user->serialize()['id'],
                'username' => $this->user->serialize()['name'],
                'userimg' => $this->user->serialize()['img'],
                //'token' => $this->user->serialize()['token'],
                'time' => $this->datetime,
                'msg' => $this->msg,
            );
        }

        /**
         * 获取评论列表
         *
         * @return array
         */
        public static function getComments()
        {
            return self::$comments;
        }

        /**
         * 根据文章ID获取所有评论
         *
         * @param  mixed $articleid 文章ID
         *
         * @return integer
         */
        public static function getAllRecordsByArticleId($articleid)
        {
            //$_SESSION['comment_current_page_index'] * $this->comment_page_count
            $pdo = new Pdo();
            // SELECT distinct users.img, users.name, comments.date, comments.comment FROM article, users, comments WHERE comments.articleid='1' and comments.userid=users.id
            $sql = "SELECT COUNT(*) FROM comments " .
                "WHERE articleid='" . $articleid . "' AND approval='2'";
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                $row = $stmt->fetch();
                return (int) $row[0];
            }
            return 0;
        }

        /**
         * 通过文章ID返回当前页的评论列表
         *
         * @param  mixed $articleid 文章ID
         * @param  mixed $limit 获取评论数量
         * @param  mixed $offset 评论起始位置
         *
         * @return integer
         */
        public static function getRecordForCurrentPageByArticleId($articleid, $limit, $offset)
        {
            //$_SESSION['comment_current_page_index'] * $this->comment_page_count
            $pdo = new Pdo();
            // SELECT distinct users.img, users.name, comments.date, comments.comment FROM article, users, comments WHERE comments.articleid='1' and comments.userid=users.id
            $sql = "SELECT id, userid, date, comment, approval FROM comments " .
                "WHERE articleid='" . $articleid . "' AND approval='2'" .
                "ORDER BY date DESC " .
                "LIMIT " . $limit . " " .
                "OFFSET " . $offset;
            $stmt = $pdo->querySQL($sql);

            if ($stmt != false) {
                $count = 0;
                while ($row = $stmt->fetch()) {
                    $count += 1;
                    $comment = new comment($row['id'], $row['userid'], $row['date'], $row['comment'], $row['approval']);
                    self::$comments[] = $comment->serialize();
                }
                return $count;
            }
            return 0;
        }

        /**
         * 删除用户的所有评论
         *
         * @param  mixed $id 用户ID
         *
         * @return bool
         */
        public function deleteRecordByUserId($id)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "DELETE FROM comments WHERE userid = '" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            return true;
        }

        /**
         * 删除指定文章的评论
         *
         * @param  mixed $id 文章ID
         *
         * @return bool
         */
        public function deleteRecordByArticleId($id)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "DELETE FROM comments WHERE articleid = '" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            return true;
        }

        /**
         * 删除指定ID的评论
         *
         * @param  mixed $index 评论显示的索引
         * @param  mixed $id 评论ID
         *
         * @return bool
         */
        public function deleteRecord($index, $id)
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "DELETE FROM comments WHERE id = '" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                $this->records["result"] = false;
                return false;
            }
            $this->records["result"] = true;
            $this->records["index"] = $index;
            $this->records["name"] = "comment";
            return true;
        }

        /**
         * 获取所有评论
         *
         * @return bool
         */
        public function getTable()
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT * FROM comments";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            $index = 0;
            while ($row = $stmt->fetch()) {
                $this->records[$index]["id"] = $row["id"];
                $this->records[$index]["articleid"] = $row["articleid"];
                $this->records[$index]["userid"] = $row["userid"];
                $this->records[$index]["comment"] = $row["comment"];
                $this->records[$index]["date"] = $row["date"];
                $this->records[$index]["approval"] = $row["approval"];
                $index += 1;
            }
            $this->records["count"] = $index;
            return true;
        }

        /**
         * 插入评论记录
         *
         * @return bool
         */
        public function insertRecord()
        {
            $userid = $this->user->selectUserByNameOrInsertUser();

            if ($userid != false) {
                if ($this->msg === null) {
                    $this->bError = true;
                    $this->bAllRecord["error"][] = "留言内容包含非法字符！";
                    return false;
                }
                // 插入新的评论到数据库
                $pdo = new Pdo();
                //$sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES('" . $this->articleid . "','" . $userid . "','" . $this->msg . "','" . $this->datetime . "','2')";
                $sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES(?, ?, ?, ?, '1')";
                //$sql = "INSERT INTO comments (articleid, userid, comment, date, approval) VALUES('1','2','11111','2019-12-02 18:09:09','2')";
                try {
                    $stmt = $pdo->prepareSQL($sql, array($this->articleid, $userid, $this->msg, $this->datetime));
                    return $stmt;
                } catch (\PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                    return false;
                }
            } else {
                $this->bError = true;
                $this->records = $this->user->serialize();
            }
            return false;
        }

        /**
         * 用户是否登录
         *
         * @param  mixed $pwd 用户密码
         * @param  mixed $token 用户token
         *
         * @return bool
         */
        public function isLogin($pwd, $token)
        {
            return $this->user->isLogin($pwd, $token);
        }

        /**
         * 更新评论审核的结果
         *
         * @param  mixed $index 评论显示的索引
         * @param  mixed $id 评论ID
         *
         * @return bool
         */
        public function updateCommentApproval($index, $id)
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "UPDATE comments SET approval=2 WHERE id = '" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                $this->records["result"] = false;
                return false;
            }
            $this->records["result"] = true;
            $this->records["index"] = $index;
            $this->records["name"] = "comment";
            return true;
        }
    }
}
