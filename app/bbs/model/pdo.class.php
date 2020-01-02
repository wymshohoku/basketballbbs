<?php

namespace model {

//require_once '../../model/common/util.php';

    class Pdo
    {
        /**
         * 数据库服务器名
         *
         * @var string
         */
        private $servername;

        /**
         * 数据库名
         *
         * @var string
         */
        private $dbname;

        /**
         * 用户名
         *
         * @var string
         */
        private $username;

        /**
         * 用户密码
         *
         * @var string
         */
        private $password;

        /**
         * 数据库字符设置
         *
         * @var string
         */
        private $charset;

        /**
         * 保存数据库连接对象
         *
         * @var object
         */
        private $conn;

        /**
         * 保存错误内容
         *
         * @var string
         */
        private $error;

        /**
         * 构造函数
         *
         * @return void
         */
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
            } catch (\PDOException $e) {
                $this->error = $e->getMessage();
            }
        }

        /**
         * 析构函数
         *
         * @return void
         */
        public function __destruct()
        {
            $this->closeConnect();
        }
        
        /**
         * 关闭数据库连接
         *
         * @return void
         */
        public function closeConnect()
        {
            $this->conn = null;
        }

        /**
         * 返回错误信息
         *
         * @return string
         */
        public function getErrorMsg()
        {
            return $this->error;
        }
        
        /**
         * 执行数据库操作
         *
         * @param  mixed $sql SQL语句
         *
         * @return mixed
         */
        public function querySQL($sql)
        {
            try {
                $result = $this->conn->query($sql);
            } catch (\PDOException $e) {
                $this->error = "Error: " . $e->getMessage();
                return false;
            }
            return $result;
        }
        
        /**
         * 预处理执行SQL
         *
         * @param  mixed $sql SQL语句
         * @param  mixed $params 参数
         *
         * @return mixed
         */
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
