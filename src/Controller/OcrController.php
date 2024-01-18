<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use App\Entity\Entreprise;
use App\Form\TypePageType;
use App\Entity\TypePageOcr;
use App\Repository\ResultOcrRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypePageOcrRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OcrController extends AbstractController
{
    private $security;
    
   
    public function __construct(Security $security)
    {
        $this->security = $security;
       
    }
###################################################################################################################################
  // Mise a jour du type de page
##################################################################################################################################
  
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
            $record = $em->getRepository(TypePageOcr::class)->findBy(['matricule'=>$mat,'annee'=>$ann,'page'=>$page]);
            // Effectuer la mise à jour de l'attribut pour chaque enregistrement.
           // dd($record);
                $record[0]->setLabelType($newAttributeValue); 
            
            
            // Flusher les changements dans la base de données.
            $em->flush();

            // Message flash ou autre traitement après la mise à jour.

           
        }
        return new Response('', Response::HTTP_NO_CONTENT);
       
    }
###################################################################################################################################
 // Controlleur de la page principale
##################################################################################################################################
    
    #[Route('/ocr', name: 'app_ocr')]
    public function index(EntrepriseRepository $entrepriseRepository,TypePageOcrRepository $typePageOcrRepository,Request $request,ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(TypePageType::class);
        $form->handleRequest($request);
        //Recuperer l'utlisateur en cours 
        $user = $this->security->getUser();
       // dd($user);
        // verifier si l'utlisateur en cours a un bilan non validé
      /*  $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('e')
            ->from('App\Entity\Entreprise', 'e')
            ->Where('e.login = :param1')
            ->andWhere('e.valide = :param')
            ->setParameter('param1',$user->getUsername())
            ->setParameter('param', false)
            ->setMaxResults(1)
            ->getQuery();
        
        $entreprise = $query->getOneOrNullResult();

       if($entreprise==NULL)

       {*/


        //Recuperer l'entreprise non attribué et non validé

        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('e')
            ->from('App\Entity\Entreprise', 'e')
            ->where('e.login IS NULL OR e.login= :empty')
            ->andWhere('e.valide = :param')
            ->setParameter('param', false)
            ->setParameter('empty', '')
            ->setMaxResults(1)
            ->getQuery();
        
        $entreprise = $query->getOneOrNullResult();


        
        // selection de la premiere entreprise non valide (matricule et annee)
       // $entreprise= $entrepriseRepository->findFirstInvalid();
        //dd($entreprise);
        $matricule=$entreprise->getMatricule();
        $annee=$entreprise->getAnnee();
        $url=$entreprise->getPath();
        
        $id=$entreprise->getId();

        //Attribution du bilan a l'utilisateur en cours
        $entityManager = $doctrine->getManager();
        $entrep = $entityManager->getRepository(Entreprise::class)->find($id);
    
        if (!$entrep) {
            throw $this->createNotFoundException(
                'No ligne found for id '.$id
            );
        }
    
        $entrep->setLogin($user);
        $entityManager->flush();
       
        $entityManager->persist($entrep);
        $entityManager->flush();
        
        // recuperer un tableau de pages du bilan

        $typePage=$typePageOcrRepository->findPages($matricule,$annee);
        foreach($typePage as $ligne)
        {
            $pages[]=$ligne->getPage();
        }
        //dd($pages);
        $nbrPages=count($pages);
        // recuperer le type de lapage en cours
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('App\Entity\TypePageOcr', 'p')
            ->where('p.matricule = :param1')
            ->andWhere('p.annee = :param2')
            ->setParameter('param1', $matricule)
            
             ->andWhere('p.page = :param3')
            
            ->setParameter('param2', $annee)
             ->setParameter('param3', $pages[0])
            ->setMaxResults(1)
            ->getQuery();
        
        $type = $query->getOneOrNullResult();
        //dd($type);
     
        // Passer les variable en session

        $session = $request->getSession();
        $session->set('typePage',$type->getLabelType());
        $session->set('matricule',$matricule);
        $session->set('annee',$annee);
        $session->set('pages',$pages);
        $session->set('pageEnCours',$pages[0]); 
        $session->set('pageEnCoursIndex',0); 
        $session->set('nbrPages',$nbrPages); 
        $session->set('url',$url); 
        $pdfUrl="pdf/pdf_file.pdf";
        
   /* }
    else
    {
        return new Response('', Response::HTTP_NO_CONTENT);  
    }*/
        return $this->render('ocr/index.html.twig',['pdfUrl' =>$pdfUrl, 'form' => $form->createView(),'page'=>$pages[0],'url'=>$url,]);
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
###################################################################################################################################
 // recuperer ocrResult par matricule et annee
##################################################################################################################################

  #[Route('/getLigneOcr/{matricule}/{annee}', name: 'ligne_ocr_show')]
  public function getLigneOcr(ManagerRegistry $doctrine, string $matricule, string $annee)
    {
        $page=6;
        $result_ocr_Repository = $doctrine->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findBy(['matricule'=>'0002388V','annee'=>$annee,'page'=>$page]);
        $data = [];
        foreach ($result_ocrs as $key => $result_ocr) {
            $data[] = [
                'id' => $result_ocr->getId(),
                'code'=>  $result_ocr->getCode(),
                'label' => $result_ocr->getLabel(),
                'notes' => $result_ocr->getNotes(),
                'value_n' => $result_ocr->getValueN(),
                'value_n1' => $result_ocr->getValueN1(),   
                
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
    $url=$session->get('url'); 
    
    if( $session->get('nbrPages')==$index+1 )
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
    else{
        $index++;
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('App\Entity\TypePageOcr', 'p')
            ->where('p.matricule = :param1')
            ->andWhere('p.annee = :param2')
            ->setParameter('param1', $session->get('matricule'))
            
             ->andWhere('p.page = :param3')
            
            ->setParameter('param2', $session->get('annee'))
             ->setParameter('param3', $session->get('pages')[$index])
            ->setMaxResults(1)
            ->getQuery();
        
        $type = $query->getOneOrNullResult();
        $session = $request->getSession();
        $session->set('typePage',$type->getLabelType());
    $pageencours=$pages[$index];
    $session->set('pageEnCoursIndex',$index);
    $session->set('pageEnCours',$pageencours);
    }
    return $this->render('ocr/index.html.twig', [
        'form' => $form->createView(),'page'=>$pageencours,'url'=>$url,
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
    $url=$session->get('url'); 
    if($index==0 )
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
    else{
        $index--;
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('App\Entity\TypePageOcr', 'p')
            ->where('p.matricule = :param1')
            ->andWhere('p.annee = :param2')
            ->setParameter('param1', $session->get('matricule'))
            
             ->andWhere('p.page = :param3')
            
            ->setParameter('param2', $session->get('annee'))
             ->setParameter('param3', $session->get('pages')[$index])
            ->setMaxResults(1)
            ->getQuery();
        
        $type = $query->getOneOrNullResult();
        $session = $request->getSession();
        $session->set('typePage',$type->getLabelType());
    
    $pageencours=$pages[$index];
    $session->set('pageEnCoursIndex',$index);
    $session->set('pageEnCours',$pageencours);
    }
    return $this->render('ocr/index.html.twig', [
        'form' => $form->createView(),
        'page'=>$pageencours,
        'url'=>$url,
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
    return new Response('', Response::HTTP_NO_CONTENT);

    /*return $this->redirectToRoute('app_ocr', [
        'id' => $resultocr->getId()
    ]);*/
    
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
    // Importer les codes pour la SELECT List
    ###################################################################################################################################
    /**
 * @Route("/api/coders", name="api_genders", methods={"GET"})
 */
public function getCodes(ManagerRegistry $doctrine,Request $request): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();
    $queryBuilder = $entityManager->createQueryBuilder();
    $queryBuilder->select('t.code')
   ->distinct()
   ->from('App\Entity\EfClassification', 't');

$result = $queryBuilder->getQuery()->getResult();
//dd($result[0]["code"]);
$data = [];

/*foreach ($result as $key => $result) {
   
    $data[] = $data+ [
        'value' => $result["code"],
      
    ];
}*/
foreach ($result as $item) {
    $data[] = [
        $item["code"] => $item["code"],
        
    ];
}
//dd($data);


    return $this->json($data);
}
   
    }

