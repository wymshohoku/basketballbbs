<?php

namespace model\index {
    require_once '../../model/common/util.php';
    require_once '../../model/article/article.php';
    require_once '../../model/common/captcha.php';

    use model\article\article;
    use model\util as Util;

    function test_input($data)
    {
        return Util\DataVerify::test_input($data);
    }

    class bbs
    {
        private $art;
        private $berror;
        private $msg;

        public static function getArticleToken($art_id)
        {
            $art = new article();
            $secret = $art->getSecret(test_input($art_id));
        }
        public static function checkToken($art_id, $token)
        {
            $art = new article();
            return $art->checkToken(test_input($art_id), $token);
        }
        public function serialize()
        {
            if ($this->berror) {
                return $this->msg;
            }
            return $this->art->serialize();
        }
        public function __construct()
        {
            $this->berror = false;
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
            if (!isset($_SESSION['comment_current_page_index'])) {
                $_SESSION['comment_current_page_index'] = 1;
            }
            $curr_index = $_SESSION['comment_current_page_index'];
            if ($index === "prev") {
                $curr_index -= 1;
            } else if ($index === "next") {
                $curr_index += 1;
            } else {
                $curr_index = (int) $index;
            }
            // 从数据库读取当前文章的评论
            $ret = $this->art->getRecordById($_SESSION['art_id'], $curr_index);
            if ($ret === false) {
                header('location:../../view/error/404.html');
                exit();
            } else {
                $_SESSION['comment_current_page_index'] = $ret;
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

        public static function isAuthCode()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET) && isset($_GET['r'])) {
                return true;
            }
            return false;
        }
        public static function getAuthCodeImg()
        {
            Util\getAuthCodeImg();
        }
        public static function isUserSubmitComment()
        {
            if (
                $_SERVER['REQUEST_METHOD'] === 'POST'
                && !empty($_POST) && isset($_POST['art_id'])
            ) {
                return true;
            }
            return false;
        }

        public function checkAuthCode($code)
        {
            if (Util\getAuthCode() === $code) {
                return true;
            }
            $this->berror = true;
            $this->msg['haserror'] = true;
            $this->msg['error'][] = '验证码不正确，请重新输入！';
            return false;
        }
        public function submitComment($art_id, $name, $msg, $datetime, $userid, $pwd, $token)
        {
            $this->berror = true;
            $this->msg['haserror'] = true;
            $this->msg['error'][] = '文章ID错误！';
            $art_id = test_input($art_id);
            // 提交的评论的文章id是否是当前访问的文章id
            if ($art_id === $_SESSION['art_id']) {
                $this->msg = $this->art->insertComment($art_id, test_input($name), test_input($msg), test_input($datetime), test_input($userid), test_input($pwd), test_input($token));
                $this->berror = $this->msg['haserror'];
            }

            return $this->berror;
        }
    }
}
