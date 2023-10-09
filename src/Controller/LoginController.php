<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/registration', name: 'app_registration')]
    public function register(UserPasswordHasherInterface $passwordHasher,Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->addFlash(
                'success',
                "L'utilisateur a bien été ajouté."
            );
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_main');
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form,
        ]);
    }
}
