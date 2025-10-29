<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use App\Entity\Entreprise;
use App\Form\TypePageType;
use App\Entity\TypePageOcr;
use App\Entity\ResultOcrSauv;
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
    // Sauvegarder une page
##################################################################################################################################

public function sauvegarde(Request $request,EntityManagerInterface $entityManager) 
{

    $session = $request->getSession();
    $data=$session->get('data') ?? [];
    if (!is_iterable($data)) {
        $data = [];
    }
    $matricule=$session->get('matricule');
    $annee=$session->get('annee');
    $page = $session->get('pageEnCours');
    if ($page === null && is_iterable($data)) {
        foreach ($data as $donneesPage) {
            if ($donneesPage && $donneesPage->getPage() !== null) {
                $page = $donneesPage->getPage();
                break;
            }
        }
    }

    if ($matricule !== null && $annee !== null && $page !== null) {
        $entityManager->createQuery('DELETE FROM App\Entity\ResultOcrSauv r WHERE r.matricule = :matricule AND r.annee = :annee AND r.page = :page')
            ->setParameter('matricule', $matricule)
            ->setParameter('annee', $annee)
            ->setParameter('page', $page)
            ->execute();
    }

   //dd($data);
    $i=0;
    foreach ($data as $donnees) {
        if($donnees->getCode() != NULL && ($donnees->getValueN() != NULL || $donnees->getValueN1() != NULL))
        {
        $entite = new ResultOcrSauv();
        $entite->setMatricule($matricule);
        $entite->setAnnee($annee);
        $entite->setPage($donnees->getPage());
        $entite->setLigne($donnees->getLigne());
        $entite->setTypePage($donnees->getTypePage());
        $entite->setLabel($donnees->getLabel());
        $entite->setValueN($donnees->getValueN());
        $entite->setValueN1($donnees->getValueN1());
        $entite->setCode($donnees->getCode());
        $entite->setPath($donnees->getPath());
        $i++;
        // Répétez pour tous les champs de votre entité

        $entityManager->persist($entite);
        }
    }

    // Exécutez la requête d'insertion
    $entityManager->flush();
}
###################################################################################################################################
  // Mise a jour du type de page
##################################################################################################################################
  
    #[Route('/update-type-page', name: 'update_type_page')]
    public function updateTypePage(Request $request, EntityManagerInterface $em):Response
    {
        // Lecture des variables session necessaire 
        
       
         
        $form = $this->createForm(TypePageType::class);
        $form->handleRequest($request);
        
        return new Response('', Response::HTTP_NO_CONTENT);
       
    }
###################################################################################################################################
 // Controlleur de la page principale
