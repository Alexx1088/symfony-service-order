<?php

namespace App\Service;

class ServiceList
{
    public static function get():array
    {
        return [
            'Оценка стоимости автомобиля' => 500,
            'Оценка стоимости квартиры' => 1500,
            'Оценка стоимости бизнеса' => 3000,
        ];
    }
}