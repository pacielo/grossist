<?php

namespace App\Form\UserManagement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MailDomaineSupsType extends AbstractType
{

    public function getParent()
    {
        return TextType::class;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
