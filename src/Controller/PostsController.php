<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use App\Services\DataValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Services\EmailService;

class PostsController extends AbstractController
{

    #[Route('/api/post', name: 'app_posts')]
    public function create(
        Request $request,
        DataValidationService $dataValidationService,
        EntityManagerInterface $manager,
        EmailService $emailService
    )
    {
        $postData = json_decode($request->getContent(), true);

        $post = new Posts();
        $post->setTitre($postData['titre']);
        $post->setContenu($postData['contenu']);
        $post->setAuteur($postData['auteur']);
        $post->setEmail($postData['email']);

        $errors = $dataValidationService->validateData($post, $post);

        if ($errors !== null) {
            return new JsonResponse(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $emailService->sendEmail($post->getAuteur(), $post->getEmail(), $post->getTitre(), $post->getContenu());

        $manager->persist($post);
        $manager->flush();

        return new JsonResponse(['message' => 'Post cree avec succes']);
    }


    #[Route('/api/posts' , methods:'GET')]
    public function getAllUsers(PostsRepository $postsRepository): JsonResponse
    {
        $post= $postsRepository->findAll();

        return $this->json($post,200);
    }


    #[Route('/api/posts/{id}', methods: ['GET'])]
    public function Detail(PostsRepository $repository, $id)
    {
        $post = $repository->find($id);

        if (!$post) {
            return $this->json(['message' => 'Post not found'], 404);
        }

        return $this->json($post, 200);
    }


}

