<?php

namespace App\Controller;

use App\Services\GetUsersListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageUsersController extends AbstractController
{
    #[Route('/manage/users', name: 'app_manage_users')]
    public function index(GetUsersListService $getUsersListService): Response
    {
        $usersList = $getUsersListService->getUsersFromDatabase();
        return $this->render('manage_users/index.html.twig', [
            'usersList' => $usersList,
        ]);
    }
}
