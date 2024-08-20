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
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

class MainController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Displays the welcome page.
     *
     * @Route("/", name="app_bienvenue")
     *
     * @return Response The rendered welcome page
     */
    #[Route('/', name: 'app_bienvenue')]
    public function bienvenue(): Response
    {
        return $this->render('connexion/bienvenue.html.twig');
    }

    /**
     * Displays the login form and handles user authentication.
     *
     * @Route("/connexion", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils The Symfony authentication utilities
     *
     * @return Response The rendered login form or a redirect to the home page if the user is already authenticated
     */
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


    /**
     * Handles the user logout process.
     *
     * @Route("/deconnexion", name="app_logout")
     *
     * @throws \Exception This method should never be reached as Symfony automatically handles the logout process.
     *
     * @return never This function does not return any value, it throws an exception instead.
     */
    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): never
    {
        // Symfony gère la déconnexion automatiquement
        throw new \Exception('This method should never be reached!');
    }


    #[Route('/2fa/qrcode', name: '2fa_qrcode')]
    public function displayGoogleAuthenticatorQrCode(GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        $user = $this->getUser();

        // Vérifie que l'utilisateur est bien une instance de TwoFactorInterface
        if (!$user instanceof TwoFactorInterface) {
            throw new \LogicException('User is not authenticated.');
        }

        // Récupére le contenu du QR code à partir de Google Authenticator
        $qrContent = $googleAuthenticator->getQRContent($user);

        // Génère le QR code
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        // Retourne l'image du QR code
        return new Response($qrCode->getString(), 200, ['Content-Type' => 'image/png']);
    }


    #[Route('/2fa', name: '2fa_login')]
    public function displayGoogleAuthenticator(): Response
    {
        return $this->render('connexion/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qrcode'),
        ]);
    }
    /**
     * Handles the user registration process.
     *
     * This function is responsible for creating a new user account, validating the registration form,
     * hashing the user's password, and persisting the new user in the database.
     *
     * @Route("/inscription", name="app_register")
     *
     * @param Request $request The Symfony request object, containing the user's input data
     * @param UserPasswordHasherInterface $hasher The Symfony password hasher service, used to hash the user's password
     *
     * @return Response The rendered registration form or a redirect to the project list page if the registration is successful
     */
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, GoogleAuthenticatorInterface $googleAuth): Response
    {
        $employe = new Employe();
        $employe
            ->setStatut('CDI')
            ->setDateArrivee(new \DateTime());

        $form = $this->createForm(RegisterType::class, $employe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $employe->setPassword($hasher->hashPassword($employe, $employe->getPassword()));
            $employe->setGoogleAuthenticatorSecret($googleAuth->generateSecret());

            $this->entityManager->persist($employe);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_projets');
        }
        
        return $this->render('connexion/register.html.twig', [
            'form' => $form,
        ]);
    }
}
