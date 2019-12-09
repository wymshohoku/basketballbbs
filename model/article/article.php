<?php

namespace model\article {
    require_once '../../model/mysql/mysql.php';
    require_once '../../model/comment/comment.php';

    use model\comment\comment;
    use model\mysql\Pdo;

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
            $article_json['haserror'] = false;
            $article_json['art_id'] = $this->art_id;
            $article_json['art_title'] = $this->art_title;
            $article_json['art_text'] = $this->art_text;
            $article_json['comment_current_page_count'] = $this->comment_current_page_count;
            $article_json['comment_pre_paga_count'] = $this->comment_pre_paga_count;
            $article_json['comment_page_index'] = $this->comment_page_index;
            $article_json['comment_pages'] = $this->comment_pages;
            $article_json['comment_count'] = $this->comment_count;
            $article_json['comments'] = $this->comment_array;
            return $article_json;
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
            $offset = ($this->comment_page_index - 1) * COMMENT_PAGE_COUNT;
            // 获取当前页评论的数量
            $this->comment_current_page_count = comment::getRecordForCurrentPageByArticleId($this->art_id, COMMENT_PAGE_COUNT, $offset);
            $_SESSION['comment_count'] = $this->comment_count;
            $this->comment_array = comment::getComments();

            $this->comment_pages = (int) ($this->comment_count / COMMENT_PAGE_COUNT + (($this->comment_count % COMMENT_PAGE_COUNT) > 0 ? 1 : 0));
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
                $this->comment_pre_paga_count = COMMENT_PAGE_COUNT;
                $this->comment_pages = 0;
                $_SESSION['comment_count'] += 1;
                $this->comment_count = $_SESSION['comment_count'];
                $this->comment_array[] = $comment->serialize();

                $this->comment_pages = (int) ($this->comment_count / COMMENT_PAGE_COUNT + (($this->comment_count % COMMENT_PAGE_COUNT) > 0 ? 1 : 0));
            } else { // 插入数据失败
                $this->bError = true;
                $this->records = $comment->serialize();
                $this->records["haserror"] = true;
            }
        }
    }
}
