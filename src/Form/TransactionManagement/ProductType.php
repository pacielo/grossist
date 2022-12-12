<?php

namespace App\Form\TransactionManagement;

use App\Entity\TransactionManagement\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled')
            ->add('image')
            ->add('features')
            ->add('price')
            ->add('name')
            ->add('description')
            ->add('categories')
            ->add('distributeur')
            ->add('fabricant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
