<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surname')
            ->add('email')
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => Student::GENDER_MALE,
                    'Female' => Student::GENDER_FEMALE,
                ]
            ])
            ->add('group')
            ->add('rating', IntegerType::class, [
                'attr' => [
                    'min' => Student::RATING_MIN,
                    'max' => Student::RATING_MAX,
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'label' => 'Отправить',
            ])
        ;
    }

    public function getName()
    {
        return 'student';
    }
}