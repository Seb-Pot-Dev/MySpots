<?php

namespace App\Form;

use App\Entity\Spot;
use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SpotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name')
            ->add('description')
            ->add('adress')
            ->add('cp')
            ->add('city')
            ->add('lat'
            , NumberType::class,[
                'attr' => [
                    'readonly' => 'readonly',
                ]
            ]
            )
            ->add('lng'
            , NumberType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                ]
            ]
            )
            //affiché des checkboxs non obligatoire avec plusieurs choix 
            ->add('modules', EntityType::class,[
                'class'=> Module::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>true,
                'required'=>false,
                'attr' => [
                    'class' => 'form-row'
                ]
            ])
            ->add('pictures', FileType::class, [
                'label' => 'Photos',
                'multiple'=> true,
                'mapped' => false,
                'required' => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Spot::class,
        ]);
        //ajout d'une option custom
        $resolver->setRequired('modules');

    }
}
