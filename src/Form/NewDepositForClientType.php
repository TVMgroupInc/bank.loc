<?php

namespace App\Form;

use App\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewDepositForClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('c');
                    $qb->orderBy('c.last_name', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($client) {
                    return "{$client->getLastName()} {$client->getFirstName()}";
                },
                'required' => true,
                'placeholder' => 'Select a client'
            ])
            ->add('currency', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'UAH' => 'UAH'
                ]
            ])
            ->add('balance', TextType::class, [
                'label' => 'Down payment',
                'required' => true
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
