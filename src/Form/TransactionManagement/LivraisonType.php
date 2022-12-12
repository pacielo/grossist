<?php

namespace App\Form\TransactionManagement;

use App\Entity\TransactionManagement\Livraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('departAdress')
            ->add('arriveAdress')
            ->add('departHeure')
            ->add('arriveHeure')
            ->add('prix')
            ->add('etat')
            ->add('transporteur')
            //->add('parent')
        ;

        // ->add('client', EntityType::class, [
        //         'class' => 'AppBundle:Clients',
        //         'placeholder' => '-- Seleziona --',
        //         'query_builder' => function (EntityRepository $er) {
        //             return $er->createQueryBuilder('u')
        //                 ->where('u.referer', ':uid')   <------ ERROR HERE
        //                 ->setParameter('uid', $this->getUser()->getId())
        //                 ->orderBy('u.name', 'ASC');
        //         },
        //         'choice_label' => 'name',
        //         'choice_value' => 'id',
        //         'label' => 'Cliente',
        //         'attr' => ['class' => 'form-control']
        //     ])
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livraison::class,
        ]);
    }
}
