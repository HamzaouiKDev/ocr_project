<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use App\Form\TypePageType;
use App\Repository\ResultOcrRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypePageOcrRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OcrController extends AbstractController
{
   // Mise a jour du type de page
    #[Route('/update-type-page', name: 'update_type_page')]
    public function updateTypePage(Request $request, EntityManagerInterface $em):Response
    {
        // Lecture des variables session necessaire 
        
        $session = $request->getSession();
        $mat=$session->get('matricule');
        $ann=$session->get('annee');
        $page=$session->get('pageEnCours'); 
         
        $form = $this->createForm(TypePageType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ici, on récupère la nouvelle valeur choisie.
           $newAttributeValue = $form->get('attribute')->getData();

            // Ensuite, vous devez obtenir vos enregistrements d'une manière ou d'une autre.
            // Par exemple, on peut utiliser un repository pour obtenir les enregistrements à mettre à jour.
            $records = $em->getRepository(ResultOcr::class)->findBy(['matricule'=>$mat,'annee'=>$ann,'page'=>$page]);
            // Effectuer la mise à jour de l'attribut pour chaque enregistrement.
            foreach ($records as $record) {
                $record->setTypePage($newAttributeValue); // Remplacer 'setAttribute' par la méthode réelle.
            }
            
            // Flusher les changements dans la base de données.
            $em->flush();

            // Message flash ou autre traitement après la mise à jour.

           
        }
        return $this->redirectToRoute('app_ocr'); // Redirigez vers une route de succès.
       
    }
    #[Route('/ocr', name: 'app_ocr')]
    public function index(EntrepriseRepository $entrepriseRepository,TypePageOcrRepository $typePageOcrRepository,Request $request): Response
    {
        $form = $this->createForm(TypePageType::class);
        $form->handleRequest($request);
        
        // selection de la premiere entreprise non valide (matricule et annee)
        $entreprise= $entrepriseRepository->findFirstInvalid();
        $matricule=$entreprise->getMatricule();
        $annee=$entreprise->getAnnee();

        // recuperer un tableau de pages du bilan

        $typePage=$typePageOcrRepository->findPages($matricule,$annee);
        foreach($typePage as $ligne)
        {
            $pages[]=$ligne->getPage();
        }
        $nbrPages=count($pages);
        
     
        // Passer les variable en session

        $session = $request->getSession();
        $session->set('matricule',$matricule);
        $session->set('annee',$annee);
        $session->set('pages',$pages);
        $session->set('pageEnCours',$pages[0]); 
        $session->set('pageEnCoursIndex',0); 
        $session->set('nbrPages',$nbrPages); 
        $pdfUrl="pdf/pdf_file.pdf";
        return $this->render('ocr/index.html.twig',['pdfUrl' =>$pdfUrl, 'form' => $form->createView(),]);
    }
// call page

public function callPage(EntrepriseRepository $entrepriseRepository,TypePageOcrRepository $typePageOcrRepository,Request $request): Response
{
    $session = $request->getSession();
    $page=$session->get('pages',$pages);
    $session->set('pages',$pages);

   
    return $this->render('ocr/index.html.twig', [
        'matricule' => $matricule,
        'annee'=> $annee,
        'pages'=>$pages,
        'nbrPages'=>$nbrPages
    ]);
}


  // recuperer ocrResult par matricule et annee
  
  #[Route('/getLigneOcr/{matricule}/{annee}', name: 'ligne_ocr_show')]
  public function getLigneOcr(ManagerRegistry $doctrine, string $matricule, string $annee)
    {
        $page=6;
        $result_ocr_Repository = $doctrine->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findBy(['matricule'=>'0002388V','annee'=>$annee,'page'=>$page]);
        dd($result_ocrs);
        $data = [];
        foreach ($result_ocrs as $key => $result_ocr) {
            $data[] = [
                'id' => $result_ocr->getId(),
                'code'=>  $result_ocr->getCode(),
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
    // Acceder a la page suivante
##################################################################################################################################

#[Route('/suivant', name: 'get_page_suivante')]
public function suivante(ManagerRegistry $doctrine,Request $request)
{
    $form = $this->createForm(TypePageType::class);
    $form->handleRequest($request);
    $session = $request->getSession();
    $index=$session->get('pageEnCoursIndex');
    $pages=$session->get('pages'); 
    $index++;
    $pageencours=$pages[$index];
    $session->set('pageEnCoursIndex',$index);
    $session->set('pageEnCours',$pageencours);
    return $this->render('ocr/index.html.twig', [
        'form' => $form->createView(),
    ]);
    
}

###################################################################################################################################
    // Acceder a la page precedente
##################################################################################################################################

#[Route('/precedent', name: 'get_page_precedente')]
public function precedente(ManagerRegistry $doctrine,Request $request)
{
    $form = $this->createForm(TypePageType::class);
    $form->handleRequest($request);
    $session = $request->getSession();
    $index=$session->get('pageEnCoursIndex');
    $pages=$session->get('pages'); 
    $index--;
    $pageencours=$pages[$index];
    $session->set('pageEnCoursIndex',$index);
    $session->set('pageEnCours',$pageencours);
    return $this->render('ocr/index.html.twig', [
        'form' => $form->createView(),
    ]);
    
}

###################################################################################################################################
    //Fonction pour recuperer une page
##################################################################################################################################

    #[Route('/getpage', name: 'get_page')]
    public function getPage(ManagerRegistry $doctrine,Request $request)
    {
        $session = $request->getSession();
        $index=$session->get('pageEnCoursIndex');
        $pages=$session->get('pages');
        $pageenCours=$pages[$index];
        $result_ocr_Repository = $doctrine->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findBy(['matricule'=>$session->get('matricule'),'annee'=>$session->get('annee'),'page'=>$pageenCours]);
        $data = [];
        foreach ($result_ocrs as $key => $result_ocr) {
            $data[] = [
                'id' => $result_ocr->getId(),
                'code' => $result_ocr->getCode(),
                'label' => $result_ocr->getLabel(),
                'notes' => $result_ocr->getNotes(),
                'value_n' => $result_ocr->getValueN(),
                'value_n1' => $result_ocr->getValueN1(),
                'type_page' => $result_ocr->getTypePage(),
            ];
        }
        $data=['data'=>$data];
        return $this->json($data);
        
    }
###################################################################################################################################
    // Mise a jour de code
##################################################################################################################################
#[Route('/updatecode', name: 'update_code')]
public function updateCode(ManagerRegistry $doctrine) :Response
{
    $id= $_POST['pk'];
    $entityManager = $doctrine->getManager();
    $resultocr = $entityManager->getRepository(ResultOcr::class)->find($id);

    if (!$resultocr) {
        throw $this->createNotFoundException(
            'No ligne found for id '.$id
        );
    }

    $resultocr->setCode($_POST["value"]);
    $entityManager->flush();

    return $this->redirectToRoute('app_ocr', [
        'id' => $resultocr->getId()
    ]);
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
   
    }

