<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    public function __construct(protected UserRepository $userRepository) {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('user/show.html.twig');
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        return $this->render('user/new.html.twig');
    }

    #[Route('/update/{id}', 'PUT', name: 'update')]
    public function update(int $id): Response
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user instanceof User) {
            throw $this->createNotFoundException('User not found.');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user
        ]);
    }
}