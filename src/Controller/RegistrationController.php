<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(defaults={"_is_api": true})
 */
class RegistrationController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger=$logger;
    }

    #[Route('/api/register', name: 'app_register', methods: ['OPTIONS', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $data = json_decode($request->getContent(), true);
        if ($data === null){
            throw new BadRequestException('Invalid Json');
        }

        $email = $data['email'];
        $password = $data['password'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];

        $user->setLastName($lastName);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setFirstName($firstName);


        if ($email && $password) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $user->setFirstName($firstName);
            $user->setLastName($lastName);

            $entityManager->persist($user);
            $entityManager->flush();

            $response = new Response("successfully created account for" . $user->getUserIdentifier(), 201);
            $response->headers->set('Access-Control-Allow-Origin', '*');

            return $response;
        } else {
            $response = new Response("could not create account", 204);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;

        }

    }
}
