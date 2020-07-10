<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositCaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'required' => true
            ])
            ->add('last_name', TextType::class, [
                'required' => true
            ])
            ->add('date_of_birth', TextType::class, [
                'required' => true
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Man' => 1,
                    'Woman' => 0
                ]
            ])
            ->add('inn', TextType::class, [
                'required' => true
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
