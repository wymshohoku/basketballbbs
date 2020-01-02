<?php
namespace controller {

    use model\Article;

    require_once __DIR__ . '/../../model/autoload.php';

    class Home
    {
        /**
         * 文章对象
         *
         * @var object
         */
        private $art;

        /**
         * 构造函数
         *
         * @return void
         */
        public function __construct()
        {
            $this->art = new Article();
        }
        
        /**
         * 序列化文章输出
         *
         * @return array
         */
        public function serialize()
        {
            return $this->art->serialize();
        }

        /**
         * 获取文章列表
         *
         * @return void
         */
        public function getArticles()
        {
            $this->art->getTable();
        }
    }
}
