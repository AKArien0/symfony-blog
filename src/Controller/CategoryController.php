<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function categoryById(CategoryRepository $categoryRepository, int $id, Request $request): Response
    {
		$category = $categoryRepository->getById($id);
		if (!$category){
			die;
		}
		$posts = $category->getPosts();
        return $this->render('category/overview.html.twig', ["category" => $category, "posts" => $posts]);
    }
}
