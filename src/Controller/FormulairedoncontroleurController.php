<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Repository\EvenementRepository;
use App\Repository\HallRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FormulairedoncontroleurController extends AbstractController
{
    #[Route('/formulairedoncontroleur', name: 'app_formulairedoncontroleur')]
    public function index(): Response
    {
        return $this->render('formulairedoncontroleur/index.html.twig', [
            'controller_name' => 'FormulairedoncontroleurController',
        ]);
    }
    #[Route('/formulairedon', name: 'app_formulaire2')]
    public function index2(): Response
    {

        return $this->render('accueil/accueil2.html.twig', [
        ]);
    }

    #[Route('/formulairedon/{id}/{name}', name: 'app_lien_donation')]
    public function formulairededonperso(int $id, string $name): Response
    {
        return $this->render('accueil/accueil2.html.twig', [
            'idevenement' => $id,
            'nomevenement' => $name,
        ]);
    }
    #[Route('/submitformulaire', name: 'app_submit_formulaire')]
    public function submitForm(HallRepository $hallRepository,EvenementRepository $evenementRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $donation = new Donation();
        $idevenement = $request->request->get('idevenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$idevenement]);
        $idhall= $request->request->get('hall');
        $dateTime = new \DateTime('now', new DateTimeZone('Europe/Paris'));
        $dateTime->setTime(0,0,0);
        $donation->setCompanyName($request->request->get('companyName'));
        $donation->setSensibilisation($request->request->get('sensiblisation') === 'yes');
        $donation->setHall($idhall);
        $donation->setHallentity($hallRepository->findOneBy(['id'=>$idhall]));
        $donation->setAisleBoothNumber($request->request->get('aisleBoothNumber'));
        $donation->setContactBuilder($request->request->get('contactBuilder'));
        $donation->setDateupload($dateTime);
        $donation->setCsrFormDownloaded($request->request->get('csrFormDownloaded') === 'yes');
        $donation->setDonateMaterials($request->request->get('donateMaterials') === 'yes');
        $donation->setDonateWood($request->request->get('donateWood') === 'yes');
        $donation->setWoodTypes($request->request->all('woodTypes'));
        $donation->setWoodQuantities($request->request->all('woodQuantities'));
        $donation->setFurnitureQuantity($request->request->get('furnitureQuantity'));
        $donation->setOtherMaterialsQuantity($request->request->get('otherMaterialsQuantity'));
        $donation->setComments($request->request->get('comments'));
        $donation->setEvenement($evenement);

        // Enregistrer les données dans la base de données
        $entityManager->persist($donation);
        $entityManager->flush();

        return $this->redirectToRoute('app_remerciement_formulaire');
    }
    #[Route('/remerciements', name: 'app_remerciement_formulaire')]
    public function thankYou(): Response
    {
        return $this->render('accueil/pageremerciement.html.twig');
    }
}
