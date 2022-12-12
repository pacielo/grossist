<?php

namespace App\Form\TransactionManagement;

use App\Entity\TransactionManagement\PurchaseItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            // ->add('taxRate')
            ->add('product')
            ->add('purchase')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PurchaseItem::class,
        ]);
    }
}
