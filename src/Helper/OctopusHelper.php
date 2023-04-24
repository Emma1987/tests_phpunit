<?php

namespace App\Helper;

class OctopusHelper
{
    public static function isOctopusSleeping(\DateTimeInterface $dateTime): bool
    {
        // The octopus sleeps from 5:30pm to 3am
        $date = $dateTime->format('m/d/Y');

        $startSleepingTonightAt = (new \DateTimeImmutable($date))->setTime(17, 30, 00);
        $endSleepingTonightAt = (new \DateTimeImmutable($date))->add(new \DateInterval('P1D'))->setTime(3, 00, 00);

        $startSleepingTomorrowMorningAt = (new \DateTimeImmutable($date))->modify('-1 day')->setTime(17, 30, 00);
        $endSleepingTomorrowMorningAt = (new \DateTimeImmutable($date))->setTime(3, 00, 00);

        return ($dateTime >= $startSleepingTonightAt && $dateTime <= $endSleepingTonightAt)
            || ($dateTime >= $startSleepingTomorrowMorningAt && $dateTime <= $endSleepingTomorrowMorningAt);
    }

    public static function getOctopusMood(string $dayOfTheWeek): string
    {
        switch ($dayOfTheWeek) {
            case 'Monday':
                return 'sad 😢';
            case 'Tuesday':
            case 'Wednesday':
                return 'happy 😊';
            case 'Thursday':
                return 'tired 🥱';
            case 'Friday':
                return 'very tired 😴';
            case 'Saturday':
                return 'ready to party 🥳';
            case 'Sunday':
                return 'hungover 🤢';
            default:
                return 'I don\'t know ☹️';
        }
    }
}
