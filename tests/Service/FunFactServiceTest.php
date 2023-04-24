<?php

namespace App\Tests\Service;

use App\Entity\Enum\FriendType;
use App\Entity\FunFact;
use App\Repository\FunFactRepository;
use App\Service\FunFactService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FunFactServiceTest extends KernelTestCase
{
    public function testFindAllFunFactsOrderedByFriendTypeAndContentAscWithDatabaseData()
    {
        self::bootKernel();
        $funFactRepository = static::getContainer()->get(FunFactRepository::class);
        $funFacts = $funFactRepository->findAllFunFactsOrderedByFriendTypeAndContentAsc();

        $this->assertCount(2, $funFacts);

        $this->assertStringContainsStringIgnoringCase('Female lobsters can carry live sperm for up to two years', $funFacts[0]->getContent());
        $this->assertEquals(FriendType::LOBSTER, $funFacts[0]->getFriendType());

        $this->assertStringContainsStringIgnoringCase('Sea slugs are colorblind', $funFacts[1]->getContent());
        $this->assertEquals(FriendType::SEA_SLUG, $funFacts[1]->getFriendType());
    }

    public function testFindAllFunFactsOrderedByFriendTypeAndContentAscWithMockData()
    {
        $funFactRepository = $this->getMockBuilder(FunFactRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $funFactRepository
            ->method('findAllFunFactsOrderedByFriendTypeAndContentAsc')
            ->will($this->returnValue($this->getFunFacts()));

        $funFactService = new FunFactService($funFactRepository);
        $funFacts = $funFactService->findAllFunFactsOrderedByFriendTypeAndContentAsc();

        $this->assertCount(3, $funFacts);

        $this->assertStringContainsStringIgnoringCase('There are around 30 known species of clownfish', $funFacts[0]->getContent());
        $this->assertEquals(FriendType::CLOWNFISH, $funFacts[0]->getFriendType());

        $this->assertStringContainsStringIgnoringCase('All clownfish are born male', $funFacts[1]->getContent());
        $this->assertEquals(FriendType::CLOWNFISH, $funFacts[1]->getFriendType());

        $this->assertStringContainsStringIgnoringCase('Marine flatworms have both male and female sexual organs', $funFacts[2]->getContent());
        $this->assertEquals(FriendType::SEA_SLUG, $funFacts[2]->getFriendType());
    }

    /**
     *
     * PRIVATE
     *
     */

    private function getFunFacts(): array
    {
        $funFactClownfish1 = (new FunFact())
            ->setContent('There are around 30 known species of clownfish.')
            ->setFriendType(FriendType::CLOWNFISH);

        $funFactClownfish2 = (new FunFact())
            ->setContent('All clownfish are born male.')
            ->setFriendType(FriendType::CLOWNFISH);

        $funFactSeaSlug = (new FunFact())
            ->setContent('Marine flatworms have both male and female sexual organs. They engage in a battle of “penis fencing” to determine who will be a dad, and who will be a mum.')
            ->setFriendType(FriendType::SEA_SLUG);

        return [$funFactClownfish1, $funFactClownfish2, $funFactSeaSlug];
    }
}