##################################################################################################################################
    
    #[Route('/ocr', name: 'app_ocr')]
    public function index(EntrepriseRepository $entrepriseRepository,TypePageOcrRepository $typePageOcrRepository,Request $request,ManagerRegistry $doctrine,EntityManagerInterface $em): Response
    {
        //recuperer les type de pages pour alimenter la liste deroulante pour la mise a jour
                    $entityManager = $this->getDoctrine()->getManager();
                    $query = $entityManager->createQueryBuilder()
                            ->select('DISTINCT p.typeEf')
                            ->from('App\Entity\EfClassification', 'p')
                            ->getQuery();
                    $typePages=$query->getResult();
                    foreach($typePages as $typePage )
                    {
                        $data[$typePage["typeEf"]]=$typePage["typeEf"];
                    }
                    $typePages=$data;
                    $form = $this->createForm(TypePageType::class , null, ['typePages' => $typePages]);
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $session = $request->getSession();
                        $mat=$session->get('matricule');
                        $ann=$session->get('annee');
                        $page=$session->get('pageEnCours'); 
                        // Ici, on récupère la nouvelle valeur choisie.
                    $newAttributeValue = $form->get('attribute')->getData();
                    $record = $em->getRepository(TypePageOcr::class)->findBy(['matricule'=>$mat,'annee'=>$ann,'page'=>$page]);
                    $record[0]->setLabelType($newAttributeValue); 
                    $em->flush();
                    $this->addFlash('success', 'Mise a jour réussie !');
                    return $this->redirectToRoute('app_ocr');
                   

                        // Message flash ou autre traitement après la mise à jour.

                    
                    }
        //Recuperer l'utlisateur en cours 
        $user = $this->security->getUser();
       // dd($user);
        // verifier si l'utlisateur en cours a un bilan affecté a lui et non validé
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('e')
            ->from('App\Entity\Entreprise', 'e')
            ->Where('e.login = :param1')
            ->andWhere('e.valide = :param')
            ->setParameter('param1',$user->getUsername())
            ->setParameter('param', 'false')
            ->setMaxResults(1)
            ->getQuery();
        
        $entrepriseNonValide = $query->getOneOrNullResult();
        //dd($entreprise);

       if($entrepriseNonValide==NULL)

       {
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

        if (!$entreprise) {
            $session = $request->getSession();
            foreach (['typePage', 'matricule', 'annee', 'pages', 'pageEnCours', 'pageEnCoursIndex', 'nbrPages', 'url', 'data'] as $key) {
                $session->remove($key);
            }

            return $this->render('OCR/no_more_bilan.html.twig');
        }

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
        // recuperer le type de la page en cours
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
      
        
    }
    // Traitement du cas ou l'utilisateur est affecte a un bilan non encore validé
    else
    {
        $matricule=$entrepriseNonValide->getMatricule();
        $annee=$entrepriseNonValide->getAnnee();
        $url=$entrepriseNonValide->getPath();
        $id=$entrepriseNonValide->getId();
        //dd($matricule);
        // recuperer un tableau de pages du bilan

        $typePage=$typePageOcrRepository->findPages($matricule,$annee);
        foreach($typePage as $ligne)
        {
            $pages[]=$ligne->getPage();
        }
        //dd($pages);
        $nbrPages=count($pages);
         // recuperer la premiere page non valide
         $entityManager = $this->getDoctrine()->getManager();
         $queryBuilder = $entityManager->createQueryBuilder();
         $query = $queryBuilder
             ->select('p')
             ->from('App\Entity\TypePageOcr', 'p')
             ->where('p.matricule = :param1')
             ->andWhere('p.annee = :param2')
             ->setParameter('param1', $matricule)
             ->andWhere('p.valide = :param3')
             ->setParameter('param2', $annee)
             ->setParameter('param3', 'false')
             ->setMaxResults(1)
             ->getQuery();
         
         $pageNonValide = $query->getOneOrNullResult();
         //dd($pageNonValide);

         $session = $request->getSession();

         $currentType = null;
         $currentPage = null;

         if ($pageNonValide) {
             $currentType = $pageNonValide->getLabelType();
             $currentPage = $pageNonValide->getPage();
         } elseif (!empty($typePage)) {
             $first = reset($typePage);
             if ($first) {
                 $currentType = $first->getLabelType();
             }
             $currentPage = !empty($pages) ? $pages[0] : null;
         } else {
             $currentPage = !empty($pages) ? $pages[0] : null;
         }

         $session->set('typePage', $currentType ?? 'N/A');
         $session->set('matricule',$matricule);
         $session->set('annee',$annee);
         $session->set('pages',$pages);

         $pageIndex = 0;
         if ($currentPage !== null) {
             $foundIndex = array_search($currentPage, $pages, true);
             if ($foundIndex !== false) {
                 $pageIndex = $foundIndex;
             }
         }

         $session->set('pageEnCours', $pages[$pageIndex] ?? ($currentPage ?? 0));
         $session->set('pageEnCoursIndex', $pageIndex);
         $session->set('nbrPages',$nbrPages);
         $session->set('url',$url);
         $renderPage = $pages[$pageIndex] ?? ($currentPage ?? 1);
         $currentTypeForForm = $session->get('typePage');
         if ($form->has('attribute') && is_string($currentTypeForForm)) {
             $normalizedType = trim($currentTypeForForm);
             if ($normalizedType !== '' && strcasecmp($normalizedType, 'N/A') !== 0) {
                 $form->get('attribute')->setData($normalizedType);
             }
         }
         return $this->render('ocr/index.html.twig',[ 'form' => $form->createView(),'page'=>$renderPage,'url'=>$url,]);
        //return new Response('', Response::HTTP_NO_CONTENT);  
    }
        $currentTypeForForm = $session->get('typePage');
        if ($form->has('attribute') && is_string($currentTypeForForm)) {
            $normalizedType = trim($currentTypeForForm);
            if ($normalizedType !== '' && strcasecmp($normalizedType, 'N/A') !== 0) {
                $form->get('attribute')->setData($normalizedType);
            }
        }
        return $this->render('ocr/index.html.twig',[ 'form' => $form->createView(),'page'=>$pages[0],'url'=>$url,]);
    }
