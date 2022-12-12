<?php

namespace App\Form\UserManagement;

use App\Entity\UserManagement\User;
use App\Validator\UserManagement\ConfirmEmail;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('civility', EntityType::class, [
            //     'label' => 'user.civility',
            //     'class' => 'App\Entity\LovManagement\Civility',                
            //     'choice_label' => 'title',
            //     'query_builder' => static function (EntityRepository $er) {
            //         return $er->createQueryBuilder('c')
            //             ->where('c.isValid = true')
            //             ->orderBy('c.title', 'ASC');
            //     },               
            //     'required' => true
            // ])
            ->add('firstname', TextType::class, [             
                'required' => true,
                'attr' => [
                    'placeholder' => 'user.prenom',
                ],
            ])
            ->add('lastname', TextType::class, [             
                'required' => true,
                'attr' => [
                    'placeholder' => 'user.nom',
                ],
            ])
            ->add('tel', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'user.phone',
                ],
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
            // ->add('password', RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     'first_options' => [
            //         'label' => 'user.password.first',
            //         'help' => 'user.password.help'
            //     ],
            //     'second_options' => ['label' => 'user.password.second'],

            // ])   
            // ->add('hasAcceptedCGU', CheckboxType::class, [
            //     'label' => 'register.agree_terms',
            //     'constraints' => [
            //         new IsTrue(['message' => 'register.agree_terms_constraints']),
            //     ],
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration'],
            'allow_extra_fields' => true,
        ]);
    }
}
