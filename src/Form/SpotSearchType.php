<?php

// src/Form/SpotSearchType.php
namespace App\Form;

use App\Entity\Module;
use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SpotSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'attr' => [
                    'placeholder' => 'Recherche par nom...'],
                'required' => false,
                'label' => false,
            ])
            //afficher des checkboxs non obligatoire avec plusieurs choix 
            ->add('moduleFilter', EntityType::class,[
                'class'=> Module::class,
                'choice_label'=>'name',
                'expanded' => true,
                'multiple'=>true,
                'attr' => [
                    'class' => 'form-row'
                ],
                'required' => false
            ])
            ->add('official', CheckboxType::class, [
                'label' => 'Skatepark',
                'required' => false,
            ])
            ->add('covered', CheckboxType::class, [
                'label' => 'Couvert',
                'required' => false,
            ])
            ->add('onlyValidated', CheckboxType::class, [
                'label' => 'Spots valides',
                'required' => false,
            ])
            ->add('order', ChoiceType::class, [
                'label' => 'Trier par : ',
                'choices' => [
                    'Ajout: Nouveau en premier' => 'DateNew',
                    'Ajout: Nouveau en dernier' => 'DateLast',
                    'Note: croissant' => 'noteAsc',
                    'Note: décroissant' => 'noteDesc'
                ],
            ]);
}
public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'data_class' => SearchData::class,
        // pour que les parametres passent dans l'URL
        'method' => 'GET',
        // formulaire de recherche, pas besoin de csrf
        'csrf_protection' => false
    ]);
}

public function getBlockPrefix()
{
    // éviter de mettre le prefix du model SearchData
    return '';
}
}

