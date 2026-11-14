<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): JsonResponse
    {
        return $this->json([], 200);
    }

    #[Route('/forgot', name: 'app_fogot')]
    public function forgot(): JsonResponse
    {
        return $this->json([], 200);
    }
}