// call page

public function callPage(EntrepriseRepository $entrepriseRepository,TypePageOcrRepository $typePageOcrRepository,Request $request): Response
{
    $session = $request->getSession();
    $page=$session->get('pages',$pages);
    $session->set('pages',$pages);

   
    return $this->render('ocr/index.html.twig', [
        'matricule' =>$matricule,
        'annee'=>$annee,
        'pages'=>$pages,
        'nbrPages'=>$nbrPages
    ]);
}
###################################################################################################################################
 // recuperer ocrResult par matricule et annee
##################################################################################################################################

  /*#[Route('/getLigneOcr/{matricule}/{annee}', name: 'ligne_ocr_show')]
  public function getLigneOcr(ManagerRegistry $doctrine, string $matricule, string $annee,Request $request)
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
    }*/
###################################################################################################################################
    // Acceder a la page suivante
##################################################################################################################################

#[Route('/suivant', name: 'get_page_suivante')]
public function suivante(ManagerRegistry $doctrine,Request $request,EntityManagerInterface $entityManager,EntityManagerInterface $em)
{
    $this->sauvegarde($request,$entityManager);
    $entityManager = $this->getDoctrine()->getManager();
    $query = $entityManager->createQueryBuilder()
            ->select('DISTINCT p.typeEf')
            ->from('App\Entity\EfClassification', 'p')
            ->getQuery();
    $typePages=$query->getResult();
    foreach($typePages as $typePage )
    {
        $data[$typePage["typeEf"]]=$typePage["typeEf"];
    }
    $typePages=$data;
    $form = $this->createForm(TypePageType::class , null, ['typePages' => $typePages]);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $session = $request->getSession();
        $mat=$session->get('matricule');
        $ann=$session->get('annee');
        $page=$session->get('pageEnCours'); 
        // Ici, on récupère la nouvelle valeur choisie.
    $newAttributeValue = $form->get('attribute')->getData();
    $record = $em->getRepository(TypePageOcr::class)->findBy(['matricule'=>$mat,'annee'=>$ann,'page'=>$page]);
    $record[0]->setLabelType($newAttributeValue); 
    $em->flush();
    return $this->redirectToRoute('app_ocr');

        // Message flash ou autre traitement après la mise à jour.

    
    }
    $session = $request->getSession();
    $index=$session->get('pageEnCoursIndex');
    $pages=$session->get('pages'); 
    $url=$session->get('url'); 
    $data=$session->get('data'); 
    //dd($data);
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
    $currentTypeForForm = $session->get('typePage');
    if ($form->has('attribute') && is_string($currentTypeForForm)) {
        $normalizedType = trim($currentTypeForForm);
        if ($normalizedType !== '' && strcasecmp($normalizedType, 'N/A') !== 0) {
            $form->get('attribute')->setData($normalizedType);
        }
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
    $currentTypeForForm = $session->get('typePage');
    if ($form->has('attribute') && is_string($currentTypeForForm)) {
        $normalizedType = trim($currentTypeForForm);
        if ($normalizedType !== '' && strcasecmp($normalizedType, 'N/A') !== 0) {
            $form->get('attribute')->setData($normalizedType);
        }
    }
    return $this->render('ocr/index.html.twig', [
        'form' => $form->createView(),
        'page'=>$pageencours,
        'url'=>$url,
    ]);
    
}

