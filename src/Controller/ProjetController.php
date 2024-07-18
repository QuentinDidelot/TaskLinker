<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetRepository;
use App\Entity\Projet;
use App\Form\ProjetType;
use Doctrine\ORM\EntityManagerInterface;


class ProjetController extends AbstractController {

    private $projetRepository;
    private $entityManager;

    public function __construct(ProjetRepository $projetRepository, EntityManagerInterface $entityManager) {
        $this->projetRepository = $projetRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Formulaire de création d'un nouveau projet
     */
    #[Route('/projet-add', name: 'app_add_project')]
    public function addproject(Request $request): Response {

        $projet =  new Projet();
        $form = $this->createForm(ProjetType::class, $projet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($projet);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('projet-add.html.twig', ['form' => $form->createView()]);
    }
}

