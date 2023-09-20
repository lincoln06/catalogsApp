<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\GetUsersListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageUsersController extends AbstractController
{
    #[Route('/manage/users', name: 'app_manage_users')]
    public function index(GetUsersListService $getUsersListService): Response
    {
        return $this->render('manage_users/index.html.twig', [
            'usersList' => $getUsersListService->getUsersFromDatabase(),
        ]);
    }
    #[Route('/manage/users/delete/{id}', name: 'app_delete_user')]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id, GetUsersListService $getUsersListService): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $message = '';
        if (!$user) {
            $message = 'Brak danych';
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->render('manage_users/index.html.twig', [
            'message' => $message,
            'usersList' => $getUsersListService->getUsersFromDatabase()
        ]);
    }
}
