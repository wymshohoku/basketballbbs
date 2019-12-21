<?php

namespace model\user {
    require_once '../../model/common/util.php';
    require_once '../../model/mysql/mysql.php';
    require_once '../../model/common/token.php';

    use model\mysql\Pdo;
    use model\util as Util;

    class user
    {
        private $id;
        private $username;
        private $userimg = '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp';
        private $bAllRecord;
        private $records;
        private $bError;
        private $errors;

        public function serialize()
        {
            if ($this->bError) {
                return $this->errors;
            }
            if ($this->bAllRecord) {
                return $this->records;
            }
            return array('name' => $this->username, 'img' => $this->userimg);
        }
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
        public function __construct3($id, $name, $img)
        {
            $this->id = $id;
            $this->username = $name;
            // 图片使用默认图片
            //$this->userimg = $img;
        }
        public function updateUserPassword($pwd)
        {
            $pdo = new Pdo();
            $pwd = password_hash($pwd, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET passwd='" . $pwd . "'  WHERE name='$this->username'";
            $stmt = $pdo->querySQL($sql);
            return $stmt;
        }
        public function isLogin($pwd, $token)
        {
            $this->errors['haserror'] = true;
            $row = $this->isUserExist($this->username);
            if ($row !== false) {
                //$this->updateUserPassword($pwd);
                if ($pwd !== "" && password_verify($pwd, $row['passwd'])) {
                    $this->errors['haserror'] = false;
                    $this->errors['id'] = $row['id'];
                    $this->errors['token'] = $this->getUserToken($this->username)['token'];
                } else if ($this->id !== "" && $token !== "") {
                    if ($token === $row['token']) {
                        $minutes = Util\DateTime::getMinutes(date('Y-m-d H:i:s', time()), $row['expire_time']);
                        if ($minutes >= 0) {
                            $this->errors['haserror'] = false;
                            $this->errors['token'] = $this->getUserToken($this->username)['token'];
                        }else{
                            $this->errors['token'] = "";
                        }
                    }
                }
            }
            if ($this->errors['haserror'] === true) {
                $this->bError = true;
                if ($pwd !== "") {
                    $this->errors['error'][] = '登陆失败，用户名或者密码错误！';
                } else {
                    $this->errors['error'][] = '用户未登陆，或登陆失败！';
                }
            }
            return $this->errors;
        }
        public function getUserToken($name)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT * FROM users WHERE name='" . $name . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                return false;
            }
            $row = $stmt->fetch();
            // if ($row["token"] === "") {
            //     $row["token"] = $this->createToken($row["id"], $row["name"], $row["passwd"]);
            // }
            $row["token"] = $this->createToken($row["id"], $row["name"], $row["passwd"]);
            return array(
                "id" => $row["id"],
                "name" => $row["name"],
                "passwd" => $row["passwd"],
                "token" => $row["token"],
                "expire_time" => $row["expire_time"],
            );
        }
        public function createToken($id, $name, $passwd)
        {
            $token = new Util\Token();
            $expireTime = new \DateTime(date('Y-m-d H:i:s', time()));
            date_add($expireTime, date_interval_create_from_date_string("10 minutes"));
            $expireTime = $expireTime->format('Y-m-d H:i:s');
            $this->token = $token->user_token($id . $name . $passwd, $expireTime);
            $this->updateTokenAndExpireTime($id, $this->token, $expireTime);
            return $this->token;
        }
        public function updateTokenAndExpireTime($id, $token, $expireTime)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "UPDATE users SET token='" . $token . "', expire_time='" .
                $expireTime . "' WHERE id='" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                $this->bError = true;
                $this->errors['haserror'] = true;
                $this->errors["error"][] = "更新Token和过期时间失败！";
                return false;
            }
        }
        public function deleteRecord($index, $id)
        {
            $this->bAllRecord = true;

            $pdo = new Pdo();
            // 查询用户
            $sql = "DELETE FROM users WHERE id = '" . $id . "'";
            $stmt = $pdo->querySQL($sql);
            if ($stmt === false) {
                $this->bError = true;
                $this->errors['haserror'] = true;
                $this->errors["error"][] = "删除记录失败！";
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
        private function isUserExist($name)
        {
            $pdo = new Pdo();
            // 查询用户
            $sql = "SELECT * FROM users WHERE name='" . $name . "'";
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
                $this->errors['haserror'] = true;
                $this->errors["error"][] = "昵称包含非法字符！";
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
