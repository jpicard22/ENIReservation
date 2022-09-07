<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieFormType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("/creation", name="app_sortie_creation", methods={"GET", "POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etat = $request->request->get("etat");

            switch ($etat) {
                case 'Enregistrer':
                    $result = $etatRepository->findByLibelle("créée");
                    $sortie->setEtat($result[0]);
                    break;
                case 'Publier' :
                    $result = $etatRepository->findByLibelle("ouverte");
                    $sortie->setEtat($result[0]);
                    break;
            }

            $sortie->setNbUserCurrent(0);
            $sortie->setOrganisateur($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute("app_template");
        }


        return $this->render('sortie/index.html.twig', [
            'sortie_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="app_sortie_info")
     */
    // Sortie $sortie, permet de recup id
    public function afficher(Sortie $sortie, LieuRepository $lieuRepository, VilleRepository $villeRepository, CampusRepository $campusRepository, UserRepository $userRepository): Response{

        // renvoit l'entité directement
        $lieu = $lieuRepository->findOneBy(['id' => $sortie->getLieu()->getId()]);
        $ville = $villeRepository->findOneBy(['id' => $lieu->getVille()->getId()]);

        $lieu->setVille($ville);
        $sortie->setLieu($lieu);

        $campus = $campusRepository->findOneBy(['id' => $sortie->getSiteOrganisateur()->getId()]);
        $sortie->setSiteOrganisateur($campus);

        // envoi des infos pour l'affichage
        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/annuler/{id}", name="app_sortie_annuler", methods={"GET", "POST"})
     */
    public function annuler(Sortie $sortie, EntityManagerInterface $entityManager, LieuRepository $lieuRepository, VilleRepository $villeRepository, EtatRepository $etatRepository, CampusRepository $campusRepository, Request $request){

        if($request->getMethod() == 'POST') {
            $motif = $request->request->get("motif");
            $etat = $etatRepository->findOneBy(["libelle" => 'annulée']);

            $sortie->setEtat($etat);
            $sortie->setMotifAnnulation($motif);

            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute("app_template");
        }

        $lieu = $lieuRepository->findOneBy(['id' => $sortie->getLieu()->getId()]);
        $ville = $villeRepository->findOneBy(['id' => $lieu->getVille()->getId()]);
        $campus = $campusRepository->findOneById($sortie->getSiteOrganisateur()->getId());

        $lieu->setVille($ville);
        $sortie->setSiteOrganisateur($campus);
        $sortie->setLieu($lieu);

        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/creation/data", name="app_sortie_data", methods={"POST"})
     */
    public function sendData(Request $request, LieuRepository $lieuRepository, VilleRepository $villeRepository) {

        // On recupere l'id du lieu
        $id = $request->request->get('id');

        // On va chercher le lieu en question
        $res = $lieuRepository->findById($id);
        $idVille = $res[0]->getVille()->getId();
        // On va chercher la ville associé
        $resVille = $villeRepository->findById($idVille);

        $ville = new Ville();
        $ville->setNom($resVille[0]->getNom() );
        $ville->setCodePostal($resVille[0]->getCodePostal() );

        $lieu = new Lieu();
        $lieu->setRue($res[0]->getRue());
        $lieu->setLatitude($res[0]->getLatitude());
        $lieu->setLongitude($res[0]->getLongitude());
        $lieu->setVille( $ville );

        // on formate en JSON
        $data = $this->json($lieu);

        return $data;
    }

    /**
     * @Route("/add/{id}/{userId}", name="app_sortie_add_user", methods={"GET", "POST"})
     */
    public function app_sortie_add_user($id,$userId,SortieRepository $sortieRepository,UserRepository  $userRepository,EntityManagerInterface $em){
        $sortieAdd = $sortieRepository->findOneBy(["id"=>$id])->addUser($userRepository->findOneBy(['id' => $userId]));
        $sortieAdd->setNbUserCurrent($sortieAdd->getNbUserCurrent() + 1);
        $em->persist($sortieAdd);
        $em->flush();
        return $this->redirectToRoute('app_template');
    }

    /**
     * @Route("/remove/sortie/{id}", name="app_sortie_remove_sortie", methods={"GET", "POST"})
     */
    public function app_sortie_remove_sortie($id,SortieRepository $sortieRepository,EtatRepository  $etatRepository,EntityManagerInterface $em){
        $sortieRemove = $sortieRepository->findOneBy(["id"=>$id]);
        $em->remove($sortieRemove);
        $em->flush();
        return $this->redirectToRoute('app_template');
    }

    /**
     * @Route("/remove/{id}/{userId}", name="app_sortie_remove_user", methods={"GET", "POST"})
     */
    public function app_sortie_remove_user($id,$userId,SortieRepository $sortieRepository,UserRepository  $userRepository,EntityManagerInterface $em){
        $sortieAdd = $sortieRepository->findOneBy(["id"=>$id])->removeUser($userRepository->findOneBy(['id' => $userId]));
        $sortieAdd->setNbUserCurrent($sortieAdd->getNbUserCurrent() - 1);
        $em->persist($sortieAdd);
        $em->flush();
        return $this->redirectToRoute('app_template');
    }


}
