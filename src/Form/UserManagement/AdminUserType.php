<?php

namespace App\Form\UserManagement;

use App\Entity\UserManagement\User;
use Doctrine\Common\Annotations\Annotation\Required;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminUserType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('hasAcceptedCGU') 
            ->remove('password') 
            ->add('listOfRoles', ChoiceType::class, [
                'multiple' => true,
                //'expanded' => true,
                'required' =>true,
                'choices'  => [
                            'Transporteur'   => 'ROLE_TRANS',
                            'gerant' => 'ROLE_GERANT'
                         ],
            ])           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'User']
        ]);
    }
}
