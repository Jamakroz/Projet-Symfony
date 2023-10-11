<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserPasswordHasherInterface $passwordHasher,Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find($this->getUser());
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('ex-password')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $newMail = $form->get('mail')->getData();
            if($passwordHasher->isPasswordValid($user,$oldPassword))
            {
                $participantRepository->upgradePassword($user,$passwordHasher->hashPassword($user,$newPassword));
                $participantRepository->updateUserEmail($user,$newMail);
            }
            // update the database
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/index.html.twig', [
            'form'=>$form,
        ]);
    }
}
