<?php

namespace model\util {
    
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
