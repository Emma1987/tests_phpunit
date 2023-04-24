<?php

namespace App\Form;

use App\Entity\Enum\FriendType;
use App\Entity\FunFact;
use Greg0ire\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FunFactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [])
            ->add('friendType', EnumType::class, [
                'class' => FriendType::class,
                'choice_translation_domain' => 'enum',
                'prefix_label_with_class' => true,
                'placeholder' => 'Sélectionnez un type',
            ])
        ;

        $builder
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var FunFact $funFact */
                $funFact = $event->getData();
                $form = $event->getForm();

                $content = str_replace('fun', '⚱️', $form->get('content')->getData());

                $funFact->setContent($content);
                $event->setData($funFact);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FunFact::class,
        ]);
    }
}
