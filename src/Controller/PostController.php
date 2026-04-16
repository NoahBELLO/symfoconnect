<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Security\PostVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_nouveau')]
    #[IsGranted('ROLE_USER')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post publié avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/nouveau.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/post/{id}/supprimer', name: 'app_post_supprimer', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function supprimer(Post $post, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post supprimé.');

        return $this->redirectToRoute('app_home');
    }
}
