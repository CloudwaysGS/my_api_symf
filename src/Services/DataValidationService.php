<?php

namespace App\Services;

// src/Service/DataValidationService.php

use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataValidationService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateData($data, $entity)
    {
        $violations = $this->validator->validate($data);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $errors;
        }

        return null;
    }
}