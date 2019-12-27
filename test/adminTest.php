<?php
require __DIR__ . "/../controller/admin/admin.php";

use PHPUnit\Framework\TestCase;
use controller\admin\Admin;

class AdminTester extends TestCase
{
  protected $object;

  protected function setUp()
  {
    $this->object = new Admin("comment");
  }
  function testUpdateCommentApproval()
  {
    $this->object->updateCommentApproval("", "");
  }
}
