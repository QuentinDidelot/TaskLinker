<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegisterType;
use App\Entity\Employe;
use App\Repository\ProjetRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MainController extends AbstractController
{
    private $projetRepository;
    private $entityManager;

    public function __construct(ProjetRepository $projetRepository, EntityManagerInterface $entityManager) {
        $this->projetRepository = $projetRepository;
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_bienvenue')]
    public function bienvenue(): Response
    {
        return $this->render('connexion/bienvenue.html.twig');
    }

    /** 
     * Page d'inscription
     */
    #[Route('/inscription', name: 'app_register')]
    public function inscription(Request $request, UserPasswordHasherInterface $passwordHasher): Response {
        $employe = new Employe();
        $form = $this->createForm(RegisterType::class, $employe);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword(
                $employe,
                $employe->getPassword() 
            );
            $employe->setPassword($password);
    
            $this->entityManager->persist($employe);
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Votre compte a été créé avec succès! Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_bienvenue');
        }
    
        return $this->render('connexion/inscription.html.twig', ['form' => $form->createView()]);
    }
    

}
