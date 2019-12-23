<?php

namespace model\redis {

    require "./lib/predis-1.1/autoload.php";

    class Redis
    {
        /**
         * 构造函数
         *
         * @return void
         */
        public function __construct()
        {
            $redis = new \Predis\Client();

            $redis->connect('127.0.0.1', 6379);
            echo "Stored string in redis:: " . $redis->set("abc", "bbbb");
        }
    }
}
