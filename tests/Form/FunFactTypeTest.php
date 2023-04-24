<?php

namespace App\Tests\Form;

use App\Entity\Enum\FriendType;
use App\Entity\FunFact;
use App\Form\FunFactType;
use Symfony\Component\Form\Test\TypeTestCase;

class FunFactTypeTest extends TypeTestCase
{
    public function testSubmitDate()
    {
        $formData = [
            'content' => 'This is a fun fact about clownfish: Male clownfish are dedicated fathers. They will prepare the nest for the female, guard the eggs, and clean the nest.',
            'friendType' => FriendType::CLOWNFISH,
        ];

        $funFact = new FunFact();
        $form = $this->factory->create(FunFactType::class, $funFact);

        $expected = (new FunFact())
            ->setContent('This is a ⚱️ fact about clownfish: Male clownfish are dedicated fathers. They will prepare the nest for the female, guard the eggs, and clean the nest.')
            ->setFriendType(FriendType::CLOWNFISH);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $funFact);
    }
}
