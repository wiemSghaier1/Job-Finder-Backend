<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedBackController extends AbstractFOSRestController
{
    /**
     * @Route("/feed/back", name="feed_back")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FeedBackController.php',
        ]);
    }
}
