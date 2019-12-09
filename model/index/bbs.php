<?php

namespace model\index {
    require_once '../../model/common/util.php';
    require_once '../../model/article/article.php';

    use model\util as Util;
    use model\article\article;

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
}
