<?php

namespace model\mysql{
    $servername = "localhost";
    $dbname = "myphpweb";
    $username = "myphpwebsql";
    $password = "123456";

    class Pdo
    {
        private $conn;

        public function __constructor()
        {
            try {
                $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                echo "连接成功";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        public function __distructor()
        {
            $this->closeConnect();
        }
        public function closeConnect()
        {
            $this->conn->close();
        }
    }
}
