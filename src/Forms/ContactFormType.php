<?php

namespace App\Forms;

use App\Classes\Utils;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContactFormType
 * @package App\Forms
 */
class ContactFormType extends AbstractType
{
    /** @var TranslatorInterface  */
    private $translatorInterface;

    const CLASS_LABEL = 'Form__label';

    /**
     * ContactFormType constructor.
     *
     * @param TranslatorInterface $translatorInterface
     */
    public function __construct(TranslatorInterface $translatorInterface)
    {
        $this->translatorInterface = $translatorInterface;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastname', TextType::class, [
            'label' => 'contact.form.lastname',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'required' => true,
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'contact.placeholder.lastname'
            ]
        ]);

        $builder->add('firstname', TextType::class, [
            'label' => 'contact.form.firstname',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'required' => true,
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'contact.placeholder.firstname'
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
                    'placeholder' => 'contact.placeholder.email',
                    'class' => 'Form__input'
                ]
            ],
            'options' => [
                'attr' => [
                    'placeholder' => 'contact.placeholder.email',
                    'class' => 'Form__input',
                ]
            ]
        ]);

        $builder->add('country', CountryType::class, [
            'label' => 'contact.form.country',
            'empty_data' => 'Coucou',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'attr' => [
                'class' => 'Form__select',
                'placeholder' => 'contact.placeholder.country',
            ],
            'preferred_choices' => 'FR' // [\Locale::getRegion(\Locale::getDefault())],
        ]);


        $translate = $this->translatorInterface;
        $builder->add('topic', ChoiceType::class, [
            'choices' => array_flip(Utils::listTopicsContact()),
            'label' => 'contact.form.topic',
//            'choice_label' => function($value, $key) use($translate){
//                return $translate->trans($key);
//            },
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'attr' => [
                'class' => 'Form__radio'
            ],
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ]
        ]);

        $builder->add('message', TextareaType::class, [
            'label' => 'contact.form.message',
            'label_attr' => [
                'class' => self::CLASS_LABEL
            ],
            'attr' => [
                'class' => 'Form__textarea',
                'rows' => 6
            ]
        ]);

        $builder->add('pot2Miel', TextType::class, [
            'required' => false,
            'attr' => [
                'aria-hidden' => "true",
                'style' => 'display: none;'
            ]
        ]);

        $builder->add('recaptcha', EWZRecaptchaType::class, [
            'mapped' => false,
            'error_bubbling' => false,
            'attr' => [
                'options' => [
                    'theme' => 'light',
                    'type'  => 'image',
                    'size' => 'invisible',              // set size to invisible
                    'defer' => true,
                    'async' => true,
                    //'callback' => 'onReCaptchaSuccess', // callback will be set by default if not defined (along with JS function that validate the form on success)
                    'bind' => 'btn_contact_submit',
                ]
            ],
            'invalid_message' => 'contact.constraint',
            'constraints' => [
                new IsTrue([
                    'message' => 'form.constraint.recaptcha'
                ])
            ]
        ]);
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'validation_groups' => null,
        ]);
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'contactus';
    }

}
