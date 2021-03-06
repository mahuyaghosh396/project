<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\LibraryBook;
use App\Entity\Notice;
use App\Entity\User;
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
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'title' => 'Admin Dashboard',
        ]);
    }

    #[Route('/admin/manage/department/{id}', name: 'admin_department')]
    public function department(Request $request, ManagerRegistry $mr, $id = -1): Response
    {

        $dept = $mr->getRepository("App\Entity\Department")->findOneBy(["id" => $id]);
       
        if (!$dept) {
            $dep = new Department();
            $name = " ";
            $code = " ";
        } elseif($dept){
            $name = $dept->getName();
            $code = $dept->getCode();
        }
        
        

        if ($request->getMethod() == "POST") {
            $em = $mr->getManager();
            $dep->setName($request->get("dept"));
            $dep->setCode($request->get("code"));

            $em->persist($dep);
            $em->flush();
            $request->getSession()->getFlashBag()->add("successmsg", "Department added");
            return $this->redirect($this->generateUrl('admin_department'));
        }
        return $this->render('admin/department.html.twig', [
            'title' => 'Add Department',
            'name' => $name,
            'code' => $code
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
        $dept = $mr->getRepository("App\Entity\Department")->findAll();
        if (!$user) {
            $user = new User();
            $title = "Add User";
        }

        $form = $this->createForm(UserType::class, $user);
        $ab = $form->handleRequest($request);
        if ($request->getMethod() == "POST") {

            if ($form->isSubmitted() and $form->isValid()) {

                $message = "User Updated!";
                // if new user default password will cellphone
                if (!$user->getId()) {
                    $user->setPassword($passwordHasher->hashPassword($user, $request->get('user')['cellphone']));
                    $message = "User Added!";
                }
                $user->setDepartment($request->get("dept"));
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
            'dept' => $dept,
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
            } elseif (!$upload) {
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

    #[Route('/admin/add_book', name: 'web_add-book')]
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
            $request->getSession()->getFlashBag()->add("successmsg", "Book added!");
            return $this->redirect($this->generateUrl('web_admin_list_book'));
        }

        return $this->render('admin/add-book.html.twig', [
            'title' => 'Add Book'
        ]);
    }

    #[Route('/admin/list/book', name: 'web_admin_list_book')]
    public function listbook(ManagerRegistry $doctrine): Response
    {
        $books = $doctrine->getRepository(LibraryBook::class)->findAll();
        return $this->render('admin/list_book.html.twig', [
            "books" => $books,
            'title' => 'List Book'
        ]);
    }

    #[Route('/admin/edit/book/{id}', name: 'web_edit-book')]
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
            $request->getSession()->getFlashBag()->add("successmsg", "Book updated!");
            return $this->redirect($this->generateUrl('web_admin_list_book'));
        }


        return $this->render('admin/update_book.html.twig', [

            'title' => "Edit Book",
            'Title' => $title,
            'Author' => $author,
            'Publisher' => $publisher,
            'Edition' => $edition,
            'No_of_book' => $no_of_book,

        ]);
    }
}
