<?php

namespace App\Tests\Helper;

use App\Helper\OctopusHelper;
use PHPUnit\Framework\TestCase;

class OctopusHelperTest extends TestCase
{
    public function testIsOctopusSleeping()
    {
        $notSleepingHour = (new \DateTimeImmutable())->setTime(12, 00, 00);
        $this->assertFalse(OctopusHelper::isOctopusSleeping($notSleepingHour));

        $sleepingHour = (new \DateTimeImmutable())->setTime(23, 00, 00);
        $this->assertTrue(OctopusHelper::isOctopusSleeping($sleepingHour));

        $sleepingHour = (new \DateTimeImmutable())->setTime(01, 43, 12);
        $this->assertTrue(OctopusHelper::isOctopusSleeping($sleepingHour));
    }

    public function testGetOctopusName()
    {
        $dayOfTheWeek = ('Monday');
        $this->assertEquals('sad 😢', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('Tuesday');
        $this->assertEquals('happy 😊', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('Wednesday');
        $this->assertStringContainsStringIgnoringCase('Happy', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('Thursday');
        $this->assertEquals('tired 🥱', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('Friday');
        $this->assertEquals('very tired 😴', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('Saturday');
        $this->assertEquals('ready to party 🥳', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('Sunday');
        $this->assertEquals('hungover 🤢', OctopusHelper::getOctopusMood($dayOfTheWeek));

        $dayOfTheWeek = ('not a day');
        $this->assertEquals('I don\'t know ☹️', OctopusHelper::getOctopusMood($dayOfTheWeek));
    }
}
