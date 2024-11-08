<?php

namespace App\Controller;

use App\Entity\Formulairedechethuile;
use App\Form\FormulairedechethuileType;
use App\Repository\FormulairedechethuileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formulairedechethuile')]
class FormulairedechethuileController extends AbstractController
{
    #[Route('/', name: 'app_formulairedechethuile_index', methods: ['GET'])]
    public function index(Request $request,FormulairedechethuileRepository $formulairedechethuileRepository,PaginatorInterface $paginator): Response
    {
        $formulaires =$formulairedechethuileRepository->findBy([], ['nomhall' => 'ASC']);
        $user = $this->getUser();
        $pageslform = $paginator->paginate(
            $formulaires,
            $request->query->getInt('page',1),10
        );
        return $this->render('formulairedechethuile/index.html.twig', [
            'formulairedechethuiles' => $pageslform,
        ]);
    }

    #[Route('/new', name: 'app_formulairedechethuile_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulairedechethuile = new Formulairedechethuile();
        $form = $this->createForm(FormulairedechethuileType::class, $formulairedechethuile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulairedechethuile);
            $entityManager->flush();

            return $this->redirectToRoute('app_formulairedechethuile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulairedechethuile/new.html.twig', [
            'formulairedechethuile' => $formulairedechethuile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formulairedechethuile_show', methods: ['GET'])]
    public function show(Formulairedechethuile $formulairedechethuile): Response
    {
        return $this->render('formulairedechethuile/show.html.twig', [
            'formulairedechethuile' => $formulairedechethuile,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formulairedechethuile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formulairedechethuile $formulairedechethuile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulairedechethuileType::class, $formulairedechethuile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formulairedechethuile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulairedechethuile/edit.html.twig', [
            'formulairedechethuile' => $formulairedechethuile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formulairedechethuile_delete', methods: ['POST'])]
    public function delete(Request $request, Formulairedechethuile $formulairedechethuile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formulairedechethuile->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($formulairedechethuile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formulairedechethuile_index', [], Response::HTTP_SEE_OTHER);
    }
}
