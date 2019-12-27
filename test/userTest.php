<?php
require __DIR__ . "/../model/user/user.php";

use PHPUnit\Framework\TestCase;
use model\user\user;

class UserTest extends TestCase
{
    protected $object;

    protected function setUp()
    {
      $this->object = new user();
    }
    /**
     * @depends setUp
     *
     * @return void
     */
    function testUpdateUserPassword()
    {
        $stmt = $this->object->updateUserPassword("");
        var_dump($stmt);
        $this->assertEquals(false, $stmt);
    }
}
