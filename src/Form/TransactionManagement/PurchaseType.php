<?php

namespace App\Form\TransactionManagement;

use App\Entity\TransactionManagement\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
           //  ->add('deliveryDate')
           //  ->add('createdAt')
           // // ->add('shipping')
           //  ->add('deliveryHour')
           //  ->add('billingAddress')
           //  ->add('buyer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
