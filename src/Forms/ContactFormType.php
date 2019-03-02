<?php

namespace App\Forms;

use App\Classes\Utils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactFormType
 * @package App\Forms
 */
class ContactFormType extends AbstractType
{

    const CLASS_LABEL = 'Form-label';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class, [
            'label' => 'contact.form.firstname',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'required' => true,
            'attr' => [
                'class' => 'Form-input',
                'placeholder' => 'contact.placeholder.firstname'
            ]
        ]);

        $builder->add('lastname', TextType::class, [
            'label' => 'contact.form.lastname',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'required' => true,
            'attr' => [
                'placeholder' => 'contact.placeholder.lastname'
            ]
        ]);

        $builder->add('email', RepeatedType::class, [
            'type' => EmailType::class,
            'invalid_message' => 'contact.constraint.email_note_same',
            'required' => true,
            'first_options' => [
                'label' => 'contact.form.email',
                'label_attr' => [
                    'class' => self::CLASS_LABEL
                ]
            ],
            'second_options' => [
                'label' => 'contact.form.confirm_email',
                'label_attr' => [
                    'class' => self::CLASS_LABEL
                ],
                'attr' => [
                    'placeholder' => 'contact.placeholder.email'
                ]
            ],
            'options' => [
                'attr' => [
                    'placeholder' => 'contact.placeholder.email',
                    'class' => ''
                ]
            ]
        ]);

        $builder->add('country', CountryType::class, [
            'label' => 'contact.form.country',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'attr' => [
                'class' => ''
            ],
//            'empty_value' => 'contact.country.placeholder',
            'preferred_choices' => [\Locale::getRegion(\Locale::getDefault())],
        ]);


        $builder->add('topic', ChoiceType::class, [
            'choices' => array_flip(Utils::listTopicsContact()),
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'attr' => [
                'class' => ''
            ],
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ]
        ]);

        $builder->add('message', TextareaType::class, [
            'label' => 'contact.form.message',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ]
        ]);

        $builder->add('pot2Miel', TextType::class, [
            'required' => false,
            'attr' => [
                'aria-hidden' => "true",
                'style' => 'display: none;'
            ]
        ]);
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'validation_groups' => null,
        ]);
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'contact';
    }

}