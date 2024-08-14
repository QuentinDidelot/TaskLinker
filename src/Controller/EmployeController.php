<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Form\EmployeType;

#[Route('/employe', name: 'app_employe')]

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
    #[Route('', name: '_list')]
    public function showAllEmploye(): Response
    {
        // Récupération des employés dans une base de données
        $employes = $this->employeRepository->findAll();
        
        return $this->render('employe/employes-liste.html.twig', ['employes' => $employes]);
    }

    /**
     * Afficher les détails d'un employé avec la possibilité de le modifier
     */
    #[Route('/{id}', name: '_detail')]
    public function detailEmploye(int $id, Request $request): Response {
        $employe = $this->employeRepository->find($id);

        if (!$employe) {
            throw $this->createNotFoundException('Aucun employé trouvé avec cet identifiant.');
        }

        // Formulaire pour modifier un employé
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($employe);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_employe_detail', ['id' => $employe->getId()]);
        }

        return $this->render('employe/employes-details.html.twig', [
            'employe' => $employe,
            'form' => $form->createView()]);
    }

    /**
     * Supprimer un employé
     */
    #[Route('/{id}/delete', name: '_delete')] 
    public function deleteEmploye(int $id): Response {
        $employe = $this->employeRepository->find($id);

        if (!$employe) {
            throw $this->createNotFoundException('Aucun employé trouvé avec cet identifiant.');
        }

        $this->entityManager->remove($employe);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_employe_list');
    }
}
