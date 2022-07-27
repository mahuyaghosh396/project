<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserprofileType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/my/account/', name: 'web_my_account')]
    public function myAccount(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {
        $id = $this->getUser();
        $user = $doctrine->getRepository(User::class)->findOneBy(["id" => $id]);
        $em = $doctrine->getManager();
        $query = $em->createQuery("SELECT u from App:Department u where  u.status ='Active'");
        $result = $query->getResult();


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
        $form = $this->createForm(UserprofileType::class, $user);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {

            if ($form->isSubmitted() and $form->isValid()) {

                /** @var UploadedFile $upload */
                $upload = $form->get('profilePic')->getData();

                if ($upload) {

                    $originalFilename = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
                    $Filename = $slugger->slug($originalFilename);

                    $newFilename = uniqid() . '_' . $Filename . '.' . $upload->guessExtension();

                    //move uploaded file....
                    try {
                        $upload->move(
                            $this->getParameter('profile_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $user->setProfilePic($newFilename);
                }
                $dept = $doctrine->getRepository("App\Entity\Department")->findOneBy(["name" => $request->get("dept")]);
                $user->setDepartment($dept);
                $user->setEmail($user->getEmail());
                $em->persist($user);
                $em->flush();
                $request->getSession()->getFlashBag()->add("successmsg", "Updated successfully!!");
                return $this->redirect($this->generateUrl('web_my_account', ['id' => $this->getUser()]));
            }
        }
        return $this->render('user/my_acount.html.twig', [
            'title' => "My-Account",
            'user' => $user,
            'form' => $form->createView(),
            'dept' => $result,
            'value' => $value,

        ]);
    }

    #[Route('reset/password/', name: 'web_user_reset_password')]
    public function resetPassword(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();
        $id = $this->getUser();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(["id" => $id]);
        $current_password = $request->get('current_password');

        if ($passwordHasher->isPasswordValid($user, $current_password)) {

            if ($request->get('new_password') == $request->get('con_password')) {
                $user->setPassword($passwordHasher->hashPassword($user, $request->get('new_password')));
                $em->persist($user);
                $em->flush();
                $request->getSession()->getFlashBag()->add("successmsg", "Password Changed!");
                return $this->redirect($this->generateUrl('web_my_account'));
            } else {
                $request->getSession()->getFlashBag()->add("errormsg", "Mismatched new & confirmed password");
                return $this->redirect($this->generateUrl('web_my_account'));
            }
        } else {
            $request->getSession()->getFlashBag()->add("errormsg", "Current Password is wrong!!");
            return $this->redirect($this->generateUrl('web_my_account'));
        }
    }
}
