<?php

declare(strict_types=1);

namespace App\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LogOutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-link',
                ],
                'label' => 'Выйти',
            ]);
    }

    public function getName()
    {
        return 'logout';
    }
}
