<?php

namespace model\admin {
    require_once '../../model/common/util.php';
    require_once '../../model/article/article.php';
    require_once '../../model/comment/comment.php';
    require_once '../../model/user/user.php';
    require_once '../../model/common/util.php';
    
    use model\util as Util;
    use model\article\article;
    use model\comment\comment;
    use model\user\user;

    class Admin
    {
        private $func;

        public function __construct($func)
        {
            $f = Util\DataVerify::test_input($func);
            if($f == 'user'){
                $this->func = new user();
            }
        }
        public function serialize()
        {
            return $this->func->serialize();
        }
        public function getTable()
        {
            $this->func->getTable();
        }
    }
}
