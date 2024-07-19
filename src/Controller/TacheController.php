<?php
namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Statut;
use App\Entity\Projet;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TacheController extends AbstractController
{
    #[Route('/tache/ajouter/{projetId}', name: 'app_add_tache')]
    public function addTache(int $projetId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Cherche le projet avec l'ID donné
        $projet = $entityManager->getRepository(Projet::class)->find($projetId);

        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $tache = new Tache();
        $tache->setProjet($projet);

        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('tache-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
