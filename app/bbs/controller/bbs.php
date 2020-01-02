<?php

namespace controller {

    use model\article;
    use model\util;

    require_once __DIR__ . '/../../model/autoload.php';

    class Bbs
    {
        /**
         * 文章对象
         *
         * @var object
         */
        private $art;

        /**
         * 是否存在错误
         *
         * @var bool
         */
        private $berror;

        /**
         * 返回信息
         *
         * @var array
         */
        private $msg;

        /**
         * 检查token
         *
         * @param  mixed $art_id 文章ID
         * @param  mixed $token 文章token
         *
         * @return bool
         */
        private function checkToken($art_id, $token)
        {
            return $this->art->checkToken($this->test_input($art_id), $token);
        }

        /**
         * 序列化文章输出
         *
         * @return array
         */
        private function serialize()
        {
            if ($this->berror) {
                return $this->msg;
            }
            return $this->art->serialize();
        }

        /**
         * 构造函数
         *
         * @return void
         */
        public function __construct()
        {
            $this->berror = false;
            $this->art = new article($this->getArticleId());
        }

        /**
         * 保存文章ID
         *
         * @param integer $id 文章ID
         * @return void
         */
        private function setArticleId($id)
        {
            $_SESSION['art_id'] = $this->test_input($id);
        }

        /**
         * 获取文章
         *
         * @param  mixed $art_id 当前文章ID
         *
         * @return void
         */
        private function getArticle($art_id)
        {
            // 从数据库读取当前文章的评论
            $ret = $this->art->getRecordById($art_id);
            if ($ret === false) {
                header('location:/error/404.html');
                exit();
            }
        }

        /**
         * 获取文章评论
         *
         * @param  mixed $artid 文章ID
         * @param  mixed $page 当前分页
         *
         * @return void
         */
        private function getArticleComments($artid, $page)
        {
            $index = $this->test_input($page);
            if (!isset($_SESSION['comment_current_page_index'])) {
                $_SESSION['comment_current_page_index'] = $index;
            }
            $curr_index = $_SESSION['comment_current_page_index'];
            if ($index === "prev") {
                $curr_index -= 1;
            } else if ($index === "next") {
                $curr_index += 1;
            } else {
                $curr_index = (int) $index;
            }
            $_SESSION['comment_current_page_index'] = $this->art->getCurrentPageCommnets($this->test_input($artid), $curr_index);
        }

        /**
         * 获取文章ID
         *
         * @return integer
         */
        private function getArticleId()
        {
            if (isset($_SESSION['art_id'])) {
                return $_SESSION['art_id'];
            }

            return 0;
        }

        /**
         * 是否是获取验证码请求
         *
         * @return void
         */
        private function isAuthCode()
        {
            return Util\isAuthCode();
        }

        /**
         * 返回验证码
         *
         * @return object
         */
        private function getAuthCodeImg()
        {
            return Util\getAuthCodeImg();
        }

        /**
         * 检查验证码
         *
         * @param  mixed $code 检查的验证码
         *
         * @return bool
         */
        private function checkAuthCode($code)
        {
            if (Util\getAuthCode() === $code) {
                return true;
            }
            $this->berror = true;

            $this->msg['haserror'] = true;
            $this->msg['token'] = $this->art->getToken($_SESSION['art_id']);
            $this->msg['error'][] = '验证码不正确，请重新输入！';
            return false;
        }

        /**
         * 提交评论
         *
         * @param  mixed $art_id 文章ID
         * @param  mixed $name 评论用户名
         * @param  mixed $msg 评论内容
         * @param  mixed $userid 用户ID
         * @param  mixed $pwd 用户密码
         * @param  mixed $token 用户token
         *
         * @return bool
         */
        private function submit($art_id, $name, $msg, $userid, $pwd, $token)
        {
            $datetime = date("Y-m-d H:i:s", time());
            $this->berror = true;
            $this->msg['haserror'] = true;
            $this->msg['token'] = $this->art->getToken($_SESSION['art_id']);
            $this->msg['error'][] = '文章ID错误！';
            $art_id = $this->test_input($art_id);
            // 提交的评论的文章id是否是当前访问的文章id
            if ($art_id === $_SESSION['art_id']) {
                $this->msg = $this->art->insertComment(
                    $art_id,
                    $this->test_input($name),
                    $this->test_input($msg),
                    $datetime,
                    $this->test_input($userid),
                    $this->test_input($pwd),
                    $this->test_input($token)
                );
                $this->berror = $this->msg['haserror'];
            }

            return $this->berror;
        }

        /**
         * 过滤字符串
         *
         * @param  mixed $data 需要过滤的字符串
         *
         * @return string
         */
        private function test_input($data)
        {
            return Util\DataVerify::test_input($data);
        }

        public function getArticleByID($art_id)
        {
            $this->setArticleId($art_id);

            // 用户点击了文章链接跳转，获取该文章和所有评论
            $this->getArticle($art_id);
            return $this->serialize();
        }

        public function getCommentsByArticleID($art_id, $page)
        {
            $this->getArticleComments($art_id, $page);
            return json_encode($this->serialize());
        }

        public function getAuthCode()
        {
            if ($this->isAuthCode()) {
                $this->getAuthCodeImg();
            }
        }

        public function submitComment($info)
        {
            if ($this->checkToken($info['art_id'], $info['token'])) {
                // 验证提交评论的验证码
                if ($this->checkAuthCode($info['authcode'])) {
                    // 提交评论
                    $this->submit(
                        $info['art_id'],
                        $info['username'],
                        $info['msg'],
                        $info['id'],
                        $info['pwd'],
                        $info['token']
                    );
                }
            }
            header('Content-Type:application/json; charset=utf-8');
            return json_encode($this->serialize());
        }
    }
}
