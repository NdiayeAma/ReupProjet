<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Repository\PlanningRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning')]
    public function index(): Response
    {
        return $this->render('', [
            'controller_name' => 'PlanningController',
        ]);
    }

    #[Route('/planifier', name: 'planifier', methods: ['POST'])]
    public function planifier(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $agent = $userRepository->find($data['agent_id']);
            $start = new \DateTime($data['start']);
            $end = new \DateTime($data['end']);
            $statut = $data['statut'];

            // Create and persist a new Planning entity
            $planning = new Planning();
            $planning->setAgentId($agent->getId());
            $planning->setDebut($start);
            $planning->setFin($end);
            $planning->setStatut($statut);

            $entityManager->persist($planning);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/planning/tous', name: 'planning_tous', methods: ['GET'])]
    public function viewPlanning(Request $request, PlanningRepository $planningRepository, UserRepository $userRepository): Response
    {
        $plannings = $planningRepository->findAll();
        $agents = $userRepository->findAll();

        // Calculer les dates de la semaine en cours ou de la date fournie
        $currentDate = $request->query->get('date') ? new \DateTime($request->query->get('date')) : new \DateTime('monday this week');
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = (clone $currentDate)->modify("+{$i} days");
        }

        return $this->render('planning/view.html.twig', [
            'plannings' => $plannings,
            'agents' => $agents,
            'dates' => $dates,
            'currentDate' => $currentDate,
        ]);
    }

    #[Route('/planning/update/{id}', name: 'update_planning', methods: ['POST'])]
    public function update(Request $request, Planning $planning, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['debut']) && isset($data['fin'])) {
            $planning->setDebut(new \DateTime($data['debut']));
            $planning->setFin(new \DateTime($data['fin']));
            $entityManager->flush();

            return $this->json(['success' => true]);
        }

        return $this->json(['error' => 'Invalid data provided'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/planning/delete/{id}', name: 'delete_planning', methods: ['DELETE'])]
    public function delete(Request $request, Planning $planning, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($planning);
            $entityManager->flush();
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
