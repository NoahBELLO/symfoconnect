<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    #[Route('/profil/{username}', name: 'app_profil')]
    public function show(string $username, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        $isFollowing = $currentUser && $currentUser !== $user
            && $currentUser->getFollowing()->contains($user);

        $isSelf = $currentUser === $user;

        return $this->render('profil/show.html.twig', [
            'user' => $user,
            'isFollowing' => $isFollowing,
            'isSelf' => $isSelf,
        ]);
    }
}
