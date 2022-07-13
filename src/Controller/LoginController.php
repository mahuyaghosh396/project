<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    // #[Route('/login/signup', name: 'web_signup')]
    // public function signup(): Response
    // {
    //     return $this->render('/login/signup.html.twig', [
    //     ]);
    // }


    #[Route('/login/signup', name: 'web_signup')]
    public function regis(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {

        $msg = " ";
        $em = $doctrine->getManager();




        if ($request->getMethod() == "POST") {

            $email =  $request->get('email');

            $query = $em->createQuery("SELECT  u from App:User u where u.email= '$email' ");
            $result =  $query->getResult();

            if (count($result) == 1) {

               $request->getSession()->getFlashBag()->add("errormsg","Email already Exist!!!");
             
            }

            elseif ($request->get('password') !== $request->get('confirmpassword')) {

                $request->getSession()->getFlashBag()->add("errormsg","Password and Confirm password should match!!!");
               
            }
            
            else {
                $date = intval($request->get('DOB'));
                $regis = new User();
                $date = new DateTimeImmutable($request->get('DOB'));
                $regis->setLastName($request->get('lastname'));
                $regis->setFirstName($request->get('firstname'));
                $regis->setEmail($request->get('email'));
                $regis->setRoles(['ROLE_STUDENT']);
                $regis->setCellphone($request->get('tel'));
                $regis->setRollNumber($request->get('roll_no'));
                $regis->setRegistrationNumber($request->get('reg_no'));
                $regis->setDepartment($request->get('Dept'));
                $regis->setDob($date);
                $regis->setAcademicYear($request->get('year'));


                $plainPassword = $request->get('password');

                $regis->setpassword($passwordHasher->hashPassword($regis, $plainPassword));

                $em->persist($regis);
                $em->flush();

                $request->getSession()->getFlashBag()->add("successmsg","Congratulation you have register successfully !!!");
                
            }

            return $this->redirect($this->generateUrl('web_signup'));
        }
        return $this->render('/login/signup.html.twig', []);
    }

   


}
