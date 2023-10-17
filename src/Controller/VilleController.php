<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/ville', name: 'app_ville')]
class VilleController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(VilleRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            // A redirection should happen after form submission to prevent duplicate form submission on page refresh.
            return $this->redirectToRoute('app_ville_index');
        }

        $villes = $repository->findAll();

        return $this->render('ville/index.html.twig', [
            'form' => $form->createView(),  // Create a view of the form to render it
            'villes' => $villes,
        ]);
    }

    #[Route('/editer', name: '_editer')]
    public function editer(VilleRepository $repository): Response
    {
        return $this->render('ville/index.html.twig', [
            'villes' => $repository->findAll(),
        ]);
    }
}
