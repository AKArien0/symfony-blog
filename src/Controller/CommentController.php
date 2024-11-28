<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
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
