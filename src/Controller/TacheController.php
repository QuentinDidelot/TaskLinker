<?php
namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Projet;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tache}', name: 'app_tache')]

class TacheController extends AbstractController
{

    private $projetRepository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->projetRepository = $entityManager->getRepository(Projet::class);
    }


    /**
     * Ajouter une nouvelle tâche au projet
     */
    #[Route('/ajouter/{projetId}', name: '_add')]
    public function addTache(int $projetId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = $entityManager->getRepository(Projet::class)->find($projetId);

        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $tache = new Tache();
        $tache->setProjet($projet);

        $form = $this->createForm(TacheType::class, $tache, ['projet_id' => $projetId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('tache/templates/tache-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher les détails d'une tâche et la possibilité de la modifier
     */
    #[Route('/{id}', name: '_detail')]
    public function detailTache(int $id, TacheRepository $tacheRepository, Request $request): Response
    {
        $tache = $tacheRepository->find($id);
        $projet = $tache->getProjet();
        
        if (!$tache) {
            throw $this->createNotFoundException('Aucune tâche trouvée avec cet identifiant.');
        }

        // Créer le formulaire pour modifier la tâche
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tache);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project_details', ['id' => $projet->getId()]);
        }

        return $this->render('tache/tache-detail.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprimer une tâche
     */
    #[Route('_delete/{id}', name: '_delete')]
    public function deleteTache(int $id, EntityManagerInterface $entityManager): Response
    {
        $tache = $this->entityManager->getRepository(Tache::class)->find($id);
        $projet = $tache->getProjet();
        if (!$tache) {
            throw $this->createNotFoundException('Aucune tâche trouvée avec cet identifiant.');
        }

        $this->entityManager->remove($tache);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_project_details', ['id' => $projet->getId()]);;
    }
}
