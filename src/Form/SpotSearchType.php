<?php

// src/Form/SpotSearchType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class SpotSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('moduleTypes', ChoiceType::class, [
                'choices' => [
                    'Ramp' => 'Ramp',
                    'Rail' => 'Rail',
                    // ... other module types
                ],
                'multiple' => true,
                'required' => false,
            ]);
    }
}

