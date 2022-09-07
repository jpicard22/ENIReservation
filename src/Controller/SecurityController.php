<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(
        AuthenticationUtils $authenticationUtils, 
        UserRepository $userRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
       
        
        $user = $userRepository->findOneBy(['email' => $authenticationUtils->getLastUsername()]);
       
        if($user !== null){
            if (!$user->getActif()){
                $this->addFlash('error', "Votre compte a été désactivé, veuillez contactez un administrateur");
            }else{
                if ($error){
                    $this->addFlash('error', "Identifiant ou mot de passe incorrect, veuillez réessayer.");
                }
            }
        }
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'accesDenied' => false]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
}
