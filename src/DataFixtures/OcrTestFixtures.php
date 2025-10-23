<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
use App\Entity\ResultOcr;
use App\Entity\TypePageOcr;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class OcrTestFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $matricule = '0002388V';
        $annee = '2020';
        $pdfPath = 'pdf/pdf_traite/0002388V_2020/RAPPORTS STAR ASSURANCES 31-12-2020.pdf';

        $entreprise = (new Entreprise())
            ->setMatricule($matricule)
            ->setAnnee($annee)
            ->setPath($pdfPath)
            ->setLogin(null)
            ->setValide('false');
        $manager->persist($entreprise);

        $typePagesData = [
            [1, 'Bilan - Actif', 'BILAN_ACTIF', 22, 'false'],
            [2, 'Bilan - Passif', 'BILAN_PASSIF', 20, 'false'],
            [3, 'Compte de resultat', 'COMPTE_RESULTAT', 18, 'false'],
            [4, 'Annexe - Flux de tresorerie', 'ANNEXE_FLUX', 12, 'false'],
            [5, 'Annexe - Notes', 'ANNEXE_NOTES', 25, 'false'],
        ];

        foreach ($typePagesData as [$page, $label, $type, $nbr, $valide]) {
            $typePage = (new TypePageOcr())
                ->setMatricule($matricule)
                ->setAnnee($annee)
                ->setPage($page)
                ->setLabelType($label)
                ->setType($type)
                ->setNbr($nbr)
                ->setValide($valide)
                ->setPath($pdfPath);

            $manager->persist($typePage);
        }

        $rows = [
            [1, 1, '101', 'Capital social', 'Observations a verifier', '100000', '95000', 'Bilan - Actif'],
            [1, 2, '102', 'Reserve legale', '', '25000', '20000', 'Bilan - Actif'],
            [1, 3, '104', 'Immobilisations', 'Verifier amortissements', '85000', '83000', 'Bilan - Actif'],
            [2, 1, '201', 'Dettes bancaires', 'Voir echeancier', '45000', '52000', 'Bilan - Passif'],
            [2, 2, '202', 'Fournisseurs', '', '18000', '15000', 'Bilan - Passif'],
            [2, 3, '203', 'Dettes fiscales', 'A regulariser', '12000', '9000', 'Bilan - Passif'],
            [3, 1, '301', "Chiffre d'affaires", '', '220000', '210000', 'Compte de resultat'],
            [3, 2, '302', "Charges d'exploitation", 'Reclasser charges', '160000', '150000', 'Compte de resultat'],
            [3, 3, '303', 'Resultat net', '', '32000', '29000', 'Compte de resultat'],
            [4, 1, '401', 'Flux tresorerie exploitation', '', '45000', '40000', 'Annexe - Flux de tresorerie'],
            [4, 2, '402', 'Flux tresorerie investissement', '', '15000', '12000', 'Annexe - Flux de tresorerie'],
            [5, 1, '501', 'Note provisions', 'Analyse detaillee', '', '', 'Annexe - Notes'],
            [5, 2, '502', 'Note effectifs', '', '', '', 'Annexe - Notes'],
        ];

        foreach ($rows as [$page, $ligne, $code, $label, $notes, $valueN, $valueN1, $typePageLabel]) {
            $result = (new ResultOcr())
                ->setPath($pdfPath)
                ->setMatricule($matricule)
                ->setAnnee($annee)
                ->setPage($page)
                ->setLigne($ligne)
                ->setCode($code)
                ->setLabel($label)
                ->setNotes($notes)
                ->setValueN($valueN)
                ->setValueN1($valueN1)
                ->setTypePage($typePageLabel);

            $manager->persist($result);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['ocr_test'];
    }
}
