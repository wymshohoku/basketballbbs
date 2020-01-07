<?php
namespace app\bbs\controller {

    use app\common\model\ArticleModel;

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
            $this->art = new ArticleModel();
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
