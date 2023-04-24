<?php

namespace App\Tests\Controller;

use App\DataFixtures\OctopusFixtures;
use App\Repository\OctopusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected ?EntityManagerInterface $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // retrieve and log the test user
        $userRepository = static::getContainer()->get(OctopusRepository::class);
        $user = $userRepository->findOneBy(['email' => OctopusFixtures::USER['email']]);
        $this->client->loginUser($user);

        // EntityManager
        $this->client->disableReboot(); // @see https://stackoverflow.com/a/42319195/10852762
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
            $this->entityManager->getConnection()->close();
            $this->entityManager->close();
        }
    }
}
