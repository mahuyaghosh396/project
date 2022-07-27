<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\LibraryBook;
use App\Entity\Notice;
use App\Entity\User;
use App\Form\DepartmentType;
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
    // ---------------------------[[dashboard]]-----------------------------------------------------


    #[Route('/admin/dashboard', name: 'web_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/admin_dashboard.html.twig', [
            'title' => 'Admin Dashboard',
        ]);
    }


    //....................................[[manage user]]......................................


    #[Route('admin/manage/user/{id}', name: 'web_admin_manage_user')]
    public function manageUser(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, $id = -1): Response
    {
        $title = "Update User";
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->findOneBy(["id" => $id]);
        $query = $em->createQuery("SELECT u from App:Department u where  u.status ='Active'");
        $result = $query->getResult();


        if (!$user) {
            $user = new User();
            $title = "Add User";
            $value = "Select";
        } else {

            if ($user->getDepartment() == null) {
                $value = "Select";
                $query = $em->createQuery("SELECT u from App:Department u where  u.status ='Active'");
                $result = $query->getResult();
            } else {
                $dpt_id = $user->getDepartment()->getId();
                $dpt = $doctrine->getRepository("App\Entity\Department")->findOneBy(["id" => $dpt_id, "status" => 'Active']);
                $value = $dpt->getName();
                $name = $user->getDepartment()->getId();
                $query = $em->createQuery("SELECT u from App:Department u where u.status ='Active' and u.id != :dId");
                $query->setParameter('dId', $name);
                $result = $query->getResult();
            }
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

                $dept = $doctrine->getRepository("App\Entity\Department")->findOneBy(["name" => $request->get("dept")]);
                $user->setDepartment($dept);
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
            'dept' => $result,
            'value' => $value,
        ]);
    }

    #[Route('admin/list/user', name: 'web_admin_list_user')]
    public function listUser(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findAll();

        return $this->render('admin/list_user.html.twig', [
            'title' => "List User",
            'users' => $user,
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


    // ------------------------[[manage notice]]--------------------------------


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

    #[Route('/admin/list/notice', name: 'web_admin_list_notice')]
    public function listNotice(ManagerRegistry $doctrine): Response
    {
        $notices = $doctrine->getRepository(Notice::class)->findAll();
        return $this->render('admin/list_notice.html.twig', [
            "notices" => $notices,
            "title" => 'List Notice'
        ]);
    }


    // -------------------[[Manage Book]]------------------------


    #[Route('/admin/add_book', name: 'web_admin_add_book')]
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

    #[Route('/admin/edit/book/{id}', name: 'web_admin_edit_book')]
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


        return $this->render('admin/edit_book.html.twig', [

            'title' => "Edit Book",
            'Title' => $title,
            'Author' => $author,
            'Publisher' => $publisher,
            'Edition' => $edition,
            'No_of_book' => $no_of_book,

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



    //..........................[[ Manage Department ]]...............................


    #[Route('/admin/manage/department/{id}', name: 'web_admin_manage_department')]
    public function manageDepartment(Request $request, ManagerRegistry $doctrine, $id = -1): Response
    {

        $dept = $doctrine->getRepository("App\Entity\Department")->findOneBy(["id" => $id]);
        $message = "Department updated";
        $btn = "Update";

        if (!$dept) {
            $dept = new Department();
            $message = "Department added";
            $btn = "Add";
        }

        $form = $this->createForm(DepartmentType::class, $dept);
        $form->handleRequest($request);

        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {
                $em = $doctrine->getManager();
                $em->persist($dept);
                $em->flush();
                $request->getSession()->getFlashBag()->add("successmsg", $message);
                return $this->redirect($this->generateUrl('web_admin_list_department'));
            }
        }
        return $this->render('admin/manage_department.html.twig', [
            'title' => 'Manage Department',
            'form' => $form->createView(),
            'btn' => $btn
        ]);
    }

    #[Route('admin/list/department', name: 'web_admin_list_department')]
    public function listDepartment(ManagerRegistry $doctrine): Response
    {
        $dept = $doctrine->getRepository("App\Entity\Department")->findAll();
        return $this->render('admin/list_department.html.twig', [
            'title' => "List Department",
            'dept' => $dept,
        ]);
    }

    #[Route('admin/update/department/{id}', name: 'web_admin_toggle_department_status')]
    public function toggleDepartmentStatus(ManagerRegistry $doctrine, $id, Request $request): Response
    {

        $em = $doctrine->getManager();
        $dept = $doctrine->getRepository("App\Entity\Department")->findOneBy(["id" => $id]);
        if ($dept->getStatus() == "Active") {
            $dept->setStatus("Deleted");
            $em->persist($dept);
            $em->flush();
            $request->getSession()->getFlashBag()->add("successmsg", "Department Deleted!");
        } else {
            $dept->setStatus("Active");
            $em->persist($dept);
            $em->flush();
            $request->getSession()->getFlashBag()->add("successmsg", "Department Activated!");
        }
        return $this->redirect($this->generateUrl('web_admin_list_department'));
    }
}
