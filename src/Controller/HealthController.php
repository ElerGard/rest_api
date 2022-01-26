<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Health;

class HealthController extends AbstractController
{
    /**
     * @Route("/health", name="health")
     */
    public function health(Health $service): Response
    {
        return $this->json([
            'APP_ENV' => $service->getHealth()
        ]);
    }
}
