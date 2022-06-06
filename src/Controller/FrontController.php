<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    #[Route('/redirect', name: 'web_redirect')]
    public function checkRedirect(Request $request): Response
    {
        $url = 'home';
        if ($this->isGranted('ROLE_ADMIN')) {
            $url = 'admin_dashboard';
        } elseif ($this->isGranted('ROLE_STUDENT')) {
            $url = 'student_dashboard';
        } elseif ($this->isGranted('ROLE_LECTURER')) {
            $url = 'lecturer_dashboard';
        } else{

        }
            //$url = 'web_lecturer_dashboard';


            return $this->redirect($this->generateUrl($url));
    }
}
