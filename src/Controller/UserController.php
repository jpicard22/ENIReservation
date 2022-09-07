<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Entity\Campus;
use App\Form\UserType;
use App\Form\ProfilUserType;
use App\Entity\UploadUserCSV;
use App\Form\UploadPictureType;
use App\Form\UploadUserCSVType;
use App\Repository\UserRepository;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $userRepository;
    // encoder nvx mdp
    private $encoder;
    private $em;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
        $this->em = $em;
    }

   /**
     * @Route("/mon_profil", name="profil")
     */
    public function profil(CampusRepository $campusRepository, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // afficher campus dans form
        $allCampus = $campusRepository->findAll();
        // $users = $this->userRepository->findAll();  
        $user = $this->getUser();      
        // dd($user->getId());
        // foreach($users as $user){
            $user = $userRepository->findBy(['id' =>$user->getUserIdentifier()]);
            //dump($allCampus);

            // si click sur valider
            if ($request->isMethod('POST')) {

                // récupère tous les champs dans un tableaux
                //dump($request->request);

                // $user = mail du user actuel
                $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

                // on récupère un champ à la fois, util pour maj 1 info du profil
                $user->setNom($request->request->get('nom'));
                $user->setTel($request->request->get('tel'));
                $user->setPrenom($request->request->get('prenom'));

                // récupère id campus
                $campusId = $request->request->get('campus');
                $campusEntity = null;
                foreach ($allCampus as $campus){
                    if ($campus->getId()==$campusId){
                        $campusEntity = $campus;
                    }
                }
                // si le campus est différent de vide ok maj,
                if ($campusEntity != '') {
                    $user->setCampus($campusEntity);
                }

                $errorNotUnique = false;

                // si il trouve user avec cet email = null  alors ok pour maj email
                if (($userRepository->findOneBy(['email' => $request->request->get('email')]) == null) || ($request->request->get('email') == $user->getEmail())) {
                    $user->setEmail($request->request->get('email'));
                } else {
                    $errorNotUnique = true;
                    $this->addFlash('error', "Cette adresse est déjà utilisée, veuillez en choisir une nouvelle.");
                }

                // si il ne trouve pas de user avec ce pseudo alors ok pour maj pseudo
                if (($userRepository->findOneBy(['pseudo' => $request->request->get('pseudo')]) == null) || ($request->request->get('pseudo') == $user->getPseudo())) {
                    $user->setPseudo($request->request->get('pseudo'));
                } else {
                    $errorNotUnique = true;
                    $this->addFlash('error', "Ce pseudo est déjà utilisé, veuillez en choisir un nouveau.");
                }

                // si mdp = confirmmdp et si mdp est différent de vide alors ok pour maj
                if ($request->request->get('password') == $request->request->get('confirmPassword') && $request->request->get('password') != '') {
                    $user->setPassword($this->encoder->encodePassword($user, $request->request->get('password')));
                    $this->addFlash('success', "Modification effectuée.");
                }else if ($request->request->get('password') == '') {  //si autres maj que password, ok msg confirm
                    $errorNotUnique ? " " : $this->addFlash('success', "Modification effectuée.");
                }else if ($request->request->get('password') != $request->request->get('confirmPassword')){
                    $this->addFlash('error', "Le mot de passe n'est pas correct.");
                }
                // execute la modif
                $entityManager->persist($user);
                $entityManager->flush();
            }
         
        // twig qui utilise cette méthode
        return $this->render('user/profil.html.twig', [
            'allCampus' => $allCampus,
            'user' => $user
        ]);
    }

    /**
     * @Route("/gestion-utilisateurs", name="gestionUtilisateurs")
     */
    public function list(Request $request): Response
    {
        $page = $request->get('page');
        $limit = 10;
        if ($page === null || $page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;

        if($request->get('search-txt') != ''){
            $users = $this->userRepository->findUserByName($request->get('search-txt'));
        }
        else{
            $users = $this->userRepository->findAll($limit, $offset);
        }

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/{id}/actif", name="userActif")
     */
    public function actifUser(User $user)
    {
        $this->em->getConnection();

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            $this->addFlash('attention' , "Cet utilisateur à un rôle admin, vous ne pouvez pas le désactiver");
            return $this->redirectToRoute('gestionUtilisateurs');
        }

        $user->setActif(!$user->getActif());
        $this->em->persist($user);
        $this->em->flush();

        $status = $user->getActif();
        if($user->getActif()){
            $status = "activé";
        }else{
            $status = "désactivé";
        }
        $this->addFlash('success', "L'utilisateur ".$user->getEmail()." a bien été $status");
        return $this->redirectToRoute('gestionUtilisateurs');
    }

    /**
     * @Route("/{id}", name="UserDelete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user, true);
        }

        return $this->redirectToRoute('gestionUtilisateurs', [], Response::HTTP_SEE_OTHER);
    }
     /**
     * Chargement d'une photo de profil
     *
     * @Route("/modification/photo", name="uploadPicture")
     */
    public function upload(Request $request): Response
    {
        $this->em->getConnection();
        $user = $this->getUser();
        // dd($users);

        $previousProfilePicture = $user->getPicture();
    
        $form = $this->createForm(UploadPictureType::class, $user);
        $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
    
                    // $folder = 'c:/temp/'; // emplacement de sauvegarde
                    // $dateTime = new \DateTime();
                    $fileOriginal = random_int(100, 100000); // nom du fichier unique

                    $newFile = $fileOriginal.'.'.$user->getPictureUpload()->guessExtension();
                   
                    $user->getPictureUpload()->move($this->getParameter('photoProfile'), $newFile);
    
                    $user->setPicture($newFile);
    
                    $user->setPictureUpload(null);
                    
                    $this->em->persist($user);
                    $this->em->flush();
    
                    if(!empty($newFile)){
                        $this->addFlash('success', 'Photo de profil ajoutée !');
                    }    
    
                    return $this->redirectToRoute('profil', ["id" => $user->getId()]);
                }
    
                $user->setPictureUpload(null);
            }

        return $this->render('user/upload.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/modele-csv-type", name="fichierCsvType")
     */
    public function uploadCSV(Request $request)
    {
        $titles = ["id","email","roles","password","prenom","pseudo","nom","tel","actif","campus_id"];

        $response = new Response(implode(", ", $titles));

        $response->headers->set('Content-Type', 'text/csv');
        
        $response->headers->set('Content-Disposition', 'attachment; filename="modele.csv"');

        return $response;
    }
}
