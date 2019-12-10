<?php

namespace model\admin {
    require_once '../../model/common/util.php';
    require_once '../../model/article/article.php';
    require_once '../../model/comment/comment.php';
    require_once '../../model/user/user.php';
    require_once '../../model/common/util.php';
    require_once '../../model/mysql/mysql.php';

    use model\mysql\Pdo;
    use model\article\article;
    use model\comment\comment;
    use model\user\user;
    use model\util as Util;

    class Admin
    {
        private $func;
        private $func_name;
        private $records;
        private $bAllRecord;

        public function __construct($func)
        {
            $this->bAllRecord = false;
            $this->func_name = $func;
            if ($this->func_name == 'user') {
                $this->func = new user();
            } else if ($this->func_name == 'article') {
                $this->func = new article();
            } else if ($this->func_name == 'comment') {
                $this->func = new comment();
            } else if ($this->func_name == 'my') {
                $this->func = $this;
            }
        }
        public function serialize()
        {
            if ($this->bAllRecord) {
                return $this->records;
            }
            return $this->func->serialize();
        }
        public function deleteRecord($index, $id)
        {
            $index = Util\DataVerify::test_input($index);
            $id = Util\DataVerify::test_input($id);

            if($this->func_name == "user"){
                // 删除用户时，需要删除该用户的评论
                $comment = new comment();
                if($comment->deleteRecordByUserId($id) === false){
                    $this->bAllRecord = true;
                    $this->records["result"]=false;
                    return false;
                }
            }
            if($this->func_name == "article"){
                // 删除文章时，要把该文章的评论删除
                $comment = new comment();
                if($comment->deleteRecordByArticleId($id) === false){
                    $this->bAllRecord = true;
                    $this->records["result"]=false;
                    return false;
                }
            }
            $this->func->deleteRecord($index, $id);
        }
        public function getTable()
        {
            if ($this->func === $this) {

                $this->bAllRecord = true;

                $pdo = new Pdo();
                // 查询用户
                $sql = "SELECT * FROM admin";
                $stmt = $pdo->querySQL($sql);
                if ($stmt === false) {
                    return false;
                }
                $index = 0;
                while($row = $stmt->fetch()){
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
    }
}
