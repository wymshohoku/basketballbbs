<?php

namespace model\util {
    class DataVerify
    {
        public static function test_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        public static function filterWords($str)
        {
            $farr = array(
                "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
                "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
                "/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is",
            );
            $str = preg_replace($farr, '', $str);
            return $str;
        }
        public static function filterArr($arr)
        {
            if (is_array($arr)) {
                foreach ($arr as $k => $v) {
                    $arr[$k] = self::filterWords($v);
                }
            } else {
                $arr = self::filterWords($v);
            }
            return $arr;
        }
    }
}
