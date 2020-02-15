<?php


namespace App\Forms;

use App\Classes\Utils;
use App\Entity\ES\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ObservingEventFormType
 *
 * @package App\Forms
 */
class ObservingEventFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Main infos
        $builder->add('name', TextType::class, [
            'label' => 'event.name.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.name.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('eventDate', DateType::class, [
            'label' => 'event.eventDate.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.eventDate.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'event.description.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.description.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        // Place
        $builder->add('locationLabel', TextType::class, [
            'label' => 'event.locationLabel.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.locationLabel.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('location', HiddenType::class, [
        ]);

        // Others infos
        $builder->add('tarif', NumberType::class, [
            'label' => 'event.tarif.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.tarif.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('public', ChoiceType::class, [
            'label' => 'event.public.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.public.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('numberEntrant', NumberType::class, [
            'label' => 'event.numberEntrant.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.numberEntrant.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        // Organiser
        $builder->add('organiserName', TextType::class, [
            'label' => 'event.organiserName.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.organiserName.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('organiserMail', EmailType::class, [
            'label' => 'event.organiserMail.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.organiserMail.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('organiserTel', TelType::class, [
            'label' => 'event.organiserTel.label',
            'attr' => [
                'class' => 'Form__input',
                'placeholder' => 'event.organiserTel.placeholder'
            ],
            'label_attr' => [
                'class' => ContactFormType::CLASS_LABEL
            ]
        ]);

        $builder->add('pot2Miel', TextType::class, [
            'required' => false,
            'attr' => [
                'aria-hidden' => "true",
                'style' => 'display: none;'
            ]
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Event $data */
            $data = $event->getData();

            /** @var \DateTimeInterface $now */
            $now = new \DateTime();

            $data->setCreatedAt($now->format(Utils::FORMAT_DATE_ES), true);
            $data->setEventDate($data->getEventDate()->format(Utils::FORMAT_DATE_ES), true);

            // URL
            $eventUrl = Utils::camelCaseUrlTransform(implode(trim(Event::URL_CONCAT_GLUE), [$data->getName(), $data->getEventDate()->format('Y-m-d')]));

            // Add location
            $geoShape = [
                "type" => ucfirst("Point"),
                "coordinates" => json_decode($data->getLocation())
            ];
            $data->setLocation($geoShape);

            // Add URL
            $data->setFullUrl($eventUrl);
            // Add id
            $data->setId(md5($eventUrl));

        });
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Event::class,
            'validation_groups' => 'add_event'
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'observing_event';
    }

}
