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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SpotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('adress' , TextType::class)
            ->add('cp', TextType::class)
            ->add('city', TextType::class)
            ->add('lat', NumberType::class)
            ->add('lng', NumberType::class)
            //affiché des checkboxs non obligatoire avec plusieurs choix 
            ->add('modules', EntityType::class,[
                'class'=> Module::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>true,
                'required'=>false,
                'attr' => [
                    'class' => 'form-row'
                ],
                'label_attr' => [
                    'class' => 'inline-flex'
                ]
            ])
            ->add('covered', CheckboxType::class,[
                'label' => 'Couvert?',
                'required' => false
            ])
            ->add('official', CheckboxType::class,[
                'label' => 'Skatepark?',
                'required' => false
            ])
            ->add('pictures', FileType::class, [
                'label' => 'Photos',
                'multiple'=> true,
                'mapped' => false,
                'required' => false
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Envoyer',
                'attr' => ['class' => ' submit-form-spot header-medium']
            ])
            /* Pour après la pause : 
            voir où l'info passe après la soumission du formulaire PictureType dans showSpot
            voir si je peux passer par le SpotType pour ajouter uniquement une photo (rendre les autres champs non obligatoire et renseigner automatiquement le spotId ?)
            */
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
