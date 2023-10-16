<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ChangePasswordType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

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

    #[Route('/register', name: 'app_register')]
    public function register(UserPasswordHasherInterface $passwordHasher,Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();

        $form = $this->createForm(RegistrationFormType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->addFlash(
                'success',
                "L'utilisateur a bien été ajouté."
            );
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setActif(true);
            $user->setAdministrateur(false);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('registration/register.html.twig', [
            'form' => $form,
            [
                new RememberMeBadge(),
            ]
        ]);

        }
//cree une route pour la page de mdifications de Mots de passe
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
            $oldPassword = $form->get('exPassword')->getData();
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
