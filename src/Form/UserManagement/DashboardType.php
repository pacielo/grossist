<?php

namespace App\Form\UserManagement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DashboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $datemax =  new \DateTime("now");
        $datemin = new \DateTime("now");
        $datemin = $datemin->modify('-1 week');

        $builder
        ->add('datemin', DateType::class, [
                'required' => true,
                'html5' => false,
                'data' => $datemin,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'jj/mm/aaaa',
                ],
                'format' => 'dd/MM/yyyy',   
        ])
       ->add('datemax', DateType::class, [
                'required' => true,
                'html5' => false,
                'data' => $datemax,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'jj/mm/aaaa',
                ],
                'format' => 'dd/MM/yyyy', 
        ])
        ;
    }

     /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
