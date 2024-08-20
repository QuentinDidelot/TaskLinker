<?php

namespace App\Controller;

use App\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjetRepository;
use App\Repository\StatutRepository;
use App\Repository\TacheRepository;
use App\Form\TacheType;
use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TacheController extends AbstractController
{

    public function __construct(
        private ProjetRepository $projetRepository,
        private TacheRepository $tacheRepository,
        private EntityManagerInterface $entityManager,
    )
    {

    }

    /**
     * Permet d'ajouter une tache au projet
     *
     * @param Projet $projet The project to which the new task will be added.
     * @param Request $request The request object containing the form data.
     *
     * @return Response The response to be sent back to the client.
     *
     * @Route("/projets/{id}/taches/ajouter", name="app_tache_add")
     * @IsGranted("acces_projet", "projet")
     */
    #[Route('/projets/{id}/taches/ajouter', name: 'app_tache_add')]
    #[IsGranted('acces_projet', 'projet')]
    public function ajouterTache(Projet $projet, Request $request): Response
    {  
        if ($projet->isArchive()) {
            return $this->redirectToRoute('app_projets');
        }
    
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache, ['projet' => $projet]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $tache->setProjet($projet);
            $this->entityManager->persist($tache);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_projet', ['id' => $projet->getId()]);
        }
    
        return $this->render('tache/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    /**
     * Supprime une tâche du projet
     *
     * This function removes a task from the database and redirects the user to the project's page.
     * It checks if the project is archived before allowing the deletion.
     *
     * @param Tache $tache The task to be deleted.
     *
     * @return Response The response to be sent back to the client.
     *
     * @Route("/taches/{id}/supprimer", name="app_tache_delete")
     * @IsGranted("acces_tache", "tache")
     */
    #[Route('/taches/{id}/supprimer', name: 'app_tache_delete')]
    #[IsGranted('acces_tache', 'tache')]
    public function supprimerTache(Tache $tache): Response
    {
        if ($tache->getProjet()->isArchive()) {
            return $this->redirectToRoute('app_projets');
        }

        $this->entityManager->remove($tache);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_projet', ['id' => $tache->getProjet()->getId()]);
    }
    

    /**
     * Permet d'accéder à la page du projet où se trouvent toutes les tâches
     *
     * @param Tache $tache The task to be displayed and edited.
     * @param Request $request The request object containing the form data.
     *
     * @return Response The response to be sent back to the client.
     *
     * @Route("/taches/{id}", name="app_tache")
     * @IsGranted("acces_tache", "tache")
     */
    #[Route('/taches/{id}', name: 'app_tache')]
    #[IsGranted('acces_tache', 'tache')]
    public function tache(Tache $tache, Request $request): Response
    {
        if ($tache->getProjet()->isArchive()) {
            return $this->redirectToRoute('app_projets');
        }
    
        $form = $this->createForm(TacheType::class, $tache, ['projet' => $tache->getProjet()]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_projet', ['id' => $tache->getProjet()->getId()]);
        }
    
        return $this->render('tache/tache.html.twig', [
            'form' => $form->createView(),
            'tache' => $tache,
        ]);
    }
    
}
