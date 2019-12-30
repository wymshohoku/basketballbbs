<?php
require __DIR__ . "/../model/user/user.php";

use PHPUnit\Framework\TestCase;
use model\user\user;

class UserTest extends TestCase
{
    protected $object;

    protected function setUp()
    {
      $this->object = new user(1);
    }
    /**
     * @depends setUp
     *
     * @return void
     */
    function testUpdateUserPasswordWithEmpty()
    {
        $stmt = $this->object->updateUserPassword("");
        $this->assertEquals(false, $stmt);
    }

    /**
     * @dataProvider additionProvider
     *
     * @param  mixed $a
     * @param  mixed $b
     * @param  mixed $expected
     *
     * @return void
     */
    public function testAdd($a, $b, $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public function additionProvider()
    {
        return [
            'adding zeros'  => [0, 0, 0],
            'zero plus one' => [0, 1, 1],
            'one plus zero' => [1, 0, 1],
            'one plus one'  => [1, 1, 3]
        ];
    }
}
