<?php
// hand created static pages
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StaticController extends AbstractController
{
    #[Route('/about_me', name: 'about_me')]
    public function index(): Response
    {
        return $this->render('static_pages/about_me.html.twig', []);
    }
}

