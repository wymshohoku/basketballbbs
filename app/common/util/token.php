<?php

namespace app\common\util {

    class Token
    {
        /**
         * 获取接口token
         *
         * @param  mixed $before token的前置字符
         * @param  mixed $after token的后置字符
         *
         * @return string
         */
        public function api_token($before, $after)
        {
            return md5($before . date('Y-m-d', time()) . $after);
        }

        
        /**
         * 检查接口token
         *
         * @param  mixed $before token的前置字符
         * @param  mixed $after token的后置字符
         * @param  mixed $token 待检测token
         *
         * @return void
         */
        public function check_api_token($before, $after, $token)
        {
            return $token === $this->api_token($before, $after);
        }

        /**
         * 获取用户token
         *
         * @param  mixed $before token的前置字符
         * @param  mixed $after token的后置字符
         *
         * @return void
         */
        public function user_token($before, $after)
        {
            return md5($before . $after);
        }

        /**
         * 检查用户token
         *
         * @param  mixed $before token的前置字符
         * @param  mixed $after token的后置字符
         * @param  mixed $token 待检测token
         *
         * @return void
         */
        public function check_user_token($before, $after, $token)
        {
            return $token === $this->user_token($before, $after);
        }
    }
}
