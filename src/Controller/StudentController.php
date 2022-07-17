<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student/dashboard', name: 'student_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('student/dashboard.html.twig', [
            'title' => 'Student Dashboard',
        ]);
    }
}
