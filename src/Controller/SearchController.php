<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $page = $request->query->getInt('page', 1);

        $username = $request->query->get('username');
        $data = json_decode($request->getContent(), true);

        $accept = $request->headers->get('Accept');

        if($request->isMethod("POST")){}
        
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}
