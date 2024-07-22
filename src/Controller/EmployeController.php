<?php

namespace App\Controller;

use finfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;

class EmployeController extends AbstractController
{

    private $employeRepository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->employeRepository = $entityManager->getRepository(Employe::class);
    }

    /**
     * Afficher la liste des employés
     */
    #[Route('/employe', name: 'app_employe_list')]
    public function showAllEmploye(): Response
    {
        // Récupération des employés dans une base de données
        $employes = $this->employeRepository->findAll();
        
        return $this->render('employes-liste.html.twig', ['employes' => $employes]);
    }
}
