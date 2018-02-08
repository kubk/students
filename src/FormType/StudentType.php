<?php

declare(strict_types=1);

namespace App\FormType;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Имя',
            ])
            ->add('surname', null, [
                'label' => 'Фамилия',
            ])
            ->add('email')
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Мужской' => Student::GENDER_MALE,
                    'Женский' => Student::GENDER_FEMALE,
                ],
                'label' => 'Пол',
            ])
            ->add('group', null, [
                'label' => 'Группа',
            ])
            ->add('rating', IntegerType::class, [
                'attr' => [
                    'min' => Student::RATING_MIN,
                    'max' => Student::RATING_MAX,
                ],
                'label' => 'Рейтинг',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'label' => 'Отправить',
            ]);
    }

    public function getName()
    {
        return 'student';
    }
}
