<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

	#[IsGranted("ROLE_ADMIN")]
	#[Route("/delete/comment/{id}-{goToPost}", name: "delete_comment")]
	public function delete(CommentRepository $commentRepository, int $id, int $goToPost, Request $request, EntityManagerInterface $entityManager): Response {
	    $comment = $commentRepository->getById($id);
		if (!($this->isGranted("ROLE_ADMIN") || ($comment->getCreator() == $this->getUser()))){
			throw $this->createAccessDeniedException("You are not authorise to do this. If this is your comment, log in to remove it. If you think it should be removed, contact me.");
		}
		$entityManager->remove($comment);
		$entityManager->flush();

		$this->addFlash("success", "Comment deleted successfully");

		// return $this->redirectToRoute("recent_posts");
		return $this->redirectToRoute("view_post", ["id" => $goToPost]);
	}

	// no : using PostController instead
	// #[IsGranted('ROLE_USER')]
	// #[Route('/create/comment', name: 'create_comment')]
	// public function create(Request $request, EntityManagerInterface $entityManager, Post $tiedto): Response {
	// 	if (!$this->isGranted("ROLE_USER")){
	// 		throw $this->createAccessDeniedException("Please log in to leave comments.");
	// 	}
	// 	$comment = new Comment();
	// 	$form = $this->createForm(CommentType::class, $comment);

	// 	$form->handleRequest($request);

	// 	if ($form->isSubmitted() && $form->isValid()){
	// 		$comment->setCreator($this->getUser());
	// 		$comment->setCreatedAt(new \DateTimeImmutable());
	// 		$comment->setPost($tiedTo);

	// 		$entityManager->persist($comment);
	// 		$entityManager->flush();

	// 		$this->addFlash("success", "Comment successfully added.");
	// 		return $this->redirectToRoute("view_post", ["id" => $comment->getPost()->getId()]);
	// 	}

	// 	return $this->render("comment/create.html.twig", ["form" => $form->createView()]);
	// }
}
