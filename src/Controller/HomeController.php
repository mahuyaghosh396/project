<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Notice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    
    #[Route('/', name: 'web_homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $today = new \DateTime("today");
        $query = $em->createQuery("SELECT u from App:Notice u where u.noticeFrom < :today  and u.status ='active' and u.noticeTo > :today");
        $query->setParameter('today', $today);
        $notices = $query->getResult();
        //dd($query->getResult());
        return $this->render('home/index.html.twig', [
            "notices" => $notices
        ]);
    }

    #[Route('/redirect', name: 'web_redirect')]
    public function checkRedirect(Request $request): Response
    {
        $url = 'web_homepage';
        if ($this->isGranted('ROLE_ADMIN')) {
            $url = 'admin_dashboard';
        } elseif ($this->isGranted('ROLE_STUDENT')) {
            $url = 'student_dashboard';
        } elseif ($this->isGranted('ROLE_LECTURER')) {
            $url = 'lecturer_dashboard';
        } else {
        }

        return $this->redirect($this->generateUrl($url));
    }
    
    #[Route('/about/college', name: 'web_college')]
    public function aboutCollege(): Response
    {
        return $this->render('home/about_college.html.twig', []);
    }

    #[Route('/gallery', name: 'web_gallery')]
    public function gallery(): Response
    {
        return $this->render('home/gallery.html.twig', []);
    }
}
