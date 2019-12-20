<?php

namespace model\util {
    class DataVerify
    {
        private static function filterWords($str)
        {
            $farr = array(
                "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
                "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
                "/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is",
            );
            $str = preg_replace($farr, '', $str);
            return $str;
        }
        private static function filterArr($arr)
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
        public static function test_input($data)
        {
            $data = self::filterWords($data);
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    }

    class DateTime
    {
        public static function getDates($startdate, $enddate)
        {
            $date = floor((strtotime($enddate) - strtotime($startdate)) / 86400);
            return $date;
        }
        public static function getHours($startdate, $enddate)
        {
            $hour = floor((strtotime($enddate) - strtotime($startdate)) % 86400 / 3600);
            return $hour;
        }
        public static function getMinutes($startdate, $enddate)
        {
            $minute = floor((strtotime($enddate) - strtotime($startdate)) % 86400 / 60);
            return $minute;
        }
        public static function getSeconds($startdate, $enddate)
        {
            $second = floor((strtotime($enddate) - strtotime($startdate)) % 86400 % 60);
            return $second;
        }
    }
}
