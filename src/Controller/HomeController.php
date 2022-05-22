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

    #[Route('/view/notice', name: 'web_all_notice')]
    public function viewNotice(): Response
    {
        return $this->render('home/notice.html.twig', [
            'title'=>"notice"
        ]);
    }
    #[Route('/add/notice', name: 'web_add_notice')]
    public function addNotice(): Response
    {
        return $this->render('home/add-notice.html.twig', [
            'title'=>"notice"
        ]);
    }
    #[Route('/contact', name: 'web_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'title'=>"contact"
        ]);
    }
}
