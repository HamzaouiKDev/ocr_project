<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/getuser', name: 'get_users')]
    public function getALL(EntityManagerInterface $entityManager)
    {
        $result_ocr_Repository = $entityManager->getRepository(ResultOcr::class);
        $result_ocrs = $result_ocr_Repository->findAll();
        $data = [];
        foreach ($result_ocrs as $key => $result_ocr) {
            $data[] = [
                'id' => $result_ocr->getId(),
                'path' => $result_ocr->getPath(),
                'matricule' => $result_ocr->getMatricule(),
                'annee' => $result_ocr->getAnnee(),
                'page' => $result_ocr->getPage(),
                'ligne' => $result_ocr->getLigne(),
                'label' => $result_ocr->getLabel(),
                'notes' => $result_ocr->getNotes(),
                'value_n' => $result_ocr->getValueN(),
                'value_n1' => $result_ocr->getValueN1(),
                'code' => $result_ocr->getCode(),
                'type_page' => $result_ocr->getTypePage(),
            ];
        }
        $data=['data'=>$data];
        //dd($data);
        return $this->json($data);
        //return new JsonResponse($data);
    }
###################################################################################################################################
    // Mise a jour de first name 
##################################################################################################################################
    #[Route('/updatelabel', name: 'update_label')]
    public function updateLabel(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $result_ocr = $entityManager->getRepository(ResultOcr::class)->find($id);

        if (!$result_ocr) {
            throw $this->createNotFoundException(
                'No ligne found for id '.$id
            );
        }

        $result_ocr->setLabel($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('get_users', [
            'id' => $user->getId()
        ]);
    }

    ###################################################################################################################################
    // Mise a jour de last name 
    ###################################################################################################################################
    #[Route('/updatelastname', name: 'update_last_name')]
    public function updateLast(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $user->setLastName($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('get_users', [
            'id' => $user->getId()
        ]);
    }

    ###################################################################################################################################
    // Mise a jour de Gender 
    ###################################################################################################################################
    #[Route('/updateGender', name: 'update_Gender')]
    public function updateGender(ManagerRegistry $doctrine) :Response
    {
        $id= $_POST['pk'];
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $user->setGender($_POST["value"]);
        $entityManager->flush();

        return $this->redirectToRoute('get_users', [
            'id' => $user->getId()
        ]);
    }
    }

