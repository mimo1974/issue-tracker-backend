<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @psalm-suppress UnusedClass Instantiated by Symfony's controller resolver via
 *     routing attributes, not referenced directly from application code.
 */
class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('index.html.twig');
    }
}
