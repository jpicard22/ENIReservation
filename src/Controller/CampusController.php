<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @Route("/campus")
 */
class CampusController extends AbstractController
{
    private $campusRepository;

    public function __construct( 
        CampusRepository $campusRepository
        )
    {
        $this->campusRepository = $campusRepository;
    }

    /**
     * @Route("/", name="campusList", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $page = $request->get('page');
        $limit = 10;
        if ($page === null || $page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;

        return $this->render('campus/index.html.twig', [
            'campus' => $this->campusRepository->findAll($limit, $offset),
        ]);
    }

    /**
     * @Route("/new", name="app_campus_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campusRepository->add($campus, true);

            return $this->redirectToRoute('campusList', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/", name="campus_name", methods={"POST"})
     */
    public function recherche(Request $request): Response
    {
        $page = $request->get('page');
        $limit = 10;
        if ($page === null || $page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;
        
        if($request->get('search-txt') != ''){
            $allCampus = $this->campusRepository->findCampusByNom($request->get('search-txt'));
        }
        else{
            $allCampus = $this->campusRepository->findAll($limit, $offset);
        }

        return $this->render('campus/index.html.twig', [
            'campus' => $allCampus
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_campus_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Campus $campus): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->campusRepository->add($campus, true);

            return $this->redirectToRoute('campusList', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_campus_delete", methods={"POST"})
     */
    public function delete(Request $request, Campus $campus): Response
    {
     
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
           $this->campusRepository->remove($campus, true);
        }

        return $this->redirectToRoute('campusList', [], Response::HTTP_SEE_OTHER);
    }
}
