<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->json($this->getUser(), 200, [],[
            'groups' => ['user:read']
        ] );
    }

    public function displayUserIdentifier(): string{
        return $this->getUser()->getUserIdentifier();
    }

    public function displayName(): string{
        return $this->getUser()->getFirstName();
    }
}
