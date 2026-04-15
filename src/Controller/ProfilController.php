<?php

namespace App\Controller;

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

        return $this->render('profil/show.html.twig', [
            'user' => $user,
        ]);
    }
}
