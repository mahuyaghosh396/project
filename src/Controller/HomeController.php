<?php

namespace App\Controller;

use App\Entity\Admission;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/admission', name: 'web_admission')]
    public function admission(): Response
    {
        return $this->render('home/admit.html.twig', [
            'title'=>"admission"
        ]);
       
    }

    #[Route('/details', name: 'web_personal_details')]
    public function details(): Response
    {
        return $this->render('home/personaldata.html.twig', [
            'title'=>"admission"
        ]);
    }



    #[Route('/admission', name: 'manage_admission')]
    public function manageAdmission(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        
        if ($request->getMethod() == "POST") {
            $admission = new Admission();
            $admission->setName($request->get('name'));
            $admission->setEmail($request->get('email'));
            $admission->setPhoneNo($request->get('phone_no'));
            $admission->setadress($request->get('adress'));
            $admission->setzipno($request->get('zip_no'));
            $admission->setState($request->get('state'));
            $admission->setDistrict($request->get('district'));
            $admission->setTrade($request->get('trade'));

            $em->persist($admission);
            $em->flush();
        }

        return $this->render('home/admit.html.twig', [
        
        ]);
    }
}
