<?php

namespace App\Controller;

use App\Entity\ResultOcr;
use App\Entity\TypePageOcr;
use App\Form\TypePageType;
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

        $normalize = static function ($value) {
            if ($value === null) {
                return null;
            }

            if (is_string($value)) {
                $trimmed = trim($value);
                if ($trimmed === '' || strcasecmp($trimmed, 'N/A') === 0) {
                    return null;
                }

                return $trimmed;
            }

            return $value;
        };

        $matricule = $normalize($matricule);
        $annee = $normalize($annee);
        $page = $normalize($page);

        $form = $this->createForm(TypePageType::class);
        $form->handleRequest($request);

        $newType = null;
        if ($form->has('attribute')) {
            $newType = $normalize($form->get('attribute')->getData());
        }

        if ($newType === null) {
            $formData = $request->request->all('type_page_type');
            if (is_array($formData)) {
                $newType = $normalize($formData['attribute'] ?? null);
            }
        }

        $postedMatricule = $normalize($request->request->get('matricule'));
        $postedAnnee = $normalize($request->request->get('annee'));
        $postedPageRaw = $normalize($request->request->get('page'));
        if ($postedPageRaw !== null && is_string($postedPageRaw) && ctype_digit($postedPageRaw)) {
            $postedPage = (int) $postedPageRaw;
        } else {
            $postedPage = $postedPageRaw;
        }

        if ($matricule === null && $postedMatricule !== null) {
            $matricule = $postedMatricule;
            $session->set('matricule', $matricule);
        }

        if ($annee === null && $postedAnnee !== null) {
            $annee = $postedAnnee;
            $session->set('annee', $annee);
        }

        if ($page === null && $postedPage !== null) {
            $page = $postedPage;
            $session->set('pageEnCours', $page);
        }

        if ($page === null) {
            $pages = $session->get('pages');
            $pageIndex = $session->get('pageEnCoursIndex');
            if (is_array($pages) && $pageIndex !== null && array_key_exists($pageIndex, $pages)) {
                $page = $pages[$pageIndex];
                $session->set('pageEnCours', $page);
            }
        }

        if ($page !== null && is_string($page) && ctype_digit($page)) {
            $page = (int) $page;
        }

        if ($newType === null) {
            $currentSessionType = $normalize($session->get('typePage'));
            if ($currentSessionType !== null) {
                $newType = $currentSessionType;
            }
        }

        $missing = [];
        if ($matricule === null) {
            $missing[] = 'matricule';
        }

        if ($annee === null) {
            $missing[] = 'annee';
        }

        if ($page === null) {
            $missing[] = 'page';
        }

        if ($newType === null) {
            $missing[] = 'type';
        }

        if (!empty($missing)) {
            $this->addFlash('error', sprintf(
                'Impossible de mettre a jour le type de page (champs manquants : %s).',
                implode(', ', $missing)
            ));

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

        $this->addFlash('success', 'Type de page mis a jour.');

        return $this->redirectToRoute('app_ocr');
    }
}
