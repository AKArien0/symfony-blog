<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
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
	    $posts = $postRepository->getLatest(15);
	    return $this->render('post/overview.html.twig', ['posts' => $posts]);
    }

    #[Route('/view/post/{id}', name: 'view_post')]
    public function view(PostRepository $postRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response {
	    $post = $postRepository->getById($id);
	    if (!$post){
			die;
	    }
	    $comments = $post->getComments();

		if ($this->getUser() && (in_array("ROLE_USER", $this->getUser()->getRoles()) || in_array("ROLE_ADMIN", $this->getUser()->getRoles()))){
			$comment = new Comment();
			$form = $this->createForm(CommentType::class, $comment);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()){
				$comment->setCreator($this->getUser());
				$comment->setCreatedAt(new \DateTimeImmutable());
				$comment->setPost($post);

				$entityManager->persist($comment);
				$entityManager->flush();

				$this->addFlash("success", "Comment successfully added.");
				return $this->redirectToRoute("view_post", ["id" => $comment->getPost()->getId()]);
			}
			return $this->render("post/view.html.twig", ["post" => $post, "comment_form" => $form->createView(), "comments" => $comments]);

		}

		return $this->render("post/view.html.twig", ["post" => $post, "comments" => $comments]);
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
			$post->setCreator($this->getUser());
			$post->setPublishedAt(new \DateTimeImmutable());

 			$picture = $form->get('picture')->getData();
			if ($picture){
				$post->setPicture($picture->move("media/articles_images", uniqid() . "." . $picture->guessExtension()));
			}

			$entityManager->persist($post);
			$entityManager->flush();

			$this->addFlash("success", "Article uploaded. You earned your cofee. Or tea. Or whatever, go off queen.");
			return $this->redirectToRoute("view_post", ["id" => $post->getId()]);
		}

		return $this->render("post/create.html.twig", ["form" => $form->createView()]);
	}

	#[IsGranted("ROLE_ADMIN")]
	#[Route("/delete/post/{id}", name: "delete_post")]
	public function delete(PostRepository $postRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response {
	    $post = $postRepository->getById($id);
	    if (!$post){
			die;
	    }
	    $comments = $post->getComments();
		foreach ($comments as $comment){
			$entityManager->remove($comment);
		}
		$entityManager->remove($post);
		$entityManager->flush();

		$this->addFlash("success", "Post deleted successfully");

		return $this->redirectToRoute("recent_posts");
	}
}
