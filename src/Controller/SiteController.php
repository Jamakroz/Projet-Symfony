<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/site', name: 'app_site')]
    public function index(SiteRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();

            // A redirection should happen after form submission to prevent duplicate form submission on page refresh.
            return $this->redirectToRoute('app_site');
        }

        $villes = $repository->findAll();

        return $this->render('site/index.html.twig', [
            'form' => $form->createView(),  // Create a view of the form to render it
            'sites' => $villes,
        ]);
    }

    #[Route('/site/modifier/{id}', name: 'app_site_modifier')]
    public function modifier(Request $request, SiteRepository $siteRepository, EntityManagerInterface $entityManager, int $id = null): Response
    {
        $site = $siteRepository->find($id);
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->redirectToRoute('app_site');
        }
        return $this->render('site/modifierSite.html.twig', [
            'form' => $form,
            'site' => $site,
        ]);
    }

    #[Route('/site/supprimer/{id}', name: 'app_site_supprimer')]
    public function supprimer(SiteRepository $siteRepository, EntityManagerInterface $entityManager, $id): Response
    {
        // on récupère le site à supprimer
        $site = $siteRepository->find($id);

        if (!$site) {
            throw $this->createNotFoundException('Site non trouvé');
        }

        $entityManager->remove($site);
        $entityManager->flush();

        return $this->redirectToRoute('app_site'); // Redirigez vers la page de liste des sites après la suppression
    }
}
