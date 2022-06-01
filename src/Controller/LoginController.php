<?php

namespace App\Controller;

use App\Entity\StudentRegis;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\BinaryOp\Equal;
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
    public function regis(Request $request, ManagerRegistry $doctrine,UserPasswordHasherInterface $passwordHasher): Response
    {

        $msg = " ";
        $em = $doctrine->getManager();

        if ($request->get('password') == $request->get('confirmpassword')) {

            $msg = "confirm password and password should match!!!";
        }

        if ($request->getMethod() == "POST") {
            $regis = new User();
            $regis->setLastName($request->get('lastname'));
            $regis->setEmail($request->get('email'));
            $regis->setRoles($request->get('role'));
            $regis->setCellphone($request->get('tel'));
            $regis->setRollNumber($request->get('roll_no'));
            $regis->setRegistrationNumber($request->get('reg_no'));
            $regis->setDepartment($request->get('Dept'));
            $regis->setDob($request->get('DOB'));
            $regis->setDepartment($request->get('Dept'));
            

            $plainPassword=$request->get('password');

            $regis->setPassword($passwordHasher->hashPassword($regis,$plainPassword));

            $em->persist($regis);
            $em->flush();
        }

        return $this->render('/login/signup.html.twig', []);
    }

    #[Route('/login/admissionLogin', name: 'admission_Login')]
    public function Login(Request $request, ManagerRegistry $doctrine): Response
    {
        // $msg = "";

        // if ($request->getMethod() == "POST") {
        //     $em = $doctrine->getManager();
        //     $email = $request->get('email');
        //     $password = $request->get('password');
        //     // dd("SELECT u from App:StudentRegis u where u.email= '$email' and u.password ='$password'  ");

        //     // $code=$doctrine->getRepository('App:StudentRegis')->findBy( ['email'=>$request->get('email')] && ['password'=>$request->get('password') ]);
        //     $query = $em->createQuery("SELECT u from App:StudentRegis u where u.email= '$email' and u.password ='$password'  ");
        //     $result =  $query->getResult();
        //     dd($result);

        //     if (count($result) > 0) {

        //         return $this->redirect($this->generateUrl('manage_admission'));
        //     } else
        //         $msg = "invalid credential";
        // }

        return $this->render('/login/admissionLogin.html.twig', []);
    }
}
