<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Evenement;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->getPayload()->get('_token'))) {
           $leshalls = $client->getLeshalls();
            if(!$leshalls->isEmpty()){

            foreach ($leshalls as $hall){
                $client->removeLeshall($hall);
            }

           }
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/supprimer-client/{id}', name: 'app_client_delete_custom', methods: ['GET','POST'])]
    public function deleteclient(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
            $leshalls = $client->getLeshalls();
            $lessites = $client->getSites();
            $lesuivis = $client->getSuivis();

            if(!$lessites->isEmpty()){

            foreach ($lessites as $site){
                $client->removeSite($site);
            }

        }

        if(!$lesuivis->isEmpty()){

            foreach ($lesuivis as $suivi){
                $client->removeSuivi($suivi);
            }

        }
            if(!$leshalls->isEmpty()){

                foreach ($leshalls as $hall){
                    $client->removeLeshall($hall);
                }

            }
            $entityManager->remove($client);
            $entityManager->flush();


        return $this->redirectToRoute('admin_client', [], Response::HTTP_SEE_OTHER);
    }

}
