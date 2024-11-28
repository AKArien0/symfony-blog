<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profile(UserRepository $userRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response {
		$user = $userRepository->getById($id);
		if (!$user){
			die;
		}
	    elseif ($this->getUser()->getId() == $id){
			$form = $this->createForm(UserType::class, $user);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()){
				$entityManager->persist($user);
				$entityManager->flush();

				$this->addFlash("success", "Profile updated");
				return $this->redirectToRoute("profile", ["id" => $id]);
			}

		    return $this->render("user/profile.html.twig", [ "form" => $form, "user" => $user, "edit" => true]);
	    }
		return $this->render('user/profile.html.twig', ["user" => $user, "edit" => false]);
    }
}
