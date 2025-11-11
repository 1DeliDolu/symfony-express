<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DatabaseSelectionController extends AbstractController
{
    #[Route('/admin/select-database', name: 'app_database_selection')]
    public function selectDatabase(): Response
    {
        return $this->render('database_selection/index.html.twig');
    }
}
