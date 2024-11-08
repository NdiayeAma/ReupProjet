<?php
namespace App\Controller;

use App\Entity\Disponibilite;
use App\Repository\DisponibiliteRepository;
use App\Repository\EvenementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisponibilitesController extends AbstractController
{
    #[Route('/disponibilites', name: 'app_disponibilites')]
    public function index(DisponibiliteRepository $disponibiliteRepository, EvenementRepository $evenementRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas authentifié.');
        }

        $login = $user->getUserIdentifier();
        $utilisateur = $userRepository->findOneBy(['login' => $login]);
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $disponibilites = $disponibiliteRepository->findByUser($utilisateur->getId());
        $evenements = $evenementRepository->findAll(); // Récupère tous les événements

        $dispos = array_map(function ($dispo) {
            return [
                'id' => $dispo->getId(),
                'title' => '- ' . $dispo->getFin()->format('H:i'),
                'start' => $dispo->getDebut()->format('Y-m-d H:i:s'),
                'end' => $dispo->getFin()->format('Y-m-d H:i:s'),
                'allDay' => $dispo->isAllDay(),
                'backgroundColor' => $dispo->getBackgroundColor(),
            ];
        }, $disponibilites);

        $evenementList = array_map(function ($evenement) {
            return [
                'id' => $evenement->getId(),
                'nom' => $evenement->getNom(),
                'datedebut' => $evenement->getDatedebut()->format('Y-m-d'),
                'datefin' => $evenement->getDatefin()->format('Y-m-d'),
                'couleur' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)) // Génère une couleur aléatoire
            ];
        }, $evenements);

        $data = json_encode($dispos);
        $evenementsJson = json_encode($evenementList);

        return $this->render('disponibilites/index.html.twig', [
            'data' => $data,
            'evenements' => $evenementsJson
        ]);
    }





    #[Route('/add-disponibilite', name: 'add_disponibilite', methods: ['POST'])]
    public function addDisponibilite(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $user = $this->getUser();
        $login = $user->getUserIdentifier();
        $utilisateur = $userRepository->findOneBy(['login' => $login]);
        $data = json_decode($request->getContent(), true);

        $disponibilite = new Disponibilite();
        $disponibilite->setAgentId($utilisateur->getId());
        $disponibilite->setDebut(new \DateTime($data['debut']));
        $disponibilite->setFin(new \DateTime($data['fin']));
        $disponibilite->setAllDay(false);
        $disponibilite->setBackgroundColor('#5b0054');
        $entityManager->persist($disponibilite);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/disponibilites/toutes', name: 'app_disponibilites_toutes')]
    public function viewAll(DisponibiliteRepository $disponibiliteRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $evenements = $disponibiliteRepository->findAll();
        $dispos = [];

        foreach ($evenements as $dispo) {
            $agent = $userRepository->find($dispo->getAgentId());

            $dispos[] = [
                'id' => $dispo->getId(),
                'title' => $agent->getNom() . ' - ' . $dispo->getDebut()->format('H:i') . ' - ' . $dispo->getFin()->format('H:i'),
                'start' => $dispo->getDebut()->format('Y-m-d H:i:s'),
                'end' => $dispo->getFin()->format('Y-m-d H:i:s'),
                'allDay' => $dispo->isAllDay(),
                'backgroundColor' => $dispo->getBackgroundColor(),
            ];
        }

        $data = json_encode($dispos);

        return $this->render('disponibilites/toutes.html.twig', ['data' => $data]);
    }

    #[Route('/disponibilites/date/{date}', name: 'disponibilites_par_date')]
    public function disponibilitesParDate($date, DisponibiliteRepository $disponibiliteRepository, UserRepository $userRepository): Response
    {
        try {
            $debut = new \DateTime($date . ' 00:00:00');
            $fin = new \DateTime($date . ' 23:59:59');

            $disponibilites = $disponibiliteRepository->findByDateRange($debut, $fin);

            $data = array_map(function ($dispo) use ($userRepository) {
                $agent = $userRepository->find($dispo->getAgentId());
                return [
                    'agent' => $agent->getNom(),
                    'agent_id' => $agent->getId(),
                    'debut' => $dispo->getDebut()->format('H:i'),
                    'fin' => $dispo->getFin()->format('H:i')
                ];
            }, $disponibilites);

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
