<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class FollowController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow', methods: ['POST'])]
    public function follow(User $userToFollow, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('follow' . $userToFollow->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $userToFollow->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous suivre vous-même.');

            return $this->redirectToRoute('app_profil', ['username' => $userToFollow->getUsername()]);
        }

        if (!$currentUser->getFollowing()->contains($userToFollow)) {
            $currentUser->addFollowing($userToFollow);

            $notification = new Notification();
            $notification->setRecipient($userToFollow);
            $notification->setType('follow');
            $notification->setContent($currentUser->getUsername() . ' vous suit maintenant.');
            $em->persist($notification);

            $em->flush();
        }

        return $this->redirectToRoute('app_profil', ['username' => $userToFollow->getUsername()]);
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow', methods: ['POST'])]
    public function unfollow(User $userToUnfollow, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('unfollow' . $userToUnfollow->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $currentUser->removeFollowing($userToUnfollow);
        $em->flush();

        return $this->redirectToRoute('app_profil', ['username' => $userToUnfollow->getUsername()]);
    }
}
