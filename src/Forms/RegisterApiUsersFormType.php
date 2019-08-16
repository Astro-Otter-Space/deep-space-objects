<?php

namespace App\Forms;

use App\Entity\ApiUser;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

/**
 * Class RegisterApiUsersFormType
 *
 * @package App\Forms
 */
class RegisterApiUsersFormType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, [
            'label' => 'contact.form.email',
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
            'attr' => [
                'class' => 'Form__input'
            ]
        ]);

        $builder->add('rawPassword', PasswordType::class, [
            'label' => 'register.form.password',
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ],
            'attr' => [
                'class' => 'Form__input'
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
                    'bind' => 'btn_contact_submit',
                ]
            ],
            'invalid_message' => 'contact.constraint',
            'constraints' => [
                new IsTrue([
                    'message' => 'form.constraint.recaptcha',
                    'groups' => 'api_user'
                ])
            ]
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $formEvent) {
            /** @var ApiUser $data */
            $data = $formEvent->getData();

            $data->setIsActive(true);
        });
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ApiUser::class,
            'validation_groups' => 'api_user',
            'public_key' => '',
            'csrf_protection' => false
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return "register_api_user";
    }

}
