<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $em;
    protected $validator;
    protected $name;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function response($data, $message = 'İşlem Başarılı', $code = 200, $status = 1){
        $message = isset($message) ? $message : 'İşlem Başarılı';
        $result = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
        if($status !== 1) $result['status'] = 'error';
        return $this->json($result)->setStatusCode($code)->setCharset("UTF-8");
    }

    protected function getResult($entity): array
    {
        return [
            $this->name => $entity->getResult(),
        ];
    }

    protected function validate($entity)
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                 $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return $messages;
        }
        else return null;
    }
}
