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
    public function index(Request $request, SiteRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->render('site/index.html.twig', [
                'sites' => $repository->findAll(),
            ]);
        }
        return $this->render('site/index.html.twig', [
            'form' => $form,
            'sites' => $repository->findAll(),
        ]);
    }
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/site/modifier', name: 'app_site_modifier')]
    public function modifier(Request $request, Site $site, SiteRepository $repository, EntityManagerInterface $entityManager): Response
    {
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
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/site/supprimer/', name: 'app_site_supprimer')]
    public function supprimer(Request $request, Site $site, SiteRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($site);
        $entityManager->flush();
        return $this->redirectToRoute('app_site');
    }
}
