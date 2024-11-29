<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;

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

    #[IsGranted('ROLE_ADMIN')]
	#[Route('/create/category', name: 'create_category')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully.');

            return $this->redirectToRoute('create_post');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
