<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find($this->getUser());
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        $errorOccurred = false; // Variable pour suivre si une erreur s'est produite

        // Vérifie si le form est envoyer et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $email_pattern = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
            $oldPassword = $form->get('exPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $newMail = $form->get('mail')->getData();

            // Vérifier si le mot de passe actuel est valide
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', "Mot de passe incorrect.");
                $errorOccurred = true;
            }
            if ($newPassword != null) {
                $participantRepository->upgradePassword($user, $passwordHasher->hashPassword($user, $newPassword));
            }
            if ($newMail != null && preg_match($email_pattern, $newMail)) {
                $participantRepository->updateUserEmail($user, $newMail);
            } else {
                $this->addFlash('error', "Email invalide.");
                $errorOccurred = true;
            }
            // Si aucune erreur n'est survenue, mettez à jour la base de données
            if (!$errorOccurred) {
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour');
                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form,
            'mail' => $this->getUser()->getUserIdentifier(),
        ]);
    }

    #[Route('/profile/{id}', name: 'app_profile_id')]
    public function profile(ParticipantRepository $participantRepository, int $id = null){
        if($id != null)
        {
            return $this->render('profile/userProfile.html.twig', [
                'userProfile' => $participantRepository->find($id),
            ]);
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/ModifPassword', name: 'app_ModifPassword')]
    public function changePassword(Request $request, ParticipantRepository $participantRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $user = $participantRepository->find($this->getUser());
        $form = $this->createForm(ChangePasswordType::class, $user);
        $errorOccurred = false;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            // Vérifier si le mot de passe actuel est valide
            $oldPassword = $form->get('oldPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', "Mot de passe incorrect.");
                $errorOccurred = true;
            }
            if ($newPassword != null) {
                $participantRepository->upgradePassword($user, $passwordHasher->hashPassword($user, $newPassword));
            }
            if (!$errorOccurred) {
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour');
                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('login/editMotsPasse.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}