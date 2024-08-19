<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\ProjetRepository;
use App\Repository\TacheRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjetVoter extends Voter
{
    public function __construct(
        private ProjetRepository $projetRepository,
        private TacheRepository $tacheRepository,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['acces_projet', 'acces_tache']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Vérifie si l'utilisateur est authentifié
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifie si l'utilisateur est un admin
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Traitement spécifique selon l'attribut
        if ($attribute === 'acces_projet') {
            $projet = $this->projetRepository->find($subject);
            // if (!$projet) {
            //     return false;
            // }
            return $projet->getEmployes()->contains($user);
        }

        if ($attribute === 'acces_tache') {
            $tache = $this->tacheRepository->find($subject);
            if (!$tache) {
                return false;
            }
            $projet = $tache->getProjet();
            return $projet->getEmployes()->contains($user);
        }

        return false;
    }
}
