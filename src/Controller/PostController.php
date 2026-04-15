<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $userRepository->findOneBy([]);
            if ($author) {
                $post->setAuthor($author);
            }

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post publié avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/nouveau.html.twig', [
            'form' => $form,
        ]);
    }
}
