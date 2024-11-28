<?php

namespace App\Form;

use App\Entity\category;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            // ->add('publishedAt', null, [
            //     'widget' => 'single_text',
            // ])
			->add('picture', FileType::class, [
				'label' => 'Upload a picture',
				'mapped' => false, // The file is not part of the Post entity, it is handled separately
			'required' => false, 
			])
           // ->add('creator', EntityType::class, [
           //      'class' => User::class,
           //      'choice_label' => 'id',
           //  ])
            ->add('category', EntityType::class, [
                'class' => category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
