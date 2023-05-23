<?php

namespace App\Form;

use App\Entity\Notation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class NotationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', IntegerType::class,[
                'attr'=>[
                    'min' => 1,
                    'max' => 5
                ],
                'constraints'=>[
                    new Range([
                        'min'=> 1,
                        'max'=> 5,
                        'notInRangeMessage' => 'La note dois être comprise en 1 et 5'
                    ])
                ]
            ])
            // ->add('spot')
            // ->add('user')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notation::class,
        ]);
    }
}
