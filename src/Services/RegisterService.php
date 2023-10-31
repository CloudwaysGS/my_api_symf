<?php

// src/Service/RegisterService.php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterService
{
    private $serializer;
    private $validator;
    private $entityManager;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    public function register($requestData): array
    {
        $register = $this->serializer->deserialize(json_encode($requestData), User::class, 'json');

        $violations = $this->validator->validate($register);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return ['errors' => $errors];
        }

        $this->entityManager->persist($register);
        $this->entityManager->flush();

        return [
            'message' => 'Enregistrement reussi',
        ];
    }


}
