<?php

namespace model\article {
    require_once '../../model/mysql/mysql.php';
    require_once '../../model/comment/comment.php';
    require_once '../../model/common/token.php';

    use model\comment\comment;
    use model\mysql\Pdo;
    use model\util\Token;

    \define('COMMENT_PAGE_COUNT', 5);
    \define('ARTICLE_TABLE_NAME', 'article');

    class article
    {
        private $art_id;
        private $art_title;
        private $art_text;
        private $records;
        private $bAllRecord;
        private $bError;
        private $comment_current_page_count = 0;
        private $comment_pre_paga_count = COMMENT_PAGE_COUNT; // 每一页评论的数量;
        private $comment_page_index = 0;
        private $comment_pages = 0; // 总共的评论页数
        private $comment_count = 0; // 总共的评论
        private $comment_array = array();
        private $article_json;

        public function __construct()
        {
            $this->bError = false;
            $this->bAllRecord = false;
        }
        public function serialize()
        {
            if ($this->bAllRecord || $this->bError) {
                return $this->records;
            }
            $token = $this->getToken($this->art_id);

            $this->article_json['token'] = $token;
            $this->article_json['haserror'] = false;
            $this->article_json['art_id'] = $this->art_id;
            $this->article_json['art_title'] = $this->art_title;
            $this->article_json['art_text'] = $this->art_text;
            $this->article_json['comment_current_page_count'] = $this->comment_current_page_count;
            $this->article_json['comment_pre_paga_count'] = $this->comment_pre_paga_count;
            $this->article_json['comment_page_index'] = $this->comment_page_index;
            $this->article_json['comment_pages'] = $this->comment_pages;
            $this->article_json['comment_count'] = $this->comment_count;
            $this->article_json['comments'] = $this->comment_array;
            return $this->article_json;
        }
        public function getToken($id, $secret = "")
        {
            $t = new Token();
            if ($secret === "") {
                $secret = $this->getSecret($id);
            }
            $token = $t->api_token($id, $secret);
            return $token;
        }
        public function checkToken($id, $token)
        {
            $t = new Token();
            $secret = $this->getSecret($id);
            return $t->check_api_token($id, $secret, $token);
        }
        public function getSecret($id)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT secret FROM " . ARTICLE_TABLE_NAME . " WHERE id='" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            $row = $stmt->fetch();
            return $row[0];
        }
        public function deleteRecord($index, $id)
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "DELETE FROM " . ARTICLE_TABLE_NAME . " WHERE id='" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                $this->records["result"] = false;
                return false;
            }
            $this->records["result"] = true;
            $this->records["index"] = $index;
            $this->records["name"] = "article";
            return true;
        }
        public function getTable()
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT * FROM " . ARTICLE_TABLE_NAME;
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            $index = 0;
            while ($row = $stmt->fetch()) {
                $this->records["articles"][$index]["id"] = $row["id"];
                $this->records["articles"][$index]["title"] = $row["title"];
                $this->records["articles"][$index]["text"] = $row["text"];

                $t = new Token();
                $this->records["articles"][$index]['token'] =
                    $this->getToken($row["id"], $row["secret"]);
                $index += 1;
            }
            $this->records["pages"] = 4;
            $this->records["count"] = $index;
            return true;
        }
        public function getRecordById($artid, $comment_current_page_index)
        {
            $pdo = new Pdo();
            $sql = "SELECT title, text FROM " . ARTICLE_TABLE_NAME . " WHERE id='" . $artid . "'";
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

            // 获取评论的总数
            $this->comment_count = comment::getAllRecordsByArticleId($this->art_id);

            // 判断分页是否超出范围
            $this->comment_pages = (int) ($this->comment_count / COMMENT_PAGE_COUNT + (($this->comment_count % COMMENT_PAGE_COUNT) > 0 ? 1 : 0));
            if ($this->comment_pages < $comment_current_page_index)
                $this->comment_page_index = $this->comment_pages;

            // 获取当前分页的评论
            $offset = ($this->comment_page_index - 1) * COMMENT_PAGE_COUNT;
            // 获取当前页评论的数量
            $this->comment_current_page_count = comment::getRecordForCurrentPageByArticleId($this->art_id, COMMENT_PAGE_COUNT, $offset);
            $_SESSION['comment_count'] = $this->comment_count;
            $this->comment_array = comment::getComments();

            return $this->comment_page_index;
        }
        public function insertRecord()
        {
        }

        public function insertComment($artid, $name, $msg, $datetime, $userid, $pwd, $token)
        {
            $comment = new comment($artid, $name, $msg, $datetime, $userid, '2');
            $this->records = $comment->isLogin($pwd, $token);
            if ($this->records["haserror"] === false) {
                $result = $comment->insertRecord();

                if ($result != false) { // 插入数据成功
                    $this->bAllRecord = true;
                    $this->art_id = $_SESSION['art_id'];
                    $this->comment_pre_paga_count = COMMENT_PAGE_COUNT;
                    $this->comment_pages = 0;
                    $_SESSION['comment_count'] += 1;
                    $this->comment_count = $_SESSION['comment_count'];
                    //$this->comment_array[] = $comment->serialize();

                    $this->comment_pages = (int) ($this->comment_count / COMMENT_PAGE_COUNT + (($this->comment_count % COMMENT_PAGE_COUNT) > 0 ? 1 : 0));
                } else { // 插入数据失败
                    $this->bError = true;
                    $this->records = $comment->serialize();
                    $this->records["haserror"] = true;
                }
            }else{
                $this->bError = true;
                // $this->records = $comment->serialize();
                // $this->records["haserror"] = true;
            }
        }
    }
}
