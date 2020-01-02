<?php

use PHPUnit\Framework\TestCase;
use controller\Admin;

require_once __DIR__ . '/../controller/admin/admin.php';

class AdminTester extends TestCase
{
	protected $object;

	protected function setUp()
	{
		$this->object = new Admin("comment");
	}
	function testUpdateCommentApproval()
	{
		$this->assertEquals(false, $this->object->updateCommentApproval("", ""));
	}
}
