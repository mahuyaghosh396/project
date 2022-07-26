<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{

      // ---------------------------[[dashboard]]-----------------------------------------------------


    #[Route('/student/dashboard', name: 'web_student_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('student/dashboard.html.twig', [
            'title' => 'Student Dashboard',
        ]);
    }
}
