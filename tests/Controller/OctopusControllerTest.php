<?php

namespace App\Tests\Controller;

use App\Entity\Enum\FriendType;
use App\Repository\FunFactRepository;

class OctopusControllerTest extends AppWebTestCase
{
    public function testOctopusIndexIsSuccessful()
    {
        $this->client->request('GET', '/octopus');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#octopus-index-container');
    }

    public function testOctopusFunFactsIsSuccessful()
    {
        $this->client->request('GET', '/octopus/fun-facts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#octopus-fun-facts-container');
    }

    public function testCreateFunFactFormIsSuccessfullySubmitted()
    {
        $crawler = $this->client->request('GET', '/octopus/add-fun-fact');

        $form = $crawler->filter('form[name="fun_fact"]')->form();
        $form['fun_fact[content]'] = 'Schools of clownfish have a strict hierarchy, with the most aggressive female at the top.';
        $form['fun_fact[friendType]']->select(FriendType::CLOWNFISH);

        $this->client->request($form->getMethod(), $form->getUri(), $form->getPhpValues());

        $funFactRepository = static::getContainer()->get(FunFactRepository::class);
        $funFacts = $funFactRepository->findBy(['content' => 'Schools of clownfish have a strict hierarchy, with the most aggressive female at the top.']);
        $this->assertCount(1, $funFacts);

        $this->assertEquals('Schools of clownfish have a strict hierarchy, with the most aggressive female at the top.', $funFacts[0]->getContent());
        $this->assertEquals(FriendType::CLOWNFISH, $funFacts[0]->getFriendType());
    }

    public function testDeleteFunFactIsDeleted()
    {
        $funFactRepository = static::getContainer()->get(FunFactRepository::class);
        $funFacts = $funFactRepository->findBy(['content' => 'Female lobsters can carry live sperm for up to two years.']);

        if (empty($funFacts)) {
            throw new \LogicException('Please insert a fun fact into the database before running this test.');
        }

        $funFact = array_shift($funFacts);

        $this->client->request('GET', '/octopus/delete-fun-fact/' . $funFact->getId());
        $this->assertResponseRedirects('/octopus/fun-facts');

        // Fun fact is successfully deleted
        $funFacts = $funFactRepository->findBy(['content' => 'Female lobsters can carry live sperm for up to two years.']);
        $this->assertCount(0, $funFacts);
    }
}
