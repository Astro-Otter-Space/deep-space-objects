<?php


namespace App\Forms;

use App\Classes\Utils;
use App\Entity\Observation;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ObservationFormType
 *
 * @package App\Forms
 */
class ObservationFormType extends AbstractType
{
    private $security;

    /**
     * ObservationFormType constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**  */
        $builder->add('name', TextType::class, [
            'label' => 'observation.name.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.name.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'observation.description.label',
            'attr' => [
                'placeholder' => 'observation.description.placeholder',
                'class' => 'Form__textarea',
                'rows' => 6
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('observationDate', DateType::class, [
            'label' => 'observation.observationDate.label',
            'widget' => 'text',
            'html5' => false,
            'attr' => [
                'placeholder' => 'observation.description.placeholder',
                'class' => 'Form__input js-datepicker',
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);
//
//        $builder->add('dsoList', TextType::class, [
//            'label' => 'observation.dsoList.label',
//            'attr' => [
//                'placeholder' => 'observation.dsoList.placeholder'
//            ]
//        ]);

        $builder->add('locationLabel', TextType::class, [
            'label' => 'observation.locationLabel.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.locationLabel.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('instrument', TextType::class, [
            'label' => 'observation.instrument.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.instrument.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('diameter', IntegerType::class, [
            'label' => 'observation.diameter.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.diameter.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('focal', IntegerType::class, [
            'label' => 'observation.focal.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.focal.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);


        $builder->add('mount', TextType::class, [
            'label' => 'observation.mount.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.mount.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('ocular', TextType::class, [
            'label' => 'observation.ocular.label',
            'mapped' => false,
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'observation.ocular.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
        ]);

        $builder->add('location', HiddenType::class, []);

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

        /** @var UserInterface|null $user */
        $user = $this->security->getUser();

        // Listener before sdet data
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use($user) {
            /** @var FormInterface $form */
            $form = $event->getForm();

            if (is_null($user)) {
                $form->add('username', TextType::class, [
                    'required' => true,
                    'label' => 'observation.username.label',
                    'attr' => [
                        'class' => 'Form__input',
                        'placeholder' => 'observation.username.placeholder'
                    ],
                    'label_attr' => [
                        'class' => ContactFormType::CLASS_LABEL
                    ],
                ]);

            } else {
                $form->add('isPublic', ChoiceType::class, [
                    'choices' => array_flip(['yes', 'no']),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                ]);
            }
        });


        // Listener after submit
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($user) {
            /** @var Observation $data */
            $data = $event->getData();

            /** @var \DateTime $now */
            $now = new \DateTime();

            if (!is_null($user)) {
                $data->setUsername($user->getUsername());
            } else {
                $data->setIsPublic(true);
            }

            $data->setCreatedAt($now->format(Utils::FORMAT_DATE_ES));
            if (!is_null($data->getObservationDate())) {
                $data->setObservationDate($data->getObservationDate()->format(Utils::FORMAT_DATE_ES));
            }
        });
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Observation::class,
            'csrf_protection' => false,
            'validation_groups' => 'add_observation'
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'add_observation';
    }


}