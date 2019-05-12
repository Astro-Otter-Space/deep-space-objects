<?php


namespace App\Forms;

use App\Classes\Utils;
use App\Entity\Dso;
use App\Entity\Observation;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            'label' => '',
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => '',
        ]);

        $builder->add('observationDate', DateType::class, [
            'label' => '',
        ]);

        $builder->add('dsoList', TextType::class, [
            'label' => '',
        ]);

        $builder->add('instrument', TextType::class, [
            'label' => '',
        ]);

        $builder->add('diameter', IntegerType::class, [
            'label' => '',
        ]);

        $builder->add('mount', TextType::class, [
            'label' => '',
        ]);

        $builder->add('ocular', TextType::class, [
            'label' => '',
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

        /** @var UserInterface|null $user */
        $user = $this->security->getUser();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use($user) {
            /** @var FormInterface $form */
            $form = $event->getForm();

            if (is_null($user)) {
                $form->add('username', TextType::class, [

                ]);
            } else {
                $form->add('isPublic', ChoiceType::class, [
                    'choices' => ['yes', 'no']
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($user) {

            /** @var Observation $data */
            $data = $event->getData();

            $now = time();

            if (!is_null($user)) {
                $data->setUsername($user->getUsername());
            }

            $data->setCreatedAt($now);
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