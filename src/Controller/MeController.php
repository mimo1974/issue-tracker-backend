<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * @psalm-suppress UnusedClass Instantiated by Symfony's controller resolver via
 *     routing attributes, not referenced directly from application code.
 */
class MeController extends AbstractController
{
    #[Route('/me', name: 'app_me', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user): Response
    {
        return $this->render('security/me.html.twig', ['user' => $user]);
    }
}
