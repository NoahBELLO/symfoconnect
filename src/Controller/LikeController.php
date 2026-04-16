<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class LikeController extends AbstractController
{
    #[Route('/post/{id}/like', name: 'app_post_like', methods: ['POST'])]
    public function like(Post $post, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('like' . $post->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($post->getLikedBy()->contains($user)) {
            $post->removeLikedBy($user);
        } else {
            $post->addLikedBy($user);
        }

        $em->flush();

        $referer = $request->headers->get('referer');

        return $referer
            ? $this->redirect($referer)
            : $this->redirectToRoute('app_home');
    }
}
