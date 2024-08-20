<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmployeRepository;
use App\Form\EmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EmployeController extends AbstractController
{
    private EmployeRepository $employeRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager, EmployeRepository $employeRepository)
    {
        $this->entityManager = $entityManager;
        $this->employeRepository = $employeRepository;
    }

    /**
     * Displays a list of all employees.
     *
     * @Route("/employes", name="app_employes")
     * @IsGranted("ROLE_ADMIN")
     *
     * @return Response Returns a Response object with the rendered template
     */
    #[Route('/employes', name: 'app_employes')]
    #[IsGranted('ROLE_ADMIN')]
    public function employes(): Response
    {
        $employes = $this->employeRepository->findAll();
        
        return $this->render('employe/liste.html.twig', [
            'employes' => $employes,
        ]);
    }

    /**
     * Displays a specific employee's details.
     *
     * @Route("/employes/{id}", name="app_employe")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param int $id The unique identifier of the employee to display.
     *
     * @return Response Returns a Response object with the rendered template displaying the employee's details.
     *                  If the employee is not found, redirects to the list of all employees.
     */
    #[Route('/employes/{id}', name: 'app_employe')]
    #[IsGranted('ROLE_ADMIN')]
    public function employe($id): Response
    {
        $employe = $this->employeRepository->find($id);

        if(!$employe) {
            return $this->redirectToRoute('app_employes');
        }
        
        return $this->render('employe/employe.html.twig', [
            'employe' => $employe,
        ]);
    }


    /**
     * Deletes a specific employee from the database.
     *
     * @Route("/employes/{id}/supprimer", name="app_employe_delete")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param int $id The unique identifier of the employee to delete.
     *
     * @return Response Returns a Response object that redirects to the list of all employees.
     *                  If the employee is not found, redirects to the list of all employees.
     */
    #[Route('/employes/{id}/supprimer', name: 'app_employe_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function supprimerEmploye($id): Response
    {
        $employe = $this->employeRepository->find($id);

        if(!$employe) {
            return $this->redirectToRoute('app_employes');
        }

        $this->entityManager->remove($employe);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('app_employes');
    }


    /**
     * Edits a specific employee in the database.
     *
     * @Route("/employes/{id}/editer", name="app_employe_edit")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param int $id The unique identifier of the employee to edit.
     * @param Request $request The request object containing the form data.
     *
     * @return Response Returns a Response object that renders the employee edit template.
     *                  If the employee is not found, redirects to the list of all employees.
     *                  If the form is submitted and valid, updates the employee in the database and redirects to the list of all employees.
     */
    #[Route('/employes/{id}/editer', name: 'app_employe_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editerEmploye($id, Request $request): Response
    {
        $employe = $this->employeRepository->find($id);

        if(!$employe) {
            return $this->redirectToRoute('app_employes');
        }

        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_employes');
        }

        return $this->render('employe/employe.html.twig', [
            'employe' => $employe,
            'form' => $form->createView(),
        ]);
    }
}
