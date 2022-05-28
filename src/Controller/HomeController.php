<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Notice;
use App\Form\ContactType;
use App\Form\NoticeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'web_homepage')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    #[Route('/front', name: 'web_frontpage')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [
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
        $em = $doctrine->getManager();
        $today = new \DateTime();
        $query = $em->createQuery("SELECT u.name from App:Notice u where u.noticeTo < :today");
        $query->setParameter('today', $today);
        //dd($query->getResult());
        
        
        if($query){
        // $fs=new Filesystem();

        // // $notice = $media->getNotice();
        // // $em->remove($media);
        // $em->flush();

        // return $this->redirectToRoute('web_add_notice');
    

        //dd($path='%kernel.project_dir%/public/uploads/notices'.$query);
        // $fs->remove($path);
        // $em->flush();

           // unlink('upload_directory').'/uploads/notices/';
       // $fs->remove('/uploads/notices/'.$query->getResult());
        // dd($fs);
        // $em->flush();
        // dd("123");
        }
       
        
        return $this->render('home/notice.html.twig', [
            "notices" => $notices
        ]);
    }
    #[Route('/admin/add/notice', name: 'web_add_notice')]
    public function addNotice(Request $request, ManagerRegistry $doctrine,SluggerInterface $slugger): Response

    {

        $notice = new Notice();
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            
            if ($form->isSubmitted() and $form->isValid()) {

                /** @var UploadedFile $upload */
                $upload = $form->get('file')->getData();

                if ($upload) {

                    /*original file name..if my file name is "my_first_notice"..then $originalfilename
                    return the actual name of the file*/
                    $originalFilename = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);

                    //$filename returns the file name like that " my-first-notice " 
                    $Filename = $slugger->slug($originalFilename);

                    //$newFilename returns a unique file name with extension..like that" my-first-notice.pdf"
                    //guessExtension()is a method which returns the original extension of the file.
                    $newFilename = $Filename . '-' . uniqid() . '.' . $upload->guessExtension();

                    //move uploaded file....
                    try {
                        $upload->move(
                            $this->getParameter('upload_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }


                    //set file name....with it's new file name....
                    $notice->setFile($newFilename);
                  

                
               
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