#[Route('/finaliser', name: 'ocr_finalize_bilan', methods: ['GET'])]
public function finalizeBilan(Request $request, TypePageOcrRepository $typePageOcrRepository, EntrepriseRepository $entrepriseRepository, EntityManagerInterface $entityManager): Response
{
    $session = $request->getSession();
    $matricule = $session->get('matricule');
    $annee = $session->get('annee');

    if (!$matricule || !$annee) {
        $this->addFlash('error', "Aucun bilan en cours n'a ete detecte.");
        return $this->redirectToRoute('app_ocr');
    }

    $typePages = $typePageOcrRepository->findBy(['matricule' => $matricule, 'annee' => $annee]);
    foreach ($typePages as $typePage) {
        $typePage->setValide('true');
    }

    $entreprise = $entrepriseRepository->findOneBy(['matricule' => $matricule, 'annee' => $annee]);
    if ($entreprise) {
        $entreprise->setValide('true');
    }

    $entityManager->flush();

    foreach (['typePage', 'matricule', 'annee', 'pages', 'pageEnCours', 'pageEnCoursIndex', 'nbrPages', 'url', 'data'] as $key) {
        $session->remove($key);
    }

    $this->addFlash('success', 'Le bilan a ete valide avec succes.');

    return $this->redirectToRoute('app_ocr');
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
        $session->set('data', $result_ocrs);
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
                $session = $request->getSession();
                $entityManager = $this->getDoctrine()->getManager();
                $queryBuilder = $entityManager->createQueryBuilder();
                $queryBuilder->select('t.code','t.label')
                            ->distinct()
                            ->from('App\Entity\EfClassification', 't')
                            ->where('t.typeEf = :param1')
                            ->setParameter('param1', $session->get('typePage'));
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
                    $item["code"] => $item["code"].'=>'.$item["label"],
                    
                ];
            }
            //dd($data);


                return $this->json($data);
}

###################################################################################################################################
    // Suppression d'une ligne
###################################################################################################################################
    #[Route('/supprimerLigne', name: 'supprimer_ligne', methods: ['POST'])]
    public function supprimerLigne(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $id = $request->request->get('id');
        if (!$id) {
            return new JsonResponse(['status' => 'error', 'message' => 'Identifiant manquant'], Response::HTTP_BAD_REQUEST);
        }

        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(ResultOcr::class);
        $ligne = $repository->find($id);

        if (!$ligne) {
            return new JsonResponse(['status' => 'error', 'message' => 'Ligne introuvable'], Response::HTTP_NOT_FOUND);
        }

        $matricule = $ligne->getMatricule();
        $annee = $ligne->getAnnee();
        $page = $ligne->getPage();

        $entityManager->remove($ligne);
        $entityManager->flush();

        if ($matricule !== null && $annee !== null && $page !== null) {
            $resultats = $repository->findBy(
                ['matricule' => $matricule, 'annee' => $annee, 'page' => $page],
                ['ligne' => 'ASC']
            );
            $request->getSession()->set('data', $resultats);
        }

        return new JsonResponse(['status' => 'ok']);
    }

 ###################################################################################################################################
    // Mise a jour de annee n-1
    ###################################################################################################################################
    #[Route('/ajoutLigne', name: 'ajout_ligne')]
    public function ajoutLigne(ManagerRegistry $doctrine,Request $request) :Response
    {
        $session = $request->getSession();
        $url=$session->get('url');
        $matricule=$session->get('matricule');
        $annee=$session->get('annee');
        $page=$session->get('pageEnCours');
        $type=$session->get('typePage');

        // recuperer le Max de ligne dans la page en cours

        $repository = $this->getDoctrine()->getRepository(ResultOcr::class);

        $query = $repository->createQueryBuilder('e')
            ->select('MAX(e.ligne) as maxLigne')
            ->getQuery();
        
        $maxLigne = $query->getSingleScalarResult();


       

        $entityManager = $this->getDoctrine()->getManager();

        $entity = new ResultOcr(); 
    
        // Définir les valeurs des champs de l'entité
        $entity->setPath($url);
        $entity->setMatricule($matricule);
        $entity->setAnnee($annee);
        $entity->setPage($page);
        $entity->setLigne($maxLigne+1);
        $entity->setLabel('');
        $entity->setNotes('');
        $entity->setValueN('');
        $entity->setValueN1('');
        $entity->setCode('');
        $entity->setTypePage($type);
        // Ajoutez autant de champs et de valeurs que nécessaire
    
        // Persister l'entité
        $entityManager->persist($entity);
    
        // Flusher les changements en base de données
        $entityManager->flush();
        //return $this->redirectToRoute('current_route_name'); // Remplacez 'current_route_name' par le nom de la route actuelle
        return new JsonResponse(['message' => 'Fonction du contrôleur appelée avec succès']);
    }
   
    }

    

