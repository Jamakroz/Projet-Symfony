<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: '_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/register', name: 'app_admin_register')]
    public function register(UserPasswordHasherInterface $passwordHasher,Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();

        $form = $this->createForm(ParticipantType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setActif($form->get('actif')->getData());
            $user->setAdministrateur($form->get('administrateur')->getData());
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur a bien été ajouté."
            );
            return $this->redirectToRoute('app_adminapp_admin_register');
        }
        return $this->render('admin/createUser.html.twig', [
            'form' => $form,
        ]);
    }
}
