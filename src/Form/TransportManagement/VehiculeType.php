<?php

namespace App\Form\TransportManagement;

use App\Entity\TransportManagement\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('matricule')
            ->add('description')
            ->add('charge')
            ->add('longueur')
            ->add('largeur')
            ->add('hauteur')
            ->add('type')
            ->add('marque')
            ->add('proprietaire')
            ->add('ville')
            ->add('quartier')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
