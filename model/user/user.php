<?php

namespace model\user {
    require_once '../../model/mysql/mysql.php';
    use model\mysql\Pdo;

    class user
    {
        private $id;
        private $userimg = '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp';
        private $username;
        private $records;
        private $bAllRecord;

        public function __construct()
        {
            $this->bAllRecord = false;
            $a = func_get_args();
            $i = count($a);
            if (method_exists($this, $f = '__construct' . $i)) {
                call_user_func_array(array($this, $f), $a);
            }
        }
        public function serialize()
        {
            if ($this->bAllRecord) {
                return $this->records;
            }
            return array('name' => $this->username, 'img' => $this->userimg);
        }
        public function __construct1($id)
        {
            $this->id = $id;
        }
        public function __construct2($name, $img)
        {
            $this->username = $name;
            // 图片使用默认图片
            //$this->userimg = $img;
        }
        public function getTable()
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT * FROM users";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            $index = 0;
            while($row = $stmt->fetch()){
                $this->records[$index]["name"] = $row["name"];
                $this->records[$index]["img"] = $row["img"];
                $index += 1;
            }
            $this->records["count"] = $index;
            return true;
        }
        public function isUserExist($name)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT id FROM users WHERE name='" . $name . "'";
            $row = $pdo->querySQL($sql)->fetch();
            if ($row === false) {
                return false;
            }
            return $row;
        }
        public function getUserNameAndImage()
        {
            $pdo = new Pdo();
            $sql = "SELECT img, name FROM users WHERE id='" . $this->id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }

            $row = $stmt->fetch();
            $this->username = $row['name'];
            $this->userimg = $row['img'];
        }
        public function selectUserByNameOrInsertUser()
        {
            // 查询用户
            $row = $this->isUserExist($this->username);

            // 用户不存在
            if ($row === false) {
                $pdo = new Pdo();
                $sql = "INSERT INTO users (name, img) VALUES('" . $this->username . "','" . $this->userimg . "')";
                $stmt = $pdo->prepareSQL($sql);
                if ($stmt != false) {
                    //$pdo->closeConnect();
                    $this->id = $this->isUserExist($this->username)['id'];
                } else {
                    return false;
                }
            } else {
                $this->id = $row['id'];
            }

            return $this->id;
        }
    }
}
