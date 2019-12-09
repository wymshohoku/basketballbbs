<?php

namespace model\mysql {

//require_once '../../model/common/util.php';

    class Pdo
    {
        private $servername;
        private $dbname;
        private $username;
        private $password;
        private $charset;

        private $conn;
        private $error;

        public function __construct()
        {
            $this->servername = "localhost";
            $this->dbname = "myphpweb";
            $this->username = "myphpwebsql";
            $this->password = "123456";
            $this->charset = 'utf8';

            try {
                $this->conn = new \PDO("mysql:host=$this->servername;dbname=$this->dbname;charset=$this->charset",
                    $this->username, $this->password);
                // 设置 PDO 错误模式，用于抛出异常
                $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
        }
        public function __destruct()
        {
            $this->closeConnect();
        }
        public function closeConnect()
        {
            $this->conn = null;
        }

        public function getErrorMsg()
        {
            return $this->error;
        }
        public function querySQL($sql)
        {
            try {
                $result = $this->conn->query($sql);
            } catch (PDOException $e) {
                $this->error = "Error: " . $e->getMessage();
                return false;
            }
            return $result;
        }
        public function prepareSQL($sql, $params)
        {
            $stmt = $this->conn->prepare($sql);
            if ($stmt->execute($params)) {
                return $stmt;
            }

            return false;
        }
    }
}
