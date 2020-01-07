<?php

namespace app\admin\controller {

    use app\common\model\ArticleModel;
    use app\common\model\CommentModel;
    use app\common\model\UserModel;
    use app\common\model\PdoModel;
    use app\common\util as Util;

    class Admin
    {
        /**
         * 操作对象
         *
         * @var [object]
         */
        private $func;

        /**
         * 操作对象名称
         *
         * @var [string]
         */
        private $func_name;

        /**
         * 存储所有记录结果
         *
         * @var [array]
         */
        private $records;

        /**
         * 是否返回所有记录
         *
         * @var [bool]
         */
        private $bAllRecord;

        /**
         * 构造函数，通过参数传递操作对象名，构造对应的操作对象
         *
         * @param  mixed $func 操作对象名
         *
         * @return void
         */
        public function __construct($func)
        {
            $this->bAllRecord = false;
            $this->func_name = $func;
            if ($this->func_name == 'user') {
                $this->func = new UserModel();
            } else if ($this->func_name == 'article') {
                $this->func = new ArticleModel();
            } else if ($this->func_name == 'comment') {
                $this->func = new CommentModel();
            } else if ($this->func_name == 'login') {
                $this->func = $this;
            }
        }

        /**
         * 序列化
         *
         * @return false或array
         */
        public function serialize()
        {
            if ($this->bAllRecord) {
                return $this->records;
            }
            return $this->func->serialize();
        }

        public function login($username, $password, $token)
        {
        }
        
        /**
         * 更新留言的审核状态
         *
         * @param  mixed $index 记录显示的索引
         * @param  mixed $id 记录在数据库中的ID
         *
         * @return void
         */
        public function updateCommentApproval($index, $id)
        {
            $index = Util\DataVerify::test_input($index);
            $id = Util\DataVerify::test_input($id);

            return $this->func->updateCommentApproval($index, $id);
        }

        /**
         * 删除记录
         *
         * @param  mixed $index 记录显示的索引
         * @param  mixed $id 记录在数据库中的ID
         *
         * @return void
         */
        public function deleteRecord($index, $id)
        {
            $index = Util\DataVerify::test_input($index);
            $id = Util\DataVerify::test_input($id);

            if ($this->func_name == "user") {
                // 删除用户时，需要删除该用户的评论
                $comment = new CommentModel();
                if ($comment->deleteRecordByUserId($id) === false) {
                    $this->bAllRecord = true;
                    $this->records["result"] = false;
                    return false;
                }
            }
            if ($this->func_name == "article") {
                // 删除文章时，要把该文章的评论删除
                $comment = new CommentModel();
                if ($comment->deleteRecordByArticleId($id) === false) {
                    $this->bAllRecord = true;
                    $this->records["result"] = false;
                    return false;
                }
            }
            $this->func->deleteRecord($index, $id);
        }

        /**
         * 获取所有操作对象的记录
         *
         * @return bool
         */
        public function getTable()
        {
            if ($this->func === $this) {

                $this->bAllRecord = true;

                $pdo = new PdoModel();
                // 查询用户
                $sql = "SELECT * FROM admin";
                $stmt = $pdo->querySQL($sql);
                if ($stmt === false) {
                    return false;
                }
                $index = 0;
                while ($row = $stmt->fetch()) {
                    $this->records[$index]["name"] = $row["name"];
                    $this->records[$index]["img"] = $row["img"];
                    $this->records[$index]["authority"] = $row["authority"];
                    $index += 1;
                }
                $this->records["count"] = $index;
                return true;
            } else {
                $this->func->getTable();
            }
        }
        /**
         * 过滤信息
         *
         * @param  mixed $data 需要过滤的字符串
         *
         * @return string
         */
        public function test_input($data)
        {
            return Util\DataVerify::test_input($data);
        }

        /**
         * 生成token
         *
         * @return string
         */
        public function getToken()
        {
            $t = new Util\Token();
            return $t->api_token("login", date("Y-m-d H:i:s", time()));
        }

        public function getAuthCode($rand)
        {
            // 生成验证码图片
            return Util\Captcha::getAuthCodeImg();
        }

        public function checkAdmin($post)
        {
            $error = "用户名或密码错误！";
            //  验证失败，将 $_SESSION["admin"] 置为 false
            $_SESSION["admin"] = false;

            // 验证用户
            if ($post['authcode'] !== Util\Captcha::getAuthCode()) {
                $error = "验证码错误！";
            } else if ($post['token'] === $_SESSION["token"]) {
                $isLogin = false;
                $username = $this->test_input($post['username']);
                $pdo = new PdoModel();
                $sql = "SELECT passwd FROM admin WHERE name='$username'";
                $stmt = $pdo->querySQL($sql);

                if ($stmt != false) {
                    while ($row = $stmt->fetch()) {
                        $hashedPassword = $row[0];
                        $isLogin = password_verify($this->test_input($_POST['password']), $hashedPassword);

                        if ($isLogin) {
                            $_SESSION["admin"] = true;
                            return "";
                        }
                    }
                }
            }
            return $error;
        }

        public function isLogin()
        {
            if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
                //  判断是否登陆
                return true;
                // header('Content-Type:application/json; charset=utf-8');
                // $loc['location'] = '/view/admin/admin.html';
                // $json = json_encode($loc);
                // exit($json);
            } else {
                return false;
            }
        }

        public function logoutAdmin()
        {
            $_SESSION["admin"] = false;
        }
    }
}
