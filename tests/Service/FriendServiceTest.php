<?php

namespace App\Tests\Service;

use App\Service\FriendService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FriendServiceTest extends KernelTestCase
{
    public function testGetAllMyFriendsAsArray()
    {
        $friendService = static::getContainer()->get(FriendService::class);
        $friends = $friendService->getAllMyFriendsAsArray();

        $this->assertIsArray($friends);
        $this->assertCount(6, $friends);

        $this->assertArrayHasKey('Harley', $friends);
        $this->assertEquals('Clam 🐚', $friends['Harley']);

        $this->assertArrayHasKey('Bubbles', $friends);
        $this->assertEquals('Sea slug 🪱', $friends['Bubbles']);

        $this->assertArrayHasKey('Flash', $friends);
        $this->assertStringContainsString('Sea slug', $friends['Flash']);

        $this->assertArrayHasKey('Maurice', $friends);
        $this->assertEquals('Lobster 🦞', $friends['Maurice']);

        $this->assertArrayHasKey('Rainbow', $friends);
        $this->assertEquals('Clownfish 🐟', $friends['Rainbow']);

        $this->assertArrayHasKey('Einstein', $friends);
        $this->assertStringContainsStringIgnoringCase('clownfish', $friends['Einstein']);

        $this->assertArrayNotHasKey('Nemo', $friends);
    }
}
