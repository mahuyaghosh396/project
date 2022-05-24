<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Notice;
use App\Form\ContactType;
use App\Form\NoticeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


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
    public function viewNotice(ManagerRegistry $doctrine): Response
    {
        $notices = $doctrine->getRepository(Notice::class)->findAll();
        return $this->render('home/notice.html.twig', [
            "notices" => $notices
        ]);
    }
    #[Route('/admin/add/notice', name: 'web_add_notice')]
    public function addNotice(Request $request, ManagerRegistry $doctrine): Response

    {

        $notice = new Notice();
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            
            if ($form->isSubmitted() and $form->isValid()) {
                
               
                $em = $doctrine->getManager();
                
                $em->persist($notice);

                $em->flush();

                $request->getSession()->getFlashBag()->add("successmsg", "Notice uploaded Successfully");
                return $this->redirect($this->generateUrl('web_add_notice'));
            } else {
                $request->getSession()->getFlashBag()->add("errormsg", "something went wrong!!");
                return $this->redirect($this->generateUrl('web_add_notice'));
            }
        }

        return $this->render('home/add-notice.html.twig', [
            'title' => "Add Notice",
            'form' => $form->createView()
        ]);
    }
    #[Route('/contact', name: 'web_contact')]
    public function contact(Request $request, ManagerRegistry $doctrine): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            
            if ($form->isSubmitted() and $form->isValid()) {
                
               
                $em = $doctrine->getManager();
                $em->persist($contact);
                $em->flush();
                $request->getSession()->getFlashBag()->add("successmsg", "Your message has been received successfully");
                return $this->redirect($this->generateUrl('web_contact'));
            } else {
                $request->getSession()->getFlashBag()->add("errormsg", "something went wrong!!");
                return $this->redirect($this->generateUrl('web_contact'));
            }
        }

        return $this->render('home/contact.html.twig', [
            'title' => "Contact",
            'form' => $form->createView()
        ]);
    }
    }

