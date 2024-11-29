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


	#[IsGranted('ROLE_ADMIN')]
	#[Route('/user/list', name: "list_users")]
	public function listUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

	#[IsGranted('ROLE_ADMIN')]
	#[Route("/profile/deactivate/{id}", name: "deactivate_profile")]
	public function deactivateUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('error', 'User not found');
            return $this->redirectToRoute('admin_users');
        }

        // Clear the roles
        $user->setRoles([]);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'User account has been deactivated.');
        return $this->redirectToRoute('list_users');
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profile(UserRepository $userRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response {
		$user = $userRepository->getById($id);
		if (!$user){
			die;
		}
	    elseif ($this->getUser() && $this->getUser()->getId() == $id){
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
