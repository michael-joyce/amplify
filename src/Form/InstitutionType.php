<?php

namespace App\Form;

use App\Entity\Institution;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Institution form.
 */
class InstitutionType extends AbstractType {

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                                    $builder->add('province', TextType::class, array(
                    'label' => 'Province',
                    'required' => true,
                    'attr' => array(
                        'help_block' => '',
                    ),
                ));
                                        $builder->add('name', TextType::class, array(
                    'label' => 'Name',
                    'required' => true,
                    'attr' => array(
                        'help_block' => '',
                    ),
                ));
            
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Institution::class
        ));
    }

}