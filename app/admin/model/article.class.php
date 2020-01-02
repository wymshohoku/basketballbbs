<?php

namespace model {

    use model\Comment;
    use model\Pdo;
    use model\util\Token;
    
    require_once 'autoload.php';

    /**
     * 没有最多显示的评论数
     */
    \define('COMMENT_PAGE_COUNT', 5);

    /**
     * 数据库文章存放表的名称
     */
    \define('ARTICLE_TABLE_NAME', 'article');

    class Article
    {
        /**
         * 文章ID
         *
         * @var int
         */
        private $art_id;

        /**
         * 文章标题
         *
         * @var string
         */
        private $art_title;

        /**
         * 文章内容
         *
         * @var string
         */
        private $art_text;

        /**
         * 存储所有记录
         *
         * @var array
         */
        private $records;

        /**
         * 是否返回所有记录
         *
         * @var bool
         */
        private $bAllRecord;

        /**
         * 是否有错误
         *
         * @var bool
         */
        private $bError;

        /**
         * 当前分页的评论数
         *
         * @var integer
         */
        private $comment_current_page_count = 0;

        /**
         * 每一页最多显示的评论数
         *
         * @var integer
         */
        private $comment_pre_paga_count = COMMENT_PAGE_COUNT; // 每一页评论的数量;
        
        /**
         * 当前分页的索引
         *
         * @var integer
         */
        private $comment_page_index = 0;

        /**
         * 总共的分页数量
         *
         * @var integer
         */
        private $comment_pages = 0; 

        /**
         * 总共的评论数量
         *
         * @var integer
         */
        private $comment_count = 0;

        /**
         * 存放当前文章当前页的评论内容
         *
         * @var array
         */
        private $comment_array = array();

        /*******************************************************************/
        
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
         * 构造函数
         *
         * @return void
         */
        public function __construct0()
        {
        }

        /**
         * 构造函数
         *
         * @param  mixed $id 文章ID
         *
         * @return void
         */
        public function __construct1($id)
        {
            $this->art_id = $id;
        }
        /**
         * 序列化输出
         *
         * @return array 返回结果数组
         */
        public function serialize()
        {
            $token = $this->getToken($this->art_id);
            if ($this->bAllRecord || $this->bError) {
                $this->records['token'] = $token;
                return $this->records;
            }

            $article_comments['token'] = $token;
            $article_comments['haserror'] = false;
            $article_comments['art_id'] = $this->art_id;
            $article_comments['art_title'] = $this->art_title;
            $article_comments['art_text'] = $this->art_text;
            $article_comments['comment_current_page_count'] = $this->comment_current_page_count;
            $article_comments['comment_pre_paga_count'] = $this->comment_pre_paga_count;
            $article_comments['comment_page_index'] = $this->comment_page_index;
            $article_comments['comment_pages'] = $this->comment_pages;
            $article_comments['comment_count'] = $this->comment_count;
            $article_comments['comments'] = $this->comment_array;
            return $article_comments;
        }
        
        /**
         * 获取token
         *
         * @param  mixed $id 文章ID
         * @param  mixed $secret 密钥
         *
         * @return void
         */
        public function getToken($id, $secret = "")
        {
            $t = new Token();
            if ($secret === "") {
                $secret = $this->getSecret($id);
            }
            $token = $t->api_token($id, $secret);
            return $token;
        }
        
        /**
         * 检查token
         *
         * @param  mixed $id 文章ID
         * @param  mixed $token 检验的token
         *
         * @return void
         */
        public function checkToken($id, $token)
        {
            $t = new Token();
            $secret = $this->getSecret($id);
            $ret = $t->check_api_token($id, $secret, $token);
            if($ret === false){
                $this->bError = true;
                $this->records["error"][] = "未登录，或登陆失败！请重新登录。";
                $this->records["haserror"] = true;
            }
            return $ret;
        }

        
        /**
         * 获取密钥
         *
         * @param  mixed $id 文章ID
         *
         * @return void
         */
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
        
        /**
         * 删除记录
         *
         * @param  mixed $index 记录显示的索引
         * @param  mixed $id 记录的ID
         *
         * @return void
         */
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

        /**
         * 获取记录列表
         *
         * @return bool
         */
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
        
        /**
         * 通过ID和分页索引获取文章记录
         *
         * @param  mixed $artid 文章ID
         * @param  mixed $comment_current_page_index 当前分页索引
         *
         * @return void
         */
        public function getRecordById($artid)
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
        }
        
        public function getCommentsCount($artid)
        {
            $_SESSION['comment_count'] = comment::getAllRecordsByArticleId($artid);
            return $_SESSION['comment_count'];
        }

        public function getCurrentPageCommnets($artid, $comment_current_page_index)
        {
            $this->comment_pages = 0;
            $this->comment_count = 0;
            $this->comment_page_index = $comment_current_page_index;

            // 获取评论的总数
            $this->comment_count = $this->getCommentsCount($artid);

            // 判断分页是否超出范围
            $this->comment_pages = (int) ($this->comment_count / COMMENT_PAGE_COUNT + (($this->comment_count % COMMENT_PAGE_COUNT) > 0 ? 1 : 0));
            if ($this->comment_pages < $comment_current_page_index)
                $this->comment_page_index = $this->comment_pages;

            // 获取当前分页的评论
            $offset = ($this->comment_page_index - 1) * COMMENT_PAGE_COUNT;
            // 获取当前页评论的数量
            $this->comment_current_page_count = Comment::getRecordForCurrentPageByArticleId($artid, COMMENT_PAGE_COUNT, $offset);
            $this->comment_array = Comment::getComments();

            return $this->comment_page_index;
        }
        /**
         * 插入评论
         *
         * @param  mixed $artid 文章ID
         * @param  mixed $name 评论用户名
         * @param  mixed $msg 评论内容
         * @param  mixed $datetime 评论日期
         * @param  mixed $userid 用户ID
         * @param  mixed $pwd 用户密码
         * @param  mixed $token 令牌
         *
         * @return void
         */
        public function insertComment($artid, $name, $msg, $datetime, $userid, $pwd, $token)
        {
            $comment = new Comment($artid, $name, $msg, $datetime, $userid, '2');
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
