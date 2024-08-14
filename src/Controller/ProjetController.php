<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetRepository;
use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\ProjetType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;

#[Route('/projet', name: 'app_project')]

class ProjetController extends AbstractController {

    private $projetRepository;
    private $entityManager;

    public function __construct(ProjetRepository $projetRepository, EntityManagerInterface $entityManager) {
        $this->projetRepository = $projetRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * Page d'accueil avec tous les projets non archivés
     */
    #[Route('/', name: '')]
    public function index(): Response
    {
        $projets = $this->projetRepository->findBy(['archive' => false]);
        return $this->render('projet/projet-liste.html.twig', ['projets' => $projets]);
    }

    /**
     * Formulaire de création d'un nouveau projet
     */
    #[Route('_add', name: '_add')]
    public function addProject(Request $request): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($projet->getEmployes() as $employe) {
                $employe->addProjet($projet);
            }

            $this->entityManager->persist($projet);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('projet/projet-add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Formulaire de modification d'un projet existant
     */
    #[Route('_edit/{id}', name: '_edit')]
    public function editProject($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = $entityManager->getRepository(Projet::class)->find($id);

        if (!$projet) {
            throw $this->createNotFoundException('Aucun projet trouvé avec cet identifiant.');
        }

        // Stocker les employés avant modification
        $originalEmployes = new ArrayCollection($projet->getEmployes()->toArray());

        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Ajouter les nouveaux employés sélectionnés
            foreach ($projet->getEmployes() as $employe) {
                if (!$originalEmployes->contains($employe)) {
                    $employe->addProjet($projet);
                }
            }

            // Supprimer les employés qui ne sont plus sélectionnés
            foreach ($originalEmployes as $employe) {
                if (!$projet->getEmployes()->contains($employe)) {
                    $employe->removeProjet($projet);
                }
            }

            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('projet/projet-edit.html.twig', [
            'form' => $form->createView(),
            'projet' => $projet,
        ]);
    }


    /**
     * Affiche les détails d'un projet et les tâches associées
     */
    #[Route('_details/{id}', name: '_details')]
    public function detailsProject($id): Response {

        $projet = $this->projetRepository->find($id);

        if (!$projet) {
            throw $this->createNotFoundException('Aucun projet trouvé avec cet identifiant.');
        }

        // Récupérer les tâches du projet
        $taches = $this->entityManager->getRepository(Tache::class)->findBy(['projet' => $projet]);

        // Organiser les tâches par statut
        $tachesParStatut = [
            'To do' => [],
            'Doing' => [],
            'Done' => [],
        ];

        foreach ($taches as $tache) {
            $statutLibelle = $tache->getStatutLibelle();
            if ($statutLibelle) {
                $tachesParStatut[$statutLibelle][] = $tache;
            }
        }

        return $this->render('projet/projet.html.twig', [
            'projetId' => $id,
            'projet' => $projet,
            'tachesParStatut' => $tachesParStatut,
        ]);

    }
    
    /**
     * Archiver un projet
     */
    #[Route('_delete/{id}', name: '_delete')]
    public function deleteProject($id): Response {

        $projet = $this->projetRepository->find($id);

        if (!$projet) {
            throw $this->createNotFoundException('Aucun projet trouvé avec cet identifiant.');
        }

        $projet->setArchive(true);

        $this->entityManager->persist($projet);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_project');
    }

}

