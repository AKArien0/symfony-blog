<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/', name: 'recent_posts')]
    public function recent(PostRepository $postRepository): Response{
	    $posts = $postRepository->getLatest(3);
	    return $this->render('post/view.html.twig', ['posts' => $posts]);
    }

	#[IsGranted('ROLE_ADMIN')]
	#[Route('/create/post', name: 'create_post')]
	public function create(Request $request, EntityManagerInterface $entityManager): Response {
		if (!$this->isGranted("ROLE_ADMIN")){
			throw $this->createAccessDeniedException("You do not have access to this. Try befriending me first.");
		}
		$post = new Post();
		$form = $this->createForm(PostType::class, $post);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$post->setCreator($this->getUser()->getId());
			$post->setCreatedAt(new \DateTimeImmutable());
 			$picture = $form->get('picture')->getData();
			if ($picture){
				$post->setPicture($picture->move("media/articles_images", uniqid() . "." . $picture->guessExtension()));
			}
			
			$entityManager->persist($post);
			$entityManager->flush();

			$this->addFlash("success", "Article uploaded. You earned your cofee. Or tea. Or whatever, go off queen.");
			return $this->redirectToRoute("view_post", ["" => $post.getId()]);
		}

		return $this->render("post/create.html.twig", ["form" => $form->createView()]);
	}

}
