<?php

namespace App\DataFixtures;

use App\Entity\Octopus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OctopusFixtures extends Fixture
{
    public const USER = [
        'email' => 'grisby@octopus.ca',
        'password' => 'Pa$$woRd',
    ];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = (new Octopus())
            ->setName('Grisby')
            ->setEmail(self::USER['email'])
            ->setRoles([])
        ;

        $password = $this->passwordHasher->hashPassword($user, self::USER['password']);
        $user->setPassword($password);

        $manager->persist($user);

        $manager->flush();
    }
}
