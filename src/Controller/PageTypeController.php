<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use App\Entity\TypePageOcr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PageTypeController extends AbstractController
{
    #[Route('/page/type', name: 'page_update_type', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $matricule = $session->get('matricule');
        $annee = $session->get('annee');
        $page = $session->get('pageEnCours');

        $formData = $request->request->all('type_page_type');
        $newType = $formData['attribute'] ?? null;

        if (!$matricule || !$annee || $page === null || !$newType) {
            $this->addFlash('error', "Impossible de mettre à jour le type de page.");

            return $this->redirectToRoute('app_ocr');
        }

        $typePageRecord = $entityManager->getRepository(TypePageOcr::class)->findOneBy([
            'matricule' => $matricule,
            'annee' => $annee,
            'page' => $page,
        ]);

        if ($typePageRecord) {
            $typePageRecord->setLabelType($newType);
            $typePageRecord->setType($newType);
        }

        $resultRecords = $entityManager->getRepository(ResultOcr::class)->findBy([
            'matricule' => $matricule,
            'annee' => $annee,
            'page' => $page,
        ]);

        foreach ($resultRecords as $resultRecord) {
            $resultRecord->setTypePage($newType);
        }

        $session->set('typePage', $newType);

        $entityManager->flush();

        $this->addFlash('success', 'Type de page mis à jour.');

        return $this->redirectToRoute('app_ocr');
    }
}
