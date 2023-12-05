<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\EntrepriseRepository;

class OcrController extends AbstractController
{
    #[Route('/ocr', name: 'app_ocr')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        // selection de la premiere entreprise non valide (matricule et annee)
        $entreprise= $entrepriseRepository->findFirstInvalid();
        $matricule=$entreprise->getMatricule();
        $annee=$entreprise->getAnnee();

        
        return $this->render('ocr/index.html.twig', [
            'controller_name' => 'OcrController',
        ]);
    }
    #[Route('/getuser', name: 'get_users')]
    public function getALL(ManagerRegistry $doctrine)
    {
        
        $result_ocr_Repository = $doctrine->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findBy(['matricule'=>'0002388V','annee'=>2020,'page'=>2]);
        $data = [];
        foreach ($result_ocrs as $key => $result_ocr) {
            $data[] = [
                'id' => $result_ocr->getId(),
                'label' => $result_ocr->getLabel(),
                'notes' => $result_ocr->getNotes(),
                'value_n' => $result_ocr->getValueN(),
                'value_n1' => $result_ocr->getValueN1(),
               
                'type_page' => $result_ocr->getTypePage(),
            ];
        }
        $data=['data'=>$data];
        //dd($data);
        return $this->json($data);
        //return new JsonResponse($data);
    }
###################################################################################################################################
    // Mise a jour de label
##################################################################################################################################
    #[Route('/updatelabel', name: 'update_label')]
    public function updateLabel(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $resultocr = $entityManager->getRepository(ResultOcr::class)->find($id);

        if (!$resultocr) {
            throw $this->createNotFoundException(
                'No ligne found for id '.$id
            );
        }

        $resultocr->setLabel($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('app_ocr', [
            'id' => $resultocr->getId()
        ]);
    }

    ###################################################################################################################################
    // Mise a jour de notes
    ###################################################################################################################################
    #[Route('/updateNotes', name: 'update_notes')]
    public function updateNotes(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $resultatocr = $entityManager->getRepository(ResultOcr::class)->find($id);

        if (!$resultatocr) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $resultatocr->setNotes($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('app_ocr', [
            'id' => $resultatocr->getId()
        ]);
    }

    ###################################################################################################################################
    // Mise a jour de annee n
    ###################################################################################################################################
    #[Route('/updateAnneeN', name: 'update_annee_n')]
    public function updateAnneeN(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $resultatocr = $entityManager->getRepository(ResultOcr::class)->find($id);

        if (!$resultatocr) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $resultatocr->setValueN($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('app_ocr', [
            'id' => $resultatocr->getId()
        ]);
    }
    ###################################################################################################################################
    // Mise a jour de annee n-1
    ###################################################################################################################################
    #[Route('/updateAnneeN1', name: 'update_annee_n1')]
    public function updateAnneeN1(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $resultatocr = $entityManager->getRepository(ResultOcr::class)->find($id);

        if (!$resultatocr) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $resultatocr->setValueN1($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('app_ocr', [
            'id' => $resultatocr->getId()
        ]);
    }
    
     ###################################################################################################################################
    // Mise a jour de type page
    ###################################################################################################################################
    #[Route('/updateTypePage', name: 'update_type_page')]
    public function updateTypePage(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $resultatocr = $entityManager->getRepository(ResultOcr::class)->find($id);

        if (!$resultatocr) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        
        $resultatocr->setTypePage($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('app_ocr', [
            'id' => $resultatocr->getId()
        ]);
    }
    }

