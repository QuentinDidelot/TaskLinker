<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjetRepository;
use App\Repository\StatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Projet;
use App\Form\ProjetType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjetController extends AbstractController
{
    public function __construct(
        private ProjetRepository $projetRepository,
        private StatutRepository $statutRepository,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    #[Route('/projets', name: 'app_projets')]
    public function projets(): Response
    {
        $user = $this->getUser();
        $projets = $this->projetRepository->findAccessibleByUser($user);

        return $this->render('projet/liste.html.twig', [
            'projets' => $projets,
        ]);
    }

    #[Route('/projets/ajouter', name: 'app_projet_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function ajouterProjet(Request $request): Response
    {  
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $projet->setArchive(false);
            $this->entityManager->persist($projet);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_projet', ['id' => $projet->getId()]);
        }

        return $this->render('projet/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projets/{id}', name: 'app_projet')]
    #[IsGranted('acces_projet', 'id')]
    public function projet(int $id): Response
    {  
        $projet = $this->projetRepository->find($id);

        // if (!$projet || $projet->isArchive() || !$this->authorizationChecker->isGranted('acces_projet', $projet)) {
        //     return $this->render('erreur/acces-refuse.html.twig');
        // }

        $statuts = $this->statutRepository->findAll();

        return $this->render('projet/projet.html.twig', [
            'projet' => $projet,
            'statuts' => $statuts,
        ]);
    }

    #[Route('/projets/{id}/archiver', name: 'app_projet_archive')]
    #[IsGranted('ROLE_ADMIN')]
    public function archiverProjet(int $id): Response
    {  
        $projet = $this->projetRepository->find($id);

        if (!$projet || $projet->isArchive() || !$this->authorizationChecker->isGranted('acces_projet', $projet)) {
            return $this->render('acces-refuse.html.twig');
        }

        $projet->setArchive(true);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('app_projets');
    }

    #[Route('/projets/{id}/editer', name: 'app_projet_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editerProjet(int $id, Request $request): Response
    {  
        $projet = $this->projetRepository->find($id);

        if (!$projet || $projet->isArchive() || !$this->authorizationChecker->isGranted('acces_projet', $projet)) {
            return $this->render('acces-refuse.html.twig');
        }

        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projet->setArchive(false);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_projet', ['id' => $projet->getId()]);
        }

        return $this->render('projet/editer.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }
}
