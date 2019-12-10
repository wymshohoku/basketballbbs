<?php

namespace model\user {
    require_once '../../model/mysql/mysql.php';
    use model\mysql\Pdo;

    class user
    {
        private $id;
        private $userimg = '../../view/images/touxiang.webp';
        private $username;
        private $records;
        private $bError;
        private $bAllRecord;

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
        public function serialize()
        {
            if ($this->bAllRecord || $this->bError) {
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
        public function deleteRecord($index, $id)
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "DELETE FROM users WHERE id = '" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                $this->records["result"] = false;
                return false;
            }
            $this->records["result"] = true;
            $this->records["index"] = $index;
            $this->records["name"] = "user";
            return true;
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
            while ($row = $stmt->fetch()) {
                $this->records[$index]["id"] = $row["id"];
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
            if ($this->username == null) {
                $this->bError = true;
                $this->records["error"][] = "昵称包含非法字符！";
                return false;
            }
            /* if($this->img == null){
            $this->bError = true;
            $this->records["error"][] = "图片路径包含非法字符！";
            return false;
            } */
            // 查询用户
            $row = $this->isUserExist($this->username);

            // 用户不存在
            if ($row === false) {
                $pdo = new Pdo();
                //$sql = "INSERT INTO users (name, img) VALUES('" . $this->username . "','" . $this->userimg . "')";
                $sql = "INSERT INTO users (name, img) VALUES(?, ?)";
                $stmt = $pdo->prepareSQL($sql, array($this->username, $this->userimg));
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
