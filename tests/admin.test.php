<?php
require_once dirname(__FILE__).'/../controller/admin/admin.php';

use PHPUnit\Framework\TestCase;
use controller\admin\Admin;

class AdminTester extends TestCase
{
    protected $object;
 
    protected function setUp() {
      $this->object = new Admin("user");
    }
    function testUpdateCommentApproval()
    {
        $this->object->updateCommentApproval("", "");
    }
}