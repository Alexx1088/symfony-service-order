<?php

namespace App\Form;

use App\Entity\Order;
use App\Service\ServiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', ChoiceType::class, [
                'choices' => [
                    'Оценка стоимости автомобиля' => 'Оценка стоимости автомобиля',
                    'Оценка стоимости квартиры' => 'Оценка стоимости квартиры',
                    'Оценка стоимости бизнеса' => 'Оценка стоимости бизнеса',
                ],
                'placeholder' => 'Выберите услугу',
            ])
            ->add('email', EmailType::class)
            ->add('price', HiddenType::class);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
