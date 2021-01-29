<?php

namespace App\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;

/**
 * Class SearchFormType
 * @package App\Forms
 */
class SearchFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('terms', TextType::class, [
            'label' => '',
            'attr' => [
                'placeholder' => 'home.search.placeholder',
                'autocomplete' => 'off'
            ]
        ]);

        $builder->add('pot2Miel', TextType::class, [
            'required' => false,
            'attr' => [
                'aria-hidden' => 'true',
                'style' => 'display:none;'
            ],
            'constraints' => [
                new Blank(['message' => 'form.constraint.empty'])
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix(): ?string
    {
        return 'search';
    }
}
