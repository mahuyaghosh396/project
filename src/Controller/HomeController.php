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

    #[Route('/front', name: 'web_frontpage')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', []);
    }
    #[Route('/about', name: 'web_about_us')]
    public function aboutUs(): Response
    {
        return $this->render('home/about_us.html.twig', []);
    }
}
