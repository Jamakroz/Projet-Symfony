<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Enum\Etat;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app')]
class SortiesController extends AbstractController
{
    #[Route('/', name: '_home')]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sortieList' => $sortieRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: '_ajouter')]
    #[Route('/modifier/{id}', name: '_modifier')]
    public function editer(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, int $id = null): Response
    {
        if ($id == null) {
            $sortie = new Sortie();
        } else {
            $sortie = $sortieRepository->find($id);
        }

        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);

        //TODO: faire en sorte que la photo soit un url
        //TODO: ajouter photo de sortie
        if ($form->isSubmitted() && $form->isValid()) {
            if ($sortie->getId() != null) {
                $sortie
                    //->setNom($form->get('nom')->getData())
                    //->setDateHeureDebut($form->get('dateHeureDebut')->getData())
                    //->setDuree($form->get('duree')->getData())
                    //->setDateLimiteInscription($form->get('dateLimiteInscription')->getData())
                    //->setNbInscriptionsMax($form->get('nbInscriptionsMax')->getData())
                    //->setInfosSortie($form->get('infosSortie')->getData())
                    //TODO: ETAT auto géré dans le back, PAS DE MODIF MANUEL
                    ->setEtat(Etat::CREATION());
                    //->setLieu($form->get('lieu')->getData())
                    //->setSite($form->get('Site')->getData())
                    //TODO: ORGANISATEUR == l'id de l'utilisateur connecté lors de la création
                    //TODO: pouvoir modif ORGANISATEUR si utilisateur connecté == admin
                    //->setOrganisateur($this->getUser());
            }
            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat(Etat::CREATION());
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/editerSortie.html.twig', [
            'form' => $form,
            'sortie' => $sortie
        ]);
    }

    #[Route('/annuler/{id}', name: '_annuler')]
    public function annuler(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null): Response
    {
        $entityManager->remove($sortieRepository->find($id));
        $entityManager->flush();

        $this->addFlash(
            'success',
            'La sortie à bien été annuler !'
        );

        return $this->redirectToRoute('app_home');
    }

    #[Route('/inscription/{id}', name: '_inscription')]
    public function inscription(EntityManagerInterface $entityManager, ): Response
    {
        $sortie =

        $this->addFlash(
            'success',
            'Vous êtes bien inscrit à la sortie !'
        );

        return $this->redirectToRoute('app_home');
    }
}