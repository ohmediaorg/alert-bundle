<?php

namespace OHMedia\AlertBundle\Form;

use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\TimezoneBundle\Form\Type\DateTimeType;
use OHMedia\WysiwygBundle\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $alert = $options['data'];

        $builder->add('name', TextType::class, [
            'help' => 'For internal reference only.',
        ]);

        $builder->add('starts_at', DateTimeType::class, [
            'label' => 'Start',
            'help' => 'This value must be populated and in the past for the Alert to be considered active.',
            'widget' => 'single_text',
            'required' => false,
        ]);

        $builder->add('ends_at', DateTimeType::class, [
            'label' => 'End',
            'help' => 'If both Start and End are populated, the Alert will be active until this time is reached.',
            'widget' => 'single_text',
            'required' => false,
        ]);

        $builder->add('dismissible', ChoiceType::class, [
            'label' => 'Can this alert be dismissed?',
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => true,
            'row_attr' => [
                'class' => 'fieldset-nostyle mb-3',
            ],
        ]);

        $builder->add('dismissible_days', IntegerType::class, [
            'label' => 'After how many days should the alert be shown again?',
            'attr' => [
                'min' => 1,
                'step' => 1,
                'style' => 'width:auto',
            ],
        ]);

        $builder->add('content', WysiwygType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
