<?php

namespace App\DataFixtures;

use App\Entity\Enum\FriendType;
use App\Entity\FunFact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FunFactFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $seaSlugFunFact = (new FunFact())
            ->setContent('Sea slugs are colorblind: they have eyes that are primitive and only see the light or dark. Because of this, they navigate by scent using their rhinophores.')
            ->setFriendType(FriendType::SEA_SLUG);
        $manager->persist($seaSlugFunFact);

        $lobsterFunFact = (new FunFact())
            ->setContent('Female lobsters can carry live sperm for up to two years.')
            ->setFriendType(FriendType::LOBSTER);
        $manager->persist($lobsterFunFact);

        $manager->flush();
    }
}
