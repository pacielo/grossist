<?php

namespace App\Form\DocumentManagement;

use App\Entity\DocumentManagement\Document;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder		
			->add('title')
			->add('version')			
			->add('folder', ChoiceType::class, [
					'choices'  => [
						'relooke1' => 1,
						'relooke2' => 2,
					]])
			->add('file', VichFileType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_uri' => false,
                'download_label' => false
            ])      
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
