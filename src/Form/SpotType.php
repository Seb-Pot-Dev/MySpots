<?php

namespace App\Form;

use App\Entity\Spot;
use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SpotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $modules = $options['modules'];

        
        $builder
            ->add('name')
            ->add('description')
            // ->add('adress')
            // ->add('cp')
            // ->add('city')
            ->add('lat')
            ->add('lng')
            ->add('modules', EntityType::class,[
                'class'=> Module::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>true,
                'required'=>false
            ])
            // $builder
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
