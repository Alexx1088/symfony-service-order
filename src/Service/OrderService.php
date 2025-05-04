<?php

namespace App\Service;
use App\Entity\Order;
use App\Repository\OrderRepository;

readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {}

    public function createOrder(Order $order): void
    {
        $priceList = ServiceList::get();
        $price = $priceList[$order->getService()] ?? 0;
        $order->setPrice($price);

        $this->orderRepository->save($order);
    }
}