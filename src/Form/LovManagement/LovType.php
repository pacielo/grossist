<?php

namespace App\Form\LovManagement;

//form use stetment
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//autre use statement

class LovType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        dump($options['nom']);
        $builder->add('title', TextType::class, [
                    'required' => true, ])
                ->add('sort', NumberType::class, [
                    'required' => false, ])
                ;


        if($options['nom'] === 'Ville')
            $builder->add('country');

        if($options['nom'] === 'Commune')
            $builder->add('ville');
        
        if($options['nom'] === 'Zone')
            $builder->add('commune');

        if($options['nom'] === 'Quartier')
            $builder->add('zone');
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

     public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'nom' => null,
        ]);
    }
}
