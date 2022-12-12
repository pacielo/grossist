<?php

namespace App\Form\UserManagement;

use App\Entity\UserManagement\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Validator\UserManagement\ConfirmEmail;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OtherUserType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        parent::buildForm($builder, $options);
        $builder
           // ->remove('second_options') 
            ->add('listOfRoles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'required' =>true,
                'choices'  => [
                            'Agriculteur' => 'ROLE_AGRI',
                            'Admin'     => 'ROLE_ADMIN',
                            'Vendeur' => 'ROLE_SUPPLY',
                         ],
            ])
             ->add('email',  EmailType::class, [
                'attr' => [
                    'placeholder' => 'user.email',
                ],
                'label' => 'user.email',
            ]) 
               ->add('password', PasswordType::class, [
                'required' =>false ,
                'attr' => [
                    'placeholder' => 'mot de passe',
                ],
            ])  
             ->add('societe', TextType::class, [             
                'required' => true,
                'attr' => [
                    'placeholder' => 'user.societe',
                ],
            ]) 
            // ->add('vehicules') 
             ->add('typeCommerce', EntityType::class, [
                'label' => 'user.typeCommerce',
                'multiple' => true,
                'class' => 'App\Entity\LovManagement\TypeCommerce',                
                'choice_label' => 'title',
                'required' => false,
                // 'query_builder' => static function (EntityRepository $er) {
                //     return $er->createQueryBuilder('t')
                //         ->where('t.isValid = true')
                //         ->orderBy('t.title', 'ASC');
                // }, 
            ]) 
             ->add('genreCommerce', EntityType::class, [
                'label' => 'user.genreCommerce',
                'multiple' => true,
                'class' => 'App\Entity\LovManagement\GenreCommerce',                
                'choice_label' => 'title',
                'required' => false,
            ]) 
             ->add('quartier', EntityType::class, [
                'label' => 'user.quartier',
                'multiple' => true,
                'class' => 'App\Entity\LovManagement\Quartier',                
                'choice_label' => 'title',
                'required' => false,
            ]) 
             ->add('commune', EntityType::class, [
                'label' => 'user.commune',
                'multiple' => true,
                'class' => 'App\Entity\LovManagement\Commune',                
                'choice_label' => 'title',
                'required' => false,
            ]) 
             ->add('ville', EntityType::class, [
                'label' => 'user.ville',
                'multiple' => true,
                'class' => 'App\Entity\LovManagement\Ville',                
                'choice_label' => 'title',
                'required' => false,
            ]) 
            // ->add('email',  RepeatedType::class, [
            //     'type' => EmailType::class,
            //     'first_options' => [
            //         'label' => 'user.email',
            //         'help' => 'user.email_help',
            //         'constraints' => [
            //             new ConfirmEmail(),
            //         ],
            //     ],
            //     'second_options' => ['label' => 'user.email_second']
            // ])             
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
