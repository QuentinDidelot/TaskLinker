<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetRepository;
use App\Entity\Projet;
use DOCTRINE\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    private $projetRepository;

    public function __construct(ProjetRepository $projetRepository) {
        $this->projetRepository = $projetRepository;
    }
    /**
     * Page d'accueil avec tous les projets
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $projets = $this->projetRepository->findAll();
        return $this->render('accueil.html.twig', ['projets' => $projets]);
    }
}
