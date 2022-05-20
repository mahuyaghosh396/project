<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'web_homepage')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    #[Route('/about', name: 'web_about_us')]
    public function aboutUs(): Response
    {
        return $this->render('home/about_us.html.twig', [
        ]);
    }

    #[Route('/signup', name: 'web_signup')]
    public function signup(): Response
    {
        return $this->render('home/signup.html.twig', [
        ]);
    }
    #[Route('/admission', name: 'web_admission')]
    public function admission(): Response
    {
        return $this->render('admission.html.twig', [
        ]);
    }
    #[Route('/result', name: 'web_result')]
    public function result(): Response
    {
        return $this->render('result.html.twig', [
        ]);
    }
}
