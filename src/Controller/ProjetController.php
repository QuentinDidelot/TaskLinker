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
    /**
     * Permet d'accéder à tous les projets à condition d'y être assigné
     *
     * @Route("/projets", name="app_projets")
     *
     * @return Response Returns a Response object with the rendered template
     */
    #[Route('/projets', name: 'app_projets')]
    public function projets(): Response
    {
        $user = $this->getUser();
        $projets = $this->projetRepository->findAccessibleByUser($user);

        return $this->render('projet/liste.html.twig', [
            'projets' => $projets,
        ]);
    }

    /**
     * Création d'un nouveau projet
     * 
     * @Route("/projets/ajouter", name="app_projet_add")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request The request object containing the form data.
     * @return Response Returns a Response object with the rendered template.
     */
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

    /**
     * Accède à la page d'un projet en fonction de son identifiant
     *
     * @Route("/projets/{id}", name="app_projet")
     * @IsGranted("acces_projet", "id")
     *
     * @param int $id The unique identifier of the project to display.
     * @return Response Returns a Response object with the rendered project template.
     */
    #[Route('/projets/{id}', name: 'app_projet')]
    #[IsGranted('acces_projet', 'id')]
    public function projet(int $id): Response
    {  
        $projet = $this->projetRepository->find($id);

        $statuts = $this->statutRepository->findAll();

        return $this->render('projet/projet.html.twig', [
            'projet' => $projet,
            'statuts' => $statuts,
        ]);
    }

    /**
     * Supprime un projet en fonction de son identifiant
     *
     * @Route("/projets/{id}/supprimer", name="app_projet_delete")
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @return Response
     **/
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


    /**
     * Modifie un projet en fonction de son identifiant
     *
     * @Route("/projets/{id}/editer", name="app_projet_edit")
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param int $id
     * @return Response
     **/
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
