<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OcrController extends AbstractController
{
    #[Route('/ocr', name: 'app_ocr')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        // selection de la premiere entreprise non valide (matricule et annee)
        $entreprise= $entrepriseRepository->findFirstInvalid();
        $matricule=$entreprise->getMatricule();
        $annee=$entreprise->getAnnee();

        // recuperer un tableau de page traite de bilan

        
       
        return $this->render('ocr/index.html.twig', [
            'matricule' => $matricule,
            'annee'=> $annee
        ]);
    }

  // recuperer ocrResult par matricule et annee
  
  #[Route('/getLigneOcr/{matricule}/{annee}', name: 'ligne_ocr_show')]
  public function getLigneOcr(ManagerRegistry $doctrine, string $matricule, string $annee)
    {
        
        $result_ocr_Repository = $doctrine->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findBy(['matricule'=>$matricule,'annee'=>$annee]);
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






    #[Route('/getuser', name: 'get_users')]
    public function getALL(ManagerRegistry $doctrine,Request $request)
    {
     
        $mat = $request->request->get('param1');
        $annee = $request->request->get('param2');
        
        $var="0889422E";
        $result_ocr_Repository = $doctrine->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findBy(['matricule'=>$mat,'annee'=>$annee,'page'=>2]);
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

