<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegisterType;
use App\Entity\Employe;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_bienvenue')]
    public function bienvenue(): Response
    {
        return $this->render('connexion/bienvenue.html.twig');
    }

    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $erreur = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();

        return $this->render('connexion/login.html.twig', [
            'email' => $email,
            'erreur' => $erreur,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): never
    {
        // Symfony gère la déconnexion automatiquement
        throw new \Exception('This method should never be reached!');
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $employe = new Employe();
        $employe
            ->setStatut('CDI')
            ->setDateArrivee(new \DateTime());

        $form = $this->createForm(RegisterType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employe->setPassword($hasher->hashPassword($employe, $employe->getPassword()));

            $this->entityManager->persist($employe);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_projets'); // Assurez-vous que cette route existe
        }

        return $this->render('connexion/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
