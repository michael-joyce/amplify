<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Podcast;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Podcast form.
 */
class PodcastType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('title', TextType::class, [
            'label' => 'Title',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('alternativeTitle', TextType::class, [
            'label' => 'Alternative Title',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('explicit', ChoiceType::class, [
            'label' => 'Explicit',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => true,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('copyright', TextareaType::class, [
            'label' => 'Copyright',
            'required' => true,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('category', TextType::class, [
            'label' => 'Category',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('website', TextareaType::class, [
            'label' => 'Website',
            'required' => true,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('rss', TextType::class, [
            'label' => 'Rss',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('tags', CollectionType::class, [
            'label' => 'Tags',
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-simple',
                'help_block' => 'A URL link to the specificed publication',
            ],
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Podcast::class,
        ]);
    }
}