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
        private $records;
        private $bAllRecord;

        public function __construct($func)
        {
            $this->bAllRecord = false;
            $f = Util\DataVerify::test_input($func);
            if ($f == 'user') {
                $this->func = new user();
            } else if ($f == 'article') {
                $this->func = new article();
            } else if ($f == 'comment') {
                $this->func = new comment();
            } else if ($f == 'my') {
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
