<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\RegisterService;
use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    private $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    #[Route('/api/register', name: 'user_create', methods: ['POST'])]
    public function createPost(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $result = $this->registerService->register($data);

        if (isset($result['errors'])) {
            return new JsonResponse($result, JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result, JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/registers', name: 'get_allusers', methods:'GET')]
    public function getAllUsers(UserRepository $repository): JsonResponse
    {
        $users= $repository->findAll();
        return $this->json($users,200);
    }

}