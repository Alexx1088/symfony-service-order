<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderTypeForm;
use App\Service\OrderService;
use App\Service\ServiceList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

 class OrderController extends AbstractController
 {
     public function __construct(private readonly OrderService $orderService)
     {}

     #[Route('/order', name: 'app_order')]
     public function order(Request $request): Response
     {
         $form = $this->createForm(OrderTypeForm::class, new Order());
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

             $order = $form->getData();
             $this->orderService->createOrder($order);

             return $this->redirectToRoute('order_success');
         }

         return $this->render('order/index.html.twig', [
             'form' => $form->createView(),
             'services' => ServiceList::get(),
         ]);
     }

     #[Route('/order/success', name: 'order_success')]
     public function success(): Response
     {
         return $this->render('order/success.html.twig');
     }

 }