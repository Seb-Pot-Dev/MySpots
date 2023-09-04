<?php

// src/Form/SpotSearchType.php
namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SpotSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
            ])
            //affiché des checkboxs non obligatoire avec plusieurs choix 
            ->add('modules', EntityType::class,[
                'class'=> Module::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'attr' => [
                    'class' => 'form-row'
                ]
                ]); 
}}

