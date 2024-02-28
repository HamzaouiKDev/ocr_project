<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TypePageType extends AbstractType
{ 
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typePages = $options['typePages'];
        $builder->add('attribute', ChoiceType::class, [
            'choices' => $typePages,
            'attr' => ['class'=> 'form-control  mb-2',],
            'label' => false,
            
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'typePages' => null,
            // Configure your form options here
        ]);
    }
}
