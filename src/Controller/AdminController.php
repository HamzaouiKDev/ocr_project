<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    public function __construct(private EntrepriseRepository $entrepriseRepository)
    {
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(AuthenticationUtils $authenticationUtils): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig', [
            'isAdmin' => $isAdmin,
            'adminName' => $this->resolveAdminName(),
            'stats' => $isAdmin ? $this->collectStats() : null,
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'loginError' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    private function resolveAdminName(): ?string
    {
        $user = $this->getUser();
        if ($user === null) {
            return null;
        }

        if (method_exists($user, 'getUserIdentifier')) {
            return (string) $user->getUserIdentifier();
        }

        if (method_exists($user, 'getUsername')) {
            return (string) $user->getUsername();
        }

        return null;
    }

    /**
     * @return array{
     *   total:int,
     *   validated:int,
     *   pending:int,
     *   completionRate:float,
     *   remainingRate:float,
     *   assignedTotal:int,
     *   assignedShare:float,
     *   unassignedTotal:int,
     *   perUser:list<array{username:string,assigned:int,validated:int,globalRate:float}>,
     *   topPerformer:array{username:string,validated:int,globalRate:float}|null
     * }
     */
    private function collectStats(): array
    {
        $totalCount = (int) $this->entrepriseRepository
            ->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $trueValues = ['true', '1', 'oui'];
        $validatedCount = (int) $this->entrepriseRepository
            ->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('LOWER(e.valide) IN (:trueValues)')
            ->setParameter('trueValues', $trueValues)
            ->getQuery()
            ->getSingleScalarResult();

        $pendingCount = max(0, $totalCount - $validatedCount);
        $completionRate = $totalCount > 0 ? round(($validatedCount / $totalCount) * 100, 1) : 0.0;

        $perUserRaw = $this->entrepriseRepository
            ->createQueryBuilder('e')
            ->select("CASE WHEN e.login IS NULL OR e.login = '' THEN :unassigned ELSE e.login END AS username")
            ->addSelect('e.login AS rawLogin')
            ->addSelect('SUM(CASE WHEN LOWER(e.valide) IN (:trueValues) THEN 1 ELSE 0 END) AS validatedCount')
            ->addSelect('COUNT(e.id) AS totalCount')
            ->setParameter('trueValues', $trueValues)
            ->setParameter('unassigned', 'Non assigne')
            ->groupBy('e.login')
            ->orderBy('validatedCount', 'DESC')
            ->addOrderBy('totalCount', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $perUser = [];
        $assignedTotal = 0;
        $assignedValidated = 0;

        foreach ($perUserRaw as $row) {
            $rawLogin = $row['rawLogin'] ?? null;
            $isAssigned = $rawLogin !== null && $rawLogin !== '';

            if (!$isAssigned) {
                continue;
            }

            $username = (string) ($row['username'] ?? ($rawLogin ?? 'Non assigne'));
            $validated = (int) ($row['validatedCount'] ?? 0);
            $total = (int) ($row['totalCount'] ?? 0);
            $globalRate = $totalCount > 0 ? round(($validated / $totalCount) * 100, 2) : 0.0;

            $perUser[] = [
                'username' => $username,
                'assigned' => $total,
                'validated' => $validated,
                'globalRate' => $globalRate,
            ];

            $assignedTotal += $total;
            $assignedValidated += $validated;
        }

        $topPerformer = null;
        if (!empty($perUser)) {
            $candidate = $perUser[0];
            if ($candidate['validated'] > 0) {
                $topPerformer = [
                    'username' => $candidate['username'],
                    'validated' => $candidate['validated'],
                    'globalRate' => $candidate['globalRate'],
                ];
            }
        }

        $assignedShare = $totalCount > 0 ? round(($assignedTotal / $totalCount) * 100, 1) : 0.0;
        $unassignedTotal = max(0, $totalCount - $assignedTotal);

        return [
            'total' => $totalCount,
            'validated' => $validatedCount,
            'pending' => $pendingCount,
            'completionRate' => $completionRate,
            'remainingRate' => max(0, 100 - $completionRate),
            'assignedTotal' => $assignedTotal,
            'assignedShare' => $assignedShare,
            'assignedValidated' => $assignedValidated,
            'unassignedTotal' => $unassignedTotal,
            'perUser' => $perUser,
            'topPerformer' => $topPerformer,
        ];
    }
}
