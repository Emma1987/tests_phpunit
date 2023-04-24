<?php

namespace App\Tests\Entity;

use App\Entity\Friend;
use Greg0ire\Enum\Exception\InvalidEnumValue;
use Monolog\Test\TestCase;

class FriendTest extends TestCase
{
    public function testNewFriendWithBadTypeThrowsException()
    {
        $this->expectException(InvalidEnumValue::class);
        (new Friend())->setType('Shark');
    }
}
