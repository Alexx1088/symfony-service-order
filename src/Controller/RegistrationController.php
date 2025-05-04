<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormTypeForm;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController


{
     public function __construct(private readonly UserService $userService) {}

     #[Route('/register', name: 'app_register')]
     public function register(Request $request): Response
     {
         $user = new User();
         $form = $this->createForm(RegistrationFormTypeForm::class, $user);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             $this->userService->register($user, $form->get('plainPassword')->getData());

             return $this->redirectToRoute('app_login');
         }

         return $this->render('registration/index.html.twig', [
             'registrationForm' => $form->createView(),
         ]);
     }
}
