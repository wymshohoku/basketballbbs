<?php

namespace model\util {

    require_once '../../model/mysql/mysql.php';

    use model\mysql\Pdo;

    class Token
    {
        public function api_token($before, $after)
        {
            return md5($before . date('Y-m-d', time()) . $after);
        }
        public function check_api_token($before, $after, $token)
        {
            return $token === $this->api_token($before, $after);
        }
        public function user_token($before, $after)
        {
            return md5($before . $after);
        }

        public function check_user_token($before, $after, $token)
        {
            return $token === $this->user_token($before, $after);
        }
    }
}
