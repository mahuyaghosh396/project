<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\LibraryBook;
use App\Entity\Notice;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\NoticeType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'title' => 'Admin Dashboard',
        ]);
    }

    #[Route('admin/list/user', name: 'web_admin_list_user')]
    public function listUser(ManagerRegistry $mr): Response
    {
        $users = $mr->getRepository("App\Entity\User")->findAll();
        return $this->render('admin/list_user.html.twig', [
            'title' => "List User",
            'users' => $users,
        ]);
    }

    #[Route('admin/manage/user/{id}', name: 'web_admin_manage_user')]
    public function manageUser(Request $request, ManagerRegistry $mr, UserPasswordHasherInterface $passwordHasher, $id = -1): Response
    {
        $title = "Update User";
        $em = $mr->getManager();
        $user = $mr->getRepository(User::class)->findOneBy(["id" => $id]);
        if (!$user) {
            $user = new User();
            $title = "Add User";
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {

            if ($form->isSubmitted() and $form->isValid()) {

                $message = "User Updated!";
                // if new user default password will cellphone
                if (!$user->getId()) {
                    $user->setPassword($passwordHasher->hashPassword($user, $request->get('user')['cellphone']));
                    $message = "User Added!";
                }
                $em->persist($user);
                $em->flush();
                $request->getSession()->getFlashBag()->add("successmsg", $message);
                return $this->redirect($this->generateUrl('web_admin_list_user'));
            }
        }
        return $this->render('admin/manage_user.html.twig', [
            'title' => $title,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('admin/reset/password/{id}', name: 'web_reset_password')]
    public function resetPassword(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, $id): Response
    {
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(["id" => $id]);
        $user->setPassword($passwordHasher->hashPassword($user, $request->get('password')));
        $em->persist($user);
        $em->flush();
        $request->getSession()->getFlashBag()->add("successmsg", "Password Changed!");
        return $this->redirect($this->generateUrl('web_admin_manage_user', ['id' => $id]));
    }

    #[Route('/admin/list/notice', name: 'web_admin_list_notice')]
    public function listNotice(ManagerRegistry $doctrine): Response
    {
        $notices = $doctrine->getRepository(Notice::class)->findAll();
        return $this->render('admin/list_notice.html.twig', [
            "notices" => $notices,
            "title" => 'List Notice'
        ]);
    }

    #[Route('/admin/manage/notice/{id}', name: 'web_admin_manage_notice')]
    public function manageNotice(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, $id = -1): Response

    {
        $title = "Update Notice";
        $message = "Notice Updated!";
        $em = $doctrine->getManager();
        $notice = $doctrine->getRepository(Notice::class)->findOneBy(["id" => $id]);
        if (!$notice) {
            $notice = new Notice();
            $title = "Add Notice";
            $message = "Notice Created!";
        }

        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);
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
                $newFilename = uniqid() . '_' . $Filename . '.' . $upload->guessExtension();

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
                $em->persist($notice);
                $em->flush();

                $request->getSession()->getFlashBag()->add("successmsg", $message);
                return $this->redirect($this->generateUrl('web_admin_list_notice'));
            } elseif(!$upload) {
                $em->persist($notice);
                $em->flush();
                $request->getSession()->getFlashBag()->add("successmsg", $message);
                return $this->redirect($this->generateUrl('web_admin_list_notice'));
            } else {
                $request->getSession()->getFlashBag()->add("errormsg", "something went wrong!!");
                return $this->redirect($this->generateUrl('web_admin_list_notice'));
            }
        }

        return $this->render('admin/manage_notice.html.twig', [
            'title' => $title,
            'notice' => $notice,
            'form' => $form->createView()
        ]);
    }

    #[Route('admin/update/notice/{id}', name: 'web_update_notice')]
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

    #[Route('/view/notice', name: 'web_all_notice')]
    public function viewNotice(ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        if ($this->isGranted('ROLE_ADMIN')) {

            $query = $em->createQuery("SELECT u from App:Notice u");
            $query->getResult();
            $num = "1";
        } else {
            $today = new \DateTime();
            $query = $em->createQuery("SELECT u from App:Notice u where u.noticeTo > :today and u.status ='active'");
            $query->setParameter('today', $today);
            $num = "0";
        }

        return $this->render('admin/list_notice.html.twig', [
            "notices" => $query->getResult(),
            "value" => $num
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

    #[Route('admin/add_book', name: 'web_add-book')]
    public function add_book(Request $request, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        if ($request->getMethod() == "POST") {
            $book = new LibraryBook();
            $book->setTitle($request->get('title'));
            $book->setAuthor($request->get('author'));
            $book->setPublisher($request->get('publisher'));
            $book->setEdition($request->get('edition'));
            $book->setAvailableBook($request->get('no_of_book'));



            $em->persist($book);
            $em->flush();
        }


        return $this->render('admin/add-book.html.twig', []);
    }

    #[Route('admin/list/book', name: 'web_admin_list_book')]
    public function listbook(ManagerRegistry $doctrine): Response
    {
        $books = $doctrine->getRepository(LibraryBook::class)->findAll();
        return $this->render('admin/list_book.html.twig', [
            "books" => $books
        ]);
    }

    #[Route('admin/edit/book/{id}', name: 'web_edit-book')]
    public function edit_book(Request $request, ManagerRegistry $doctrine, $id): Response
    {

        $em = $doctrine->getManager();
        $book = $doctrine->getRepository("App\Entity\LibraryBook")->findOneBy(["id" => $id]);


        $title = $book->getTitle();
        $author = $book->getAuthor();
        $publisher = $book->getPublisher();
        $edition = $book->getEdition();
        $no_of_book = $book->getAvailableBook();




        if ($request->getMethod() == "POST") {

            $book->setTitle($request->get('title'));
            $book->setAuthor($request->get('author'));
            $book->setPublisher($request->get('publisher'));
            $book->setEdition($request->get('edition'));
            $book->setAvailableBook($request->get('no_of_book'));



            $em->persist($book);
            $em->flush();

            return $this->redirect($this->generateUrl('web_admin_list_book'));
        }


        return $this->render('admin/update_book.html.twig', [

            'Title' => $title,
            'Author' => $author,
            'Publisher' => $publisher,
            'Edition' => $edition,
            'No_of_book' => $no_of_book,

        ]);
    }

    #[Route('admin/web/ajax', name: 'web_ajax_user')]
    public function ajaxUser(Request $request, ManagerRegistry $mr, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $id = $request->get('id');
        $user = $mr->getRepository("App\Entity\User")->findOneBy(["id" => $id]);
        if (!$user) {
            $user = new User();


            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);
            if ($request->getMethod() == "POST") {


                if ($form->isSubmitted() and $form->isValid()) {

                    $em = $mr->getManager();

                    $plainPassword = $request->get('password');
                    $user->setDepartment($request->get('Dept'));
                    $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                    $em->persist($user);
                    $em->flush();
                }
            }
        } else {

            $form = $this->createForm(UserType::class, $user);
            if ($request->getMethod() == "POST") {
                $user->setDepartment($request->get('Dept'));
                $form->handleRequest($request);
                $em = $mr->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        $users = $mr->getRepository("App\Entity\User")->findAll();
        $html = $this->renderView('admin/get_user_list.html.twig', [
            'title' => "manage User",
            'form' => $form->createView(),
            'id' => $id,
            'users' => $users,

        ]);

        $response = new JsonResponse();
        $response->setData($html);
        return $response;
    }
    #[Route('admin/web/ajax/edit/abc', name: 'web_ajax_get_user_form')]

    public function getUserForm(Request $request, ManagerRegistry $mr): JsonResponse
    {
        $response = new JsonResponse();
        $id = $request->get('id');
        $user = $mr->getRepository('App\Entity\User')->findOneBy(["id" => $id]);
        $dept = $user->getDepartment();
        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('manage_user', ['id' => $id]),
        ]);
        $html = $this->renderView('admin/get_user_form.html.twig', [
            'title' => "Edit User",
            'form' => $form->createView(),
            'id' => $id,
            'dept' => $dept
        ]);
        $response->setData($html);
        return $response;
    }
}
