<?php

namespace App\Controller;

use App\Entity\Formulaire;
use App\Form\FormulaireType;
use App\Repository\FormulaireRepository;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/formulaire')]
class FormulaireController extends AbstractController
{
    private Dompdf $domPdf;

    public function __construct(Dompdf $domPdf)
    {
        $this->domPdf = $domPdf;
    }

    #[Route('/', name: 'app_formulaire_index', methods: ['GET'])]
    public function index(Request $request,FormulaireRepository $formulaireRepository,PaginatorInterface $paginator): Response
    {   $formulaires = $formulaireRepository->findAll();
        $pagiformulaires = $paginator->paginate(
            $formulaires,
            $request->query->getInt('page',1),8
        );
        return $this->render('formulaire/index.html.twig', [
            'formulaires' => $pagiformulaires
        ]);
    }

    #[Route('/new', name: 'app_formulaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulaire = new Formulaire();
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulaire/new.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formulaire_show', methods: ['GET'])]
    public function show(Formulaire $formulaire,PdfService $pdf): Response

    {
        return $this->render('formulaire/show.html.twig', [
            'formulaire' => $formulaire,
        ]);
    }




    #[Route('/{id}/edit', name: 'app_formulaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulaire/edit.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formulaire_delete', methods: ['POST'])]
    public function delete(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formulaire->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($formulaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
