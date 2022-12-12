<?php

namespace App\Form\UserManagement;

use App\Entity\UserManagement\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Validator\UserManagement\ConfirmEmail;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OtherUserShowType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        parent::buildForm($builder, $options);
        $builder
            ->remove('userName')
            ->remove('rppsNumber')
            ->remove('password')
            ->remove('hasAcceptedCGU')
            ->add('listOfRoles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'required' =>true,
                'choices'  => [
                            'Agriculteur' => 'ROLE_AGRI',
                            'Admin'     => 'ROLE_ADMIN'
                         ],
            ])
            ->add('email')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'OtherUser']
        ]);

    }
}
