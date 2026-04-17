<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[IsGranted('ROLE_USER')]
final class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(PostRepository $postRepository, CacheInterface $cache): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $cacheKey = 'feed_user_' . $user->getId();

        $posts = $cache->get($cacheKey, function (ItemInterface $item) use ($postRepository, $user) {
            $item->expiresAfter(300); // 5 minutes
            return $postRepository->findFeedForUser($user);
        });

        return $this->render('feed/index.html.twig', [
            'posts' => $posts,
            'hasFollowing' => $user->getFollowing()->count() > 0,
        ]);
    }
}
