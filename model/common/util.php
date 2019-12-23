<?php

namespace model\util {
    class DataVerify
    {
        /**
         * 过滤指定内容
         *
         * @param  mixed $str 待过滤的内容
         *
         * @return string
         */
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

        /**
         * 过滤数组
         *
         * @param  mixed $arr 待过滤数组
         *
         * @return array
         */
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

        
        /**
         * 过滤用户输入的内容
         *
         * @param  mixed $data
         *
         * @return void
         */
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
        /**
         * 获取日期时间差
         *
         * @param  mixed $startdate 开始时间
         * @param  mixed $enddate 结束时间
         *
         * @return integer
         */
        public static function getDates($startdate, $enddate)
        {
            $date = floor((strtotime($enddate) - strtotime($startdate)) / 86400);
            return $date;
        }
        
        /**
         * 获取日期小时差
         *
         * @param  mixed $startdate 开始时间
         * @param  mixed $enddate 结束时间
         *
         * @return integer
         */
        public static function getHours($startdate, $enddate)
        {
            $hour = floor((strtotime($enddate) - strtotime($startdate)) % 86400 / 3600);
            return $hour;
        }
        
        /**
         * 获取日期分钟差
         *
         * @param  mixed $startdate 开始时间
         * @param  mixed $enddate 结束时间
         *
         * @return void
         */
        public static function getMinutes($startdate, $enddate)
        {
            $minute = floor((strtotime($enddate) - strtotime($startdate)) % 86400 / 60);
            return $minute;
        }
        
        /**
         * 获取日期秒数差
         *
         * @param  mixed $startdate 开始时间
         * @param  mixed $enddate 结束时间
         *
         * @return void
         */
        public static function getSeconds($startdate, $enddate)
        {
            $second = floor((strtotime($enddate) - strtotime($startdate)) % 86400 % 60);
            return $second;
        }
    }
}
