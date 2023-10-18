<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Enum\Etat;
use App\Form\AnnulerSortieType;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/', name: 'app')]
class SortiesController extends AbstractController
{
    #[Route('/', name: '_home')]
    #[Route('/sortie/{id}', name: '_sortie_view')]
    public function index(SortieRepository $sortieRepository, int $id = null): Response
    {
        if($id != null)
        {
            $etatConstants = (new ReflectionClass(Etat::class))->getConstants();
            return $this->render('home/voirSortie.html.twig', [
                'Etat' => $etatConstants,
                'sortie' => $sortieRepository->find($id),
            ]);
        }
        else{
            $etatConstants = (new ReflectionClass(Etat::class))->getConstants();
            return $this->render('home/index.html.twig', [
                'Etat' => $etatConstants,
                'sortieList' => $sortieRepository->findAll(),
            ]);
        }
    }

    #[Route('/ajouter', name: '_ajouter')]
    #[Route('/modifier/{id}', name: '_modifier')]
    public function editer(ValidatorInterface $validator,Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null): Response
    {
        $etatConstants = (new ReflectionClass(Etat::class))->getConstants();
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
                    //TODO: ETAT auto géré dans le back, PAS DE MODIF MANUEL
                    ->setEtat(Etat::CREATION());
            }
            else{
                $sortie->setOrganisateur($this->getUser());
                $sortie->setEtat(Etat::CREATION());
                $sortie->setSite($this->getUser()->getSite());
                $sortie->setLieu($form->get('lieu')->getData());
              //  dd($sortie);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        $errors = $validator->validate($sortie);
        return $this->render('home/editerSortie.html.twig', [
            'form' => $form,
            'sortie' => $sortie,
            'errors'=>$errors,
            'etats'=> $etatConstants
        ]);
    }

    #[Route('/annuler/{id}', name: '_annuler')]
    public function annuler(Request $request,EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null): Response
    {
        $sortie = new Sortie();
        // ...

        $form = $this->createForm(AnnulerSortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $sortie = $form->getData();
            $sortie->setEtat(Etat::CANCELED());
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La sortie à bien été annulée !'
            );
            return $this->redirectToRoute('app_sortie_view');
        }
        return $this->render('home/annulerSortie.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/inscription/{id}', name: '_inscription')]
    public function inscription(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository,int $id = null ): Response
    {
        if($id != null)
        {
            $sortie = $sortieRepository->find($id);
            if($sortie->getNbInscriptionsMax() > count($sortie->getParticipantsInscrits()) && $sortie->getDateLimiteInscription() >= date("Y-m-d"))
            {
                $sortie->addParticipantsInscrit($participantRepository->findOneBy(['id'=>$this->getUser()]));
            }
            else{
                $this->addFlash(
                    'error',
                    "Il n'y a plus de place disponible ou la date limite d'inscription est dépassée."
                );
                return $this->redirectToRoute('app_home');
            }

            if($sortie->getNbInscriptionsMax() == count($sortie->getParticipantsInscrits()) || $sortie->getDateLimiteInscription() < date("Y-m-d"))
            {
                $sortie->setEtat(Etat::CLOSED());
            }

            $this->addFlash(
                'success',
                'Vous êtes bien inscrit à la sortie !'
            );
            $entityManager->persist($sortie);
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_home');
    }

    #[Route('/get-lieu', name: '_get_lieu')]
    public function getLieu(Request $request, LieuRepository $repository): JsonResponse
    {
        $cityId = $request->query->get('cityId');
        $locations = $repository->findBy(['ville' => $cityId]);

        $locationsArray = [];

        foreach ($locations as $location) {
            $locationsArray[] = [
                'id' => $location->getId(),
                'name' => $location->getNom(),
            ];
        }
        return new JsonResponse($locationsArray);
    }

    #[Route('/desister/{id}', name: '_desister')]
    public function desister(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository,SortieRepository $sortieRepository, int $id = null): Response
    {

        $user =$participantRepository->find($this->getUser());
        $user->removeSorty($sortieRepository->find($id));
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Vous vous êtes bien désisté !'
        );

        return $this->redirectToRoute('app_home');
    }

    #[Route('/publier/{id}', name: '_publier')]
    public function publier(EntityManagerInterface $entityManager,SortieRepository $sr, int $id = null)
    {
        if($id != null)
        {
            $sortie = $sr->find($id);
            if($this->getUser() === $sortie->getOrganisateur())
            {
                $sortie->setEtat(Etat::OPEN());
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_sortie_view', ['id'=>$id]);
    }


}