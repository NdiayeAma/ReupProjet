<?php
namespace App\Controller;

use App\Entity\Disponibilite;
use App\Form\DisponibiliteType;
use App\Repository\DisponibiliteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/disponibilite')]
class DisponibiliteController extends AbstractController
{
    #[Route('/', name: 'app_disponibilite_index', methods: ['GET'])]
    public function index(DisponibiliteRepository $disponibiliteRepository): Response
    {
        return $this->render('disponibilite/index.html.twig', [
            'disponibilites' => $disponibiliteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_disponibilite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $login = $user->getUserIdentifier();
        $utilisateur = $userRepository->findOneBy(['login' => $login]);

        $disponibilite = new Disponibilite();
        $form = $this->createForm(DisponibiliteType::class, $disponibilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disponibilite->setAgentId($utilisateur->getId());
            $disponibilite->setBackgroundColor('#5b0054');
            $entityManager->persist($disponibilite);
            $entityManager->flush();

            return $this->redirectToRoute('app_disponibilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('disponibilite/new.html.twig', [
            'disponibilite' => $disponibilite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_disponibilite_show', methods: ['GET'])]
    public function show(Disponibilite $disponibilite): Response
    {
        return $this->render('disponibilite/show.html.twig', [
            'disponibilite' => $disponibilite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_disponibilite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Disponibilite $disponibilite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DisponibiliteType::class, $disponibilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_disponibilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('disponibilite/edit.html.twig', [
            'disponibilite' => $disponibilite,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/delete-disponibilite/{id}', name: 'delete_disponibilite', methods: ['POST'])]
    public function delete(Request $request, Disponibilite $disponibilite, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($disponibilite);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
