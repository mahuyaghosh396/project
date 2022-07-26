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
        $url = 'web_homepage';
        if ($this->isGranted('ROLE_ADMIN')) {
            $url = 'web_admin_dashboard';
        } elseif ($this->isGranted('ROLE_STUDENT')) {
            $url = 'web_student_dashboard';
        } elseif ($this->isGranted('ROLE_LECTURER')) {
            $url = 'lecturer_dashboard';
        } else {
        }

        return $this->redirect($this->generateUrl($url));
    }

    #[Route('/download/{path}/{file}', name: 'web_download')]
    public function download($path, $file)
    {
        $response = new Response();
        $filename = $this->getParameter('upload_directory') . "/" . $path . "/" . $file;
        if (file_exists($filename)) {
            // Set headers
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($filename));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
            $response->headers->set('Content-length', filesize($filename));
            // Send headers before outputting anything
            $response->sendHeaders();
            $response->setContent(file_get_contents($filename));
        }
        return $response;
    }
}
