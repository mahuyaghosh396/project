<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/add/user', name: 'app_admin_add_user')]
    public function addUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();
        $user = new User();
        $user->setFirstName('dev');
        $user->setLastName('admin');
        $user->setEmail('admin@g.c');
        $user->setCellphone('12345');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($passwordHasher->hashPassword($user, "1234"));
        $em->persist($user);
        $em->flush();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/manage/notice', name: 'app_admin_manage_notice')]
    public function manageNotice(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        
        if ($request->getMethod() == "POST") {
            $notice = new Notice();
            $notice->setName($request->get('name'));
            $notice->setNoticeFrom(new \DateTime($request->get('notice_from')));
            $notice->setNoticeTo(new \DateTime($request->get('notice_to')));

            $em->persist($notice);
            $em->flush();
        }

        return $this->render('admin/manage_notice.html.twig', []);
    }

    #[Route('/admin/list/notice', name: 'app_admin_list_notice')]
    public function listNotice(ManagerRegistry $doctrine): Response
    {        
        $notices = $doctrine->getRepository(Notice::class)->findAll();
        return $this->render('admin/list_notice.html.twig', [
            "notices" => $notices
        ]);
    }
}
