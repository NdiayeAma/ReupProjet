<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Suivi;
use App\Form\SuiviType;
use App\Repository\SiteRepository;
use App\Repository\SuiviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/suivi')]
class SuiviController extends AbstractController
{
    #[Route('/', name: 'app_suivi_index', methods: ['GET'])]
    public function index(SuiviRepository $suiviRepository): Response
    {
        return $this->render('suivi/index.html.twig', [
            'suivis' => $suiviRepository->findBy(['auteur'=>$this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_suivi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $suivi = new Suivi();
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($suivi);
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('suivi/new.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_show', methods: ['GET'])]
    public function show(Suivi $suivi): Response
    {
        return $this->render('suivi/show.html.twig', [
            'suivi' => $suivi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Suivi $suivi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('suivi/edit.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }
    #[Route('/supprimer-suivi/{id}', name: 'app_suivi_delete_custom', methods: ['GET','POST'])]
    public function deletesite(Request $request,Suivi $suivi, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        $client = $suivi->getLeclient();
        $hall = $suivi->getHall();
        $evenement = $suivi->getEvenement();

        if($client!=null){
            $client->removeSuivi($suivi);
        }
        if($hall!=null){
            $hall->removeLessuivi($suivi);
        }
        if($evenement!=null){
            $evenement->removeSuivi($suivi);
        }


        $entityManager->remove($suivi);

        $entityManager->flush();

        return $this->redirectToRoute('admin_suivi', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_suivi_delete', methods: ['POST'])]
    public function delete(Request $request, Suivi $suivi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suivi->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($suivi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
    }
}
