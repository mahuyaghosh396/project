<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Notice;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\NoticeType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/update/notice/{id}', name: 'app_update_notice')]
    public function updateNotice(ManagerRegistry $doctrine, $id): Response
    {

        $em = $doctrine->getManager();
        $notice = $doctrine->getRepository("App\Entity\Notice")->findOneBy(["id" => $id]);
        if ($notice->getStatus() == "Active") {
            $notice->setStatus("Deleted");
            $em->persist($notice);
            $em->flush();
        } else {
            $notice->setStatus("Active");
            $em->persist($notice);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('web_all_notice'));
    }

    #[Route('admin/add/user', name: 'app_admin_add_user')]
    public function addUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();
        $user = new User();
        $user->setFirstName('Ankita');
        $user->setLastName('Baidya');
        $user->setEmail('ankita@g.c');
        $user->setCellphone('12345');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword($passwordHasher->hashPassword($user, "1234"));
        $em->persist($user);
        $em->flush();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


    #[Route('/admin/list/notice', name: 'app_admin_list_notice')]
    public function listNotice(ManagerRegistry $doctrine): Response
    {
        $notices = $doctrine->getRepository(Notice::class)->findAll();
        return $this->render('admin/list_notice.html.twig', [
            "notices" => $notices
        ]);
    }

    #[Route('/admin/download/{path}/{file}', name: 'app_admin_download')]
    public function download($path, $file)
    {
        $filename = $this->getParameter('upload_directory') . "/" . $path . "/" . $file;

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
        $response->headers->set('Content-length', filesize($filename));
        // Send headers before outputting anything
        $response->sendHeaders();
        $response->setContent(file_get_contents($filename));
        return 0;
    }

    #[Route('/view/notice', name: 'web_all_notice')]
    public function viewNotice(ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        // $abc=$this->getUser();
        // $query = $em->createQuery("SELECT u.roles from App:User u where u= :abc");
        // $query->setParameter('abc', $abc);
        // $a=$query->getResult();
        // dump($a);

        if ($this->isGranted('ROLE_ADMIN')) {

            $query = $em->createQuery("SELECT u from App:Notice u");
            $query->getResult();
        } else {
            $today = new \DateTime();
            $query = $em->createQuery("SELECT u from App:Notice u where u.noticeTo > :today and u.status ='active'");
            $query->setParameter('today', $today);
        }

        return $this->render('admin/list_notice.html.twig', [
            "notices" => $query->getResult()
        ]);
    }

    #[Route('/current/notice', name: 'web_current_notice')]
    public function currentNotice(ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();
        $yesterday = new \DateTime("yesterday");
        $query = $em->createQuery("SELECT u from App:Notice u where u.noticeFrom > :yesterday");
        $query->setParameter('yesterday', $yesterday);

        return $this->render('admin/current_notice.html.twig', [
            "notices" => $query->getResult()
        ]);
    }

    #[Route('/admin/manage/notice', name: 'web_manage_notice')]
    public function manageNotice(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response

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
                            $this->getParameter('upload_directory') . "/notices/",
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

        return $this->render('admin/manage_notice.html.twig', [
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

        return $this->render('admin/contact.html.twig', [
            'title' => "Contact",
            'form' => $form->createView()
        ]);
    }
    #[Route('/signup', name: 'web_signup')]
    public function signup(): Response
    {
        return $this->render('admin/signup.html.twig', []);
    }
}
