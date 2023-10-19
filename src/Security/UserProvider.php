<?php

namespace App\Security;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $repository;

    public function __construct(ParticipantRepository $repository)
    {
        $this->repository = $repository;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        // Ceci est fait uniquement pour des raisons de performance, vous pouvez supprimer cette méthode (avec l'interface RefreshUserInterface) si votre utilisateur est toujours en cache parfaitement les informations
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return Participant::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->repository->findOneBy(['mail' => $identifier]) ?: $this->repository->findOneBy(['pseudo' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('"%s" n\'existe pas.', $identifier));
        }

        return $user;
    }
}