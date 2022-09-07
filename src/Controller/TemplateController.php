<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    /**
     * @Route("/template", name="app_template")
     */
    public function index(Request $request,SortieRepository $sortieRepository,CampusRepository $campusRepository): Response
    {
        $allSortie = $sortieRepository->findAll();
        $allCampus = $campusRepository->findAll();
        $result = [];
        $nbInscrit = [];



        if ($request->isMethod('POST')){
            dump($request->request);
            foreach ($allSortie as $sortie){
                    $value = true;
                    //Recherche par nom
                    if($request->request->get('sortie-name') != null && str_contains(strtolower($sortie->getNom()),strtolower($request->request->get('sortie-name')))){
                        //Recherche par Campus

                    }else{
                        if($request->request->get('sortie-name') != null){
                            $value = false;
                        }
                    }

                if($request->request->get('campus_choice') != '-1'  && str_contains(strtolower($sortie->getSiteOrganisateur()->getNom()),strtolower($request->request->get('campus_choice')))){

                }else{
                    if($request->request->get('campus_choice') != '-1'){
                        $value = false;
                    }

                }

                //Recherche par date de début
                if($request->request->get('date_end') != null && $request->request->get('date_start') != null && $sortie->getDateHeureDebut() >= date_create($request->request->get('date_start')) && $sortie->getDateHeureDebut() <= date_create($request->request->get('date_end'))){

                }else{
                    if($request->request->get('date_start') == null && $request->request->get('date_end') == null){

                    }else{
                        $value = false;
                    }
                }

                //Recherche si organisteur
                if($request->request->get('organisateur')){
                    if($this->getUser()->getUserIdentifier() != $sortie->getOrganisateur()->getEmail()){
                        $value = false;
                    }
                }

                //Recherche si inscrit
                if($request->request->get('inscrit')){
                    if($sortie->getUsers()->contains($this->getUser())){
                    }else{
                        $value = false;
                    }
                }

                //Recherche si pas inscrit
                if($request->request->get('notInscrit')){
                    dump($sortie->getUsers()->contains($this->getUser()->getUserIdentifier()));
                    if($sortie->getUsers()->contains($this->getUser())){
                        $value = false;
                    }else{
                    }
                }

                //Recherche si sortie passé
                $currentDateTime = date('Y-m-d H:i:s');
                if($request->request->get('sortie_passe')){
                    if($currentDateTime < $sortie->getDateHeureDebut()->format('Y-m-d H:i:s')){
                        $value = false;
                    }
                }


                if($value){
                    array_push($result,$sortie);
                }

            }
            dump($result);
            return $this->render('template/index.html.twig', [
                'controller_name' => 'TemplateController',
                'allCampus' => $allCampus,
                'allSortie' => $result
            ]);
        }else{

            dump($allSortie);
            return $this->render('template/index.html.twig', [
                'controller_name' => 'TemplateController',
                'allCampus' => $allCampus,
                'allSortie' => $allSortie,
                'nbInscrit' => $nbInscrit
            ]);
        }

    }
}
