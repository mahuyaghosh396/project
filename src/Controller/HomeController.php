<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Notice;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{

    #[Route('/', name: 'web_homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $today = new \DateTime("today");
        $query = $em->createQuery("SELECT u from App:Notice u where u.noticeFrom < :today  and u.status ='Active' and u.type='Student' and u.noticeTo > :today");
        $query->setParameter('today', $today);
        $studentNotices = $query->getResult();

        $query = $em->createQuery("SELECT u from App:Notice u where u.noticeFrom < :today  and u.status ='Active' and u.type='Faculty' and u.noticeTo > :today");
        $query->setParameter('today', $today);
        $facultyNotices = $query->getResult();

        $query = $em->createQuery("SELECT u from App:Notice u where u.noticeFrom < :today  and u.status ='Active' and u.type='Tender' and u.noticeTo > :today");
        $query->setParameter('today', $today);
        $tenderNotices = $query->getResult();

        return $this->render('home/index.html.twig', [
            "studentNotices" => $studentNotices,
            "facultyNotices" => $facultyNotices,
            "tenderNotices" => $tenderNotices,
        ]);
    }

    // #[Route('/redirect', name: 'web_redirect')]
    // public function checkRedirect(Request $request): Response
    // {
    //     $url = 'web_homepage';
    //     if ($this->isGranted('ROLE_ADMIN')) {
    //         $url = 'admin_dashboard';
    //     } elseif ($this->isGranted('ROLE_STUDENT')) {
    //         $url = 'student_dashboard';
    //     } elseif ($this->isGranted('ROLE_LECTURER')) {
    //         $url = 'lecturer_dashboard';
    //     } else {
    //     }

    //     return $this->redirect($this->generateUrl($url));
    // }

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


    #[Route('/contact', name: 'web_contact_us')]
    public function contact(Request $request, ManagerRegistry $doctrine): Response
    {
        $contact = new Contact();

        if ($request->getMethod() == "POST") {


            $contact->setName($request->get('name'));
            $contact->setEmail($request->get('email'));
            $contact->setMessage($request->get('msg'));
            $em = $doctrine->getManager();
            $em->persist($contact);
            $em->flush();
            $request->getSession()->getFlashBag()->add("successmsg", "Your message has been received successfully");
            return $this->redirect($this->generateUrl('web_contact_us'));
        }


        return $this->render('home/contact.html.twig', [
            'title' => "Contact",

        ]);
    }

    #[Route('/admin/list/contact', name: 'web_list_contact')]
    public function listbook(ManagerRegistry $doctrine): Response
    {
        $contacts = $doctrine->getRepository(Contact::class)->findAll();
        return $this->render('home/contact_list.html.twig', [
            "contacts" => $contacts,
            'title' => 'List Contact'
        ]);
    }
}
