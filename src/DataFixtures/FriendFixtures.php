<?php

namespace App\DataFixtures;

use App\Entity\Enum\FriendType;
use App\Entity\Friend;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FriendFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $harley = (new Friend())->setName('Harley')->setType(FriendType::CLAM);
        $manager->persist($harley);

        $bubbles = (new Friend())->setName('Bubbles')->setType(FriendType::SEA_SLUG);
        $manager->persist($bubbles);

        $flash = (new Friend())->setName('Flash')->setType(FriendType::SEA_SLUG);
        $manager->persist($flash);

        $maurice = (new Friend())->setName('Maurice')->setType(FriendType::LOBSTER);
        $manager->persist($maurice);

        $rainbow = (new Friend())->setName('Rainbow')->setType(FriendType::CLOWNFISH);
        $manager->persist($rainbow);

        $einstein = (new Friend())->setName('Einstein')->setType(FriendType::CLOWNFISH);
        $manager->persist($einstein);

        $manager->flush();
    }
}
