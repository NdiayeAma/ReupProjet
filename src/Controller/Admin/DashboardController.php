<?php

namespace App\Controller\Admin;


use App\Controller\ClientController;
use App\Controller\SuiviController;
use App\Entity\Bacs;
use App\Entity\Centredetri;
use App\Entity\Client;
use App\Entity\Commentaire;
use App\Entity\Donation;
use App\Entity\Evenement;
use App\Entity\Formulaire;
use App\Entity\Hall;
use App\Entity\Inventaire;
use App\Entity\Materiels;
use App\Entity\Photosarchives;
use App\Entity\Site;
use App\Entity\Suivi;
use App\Entity\Suivicentredetri;
use App\Entity\User;
use App\Repository\CentredetriRepository;
use App\Repository\ClientRepository;
use App\Repository\DonationRepository;
use App\Repository\EvenementRepository;
use App\Repository\FormulaireRepository;
use App\Repository\HallRepository;
use App\Repository\PhotosarchivesRepository;
use App\Repository\SiteRepository;
use App\Repository\SuivicentredetriRepository;
use App\Repository\SuiviRepository;
use App\Repository\UserRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Intervention\Image\Image;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Config\KnpSnappyConfig;
use Tinify\Tinify;
use Twig\Environment;
use function Sodium\add;
use Pontedilana\PhpWeasyPrint\Pdf;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[IsGranted('IS_AUTHENTICATED_FULLY')]
class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        //  $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        //  return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('accueil/choixsuivi.html.twig');
    }
    private $domPdf;
    public function __construct(Dompdf $domPdf)
    {
        $this->domPdf = $domPdf;
    }
    #[\Symfony\Component\Routing\Attribute\Route('/pageFichierPersonnel', name: 'pageFichierPersonnel')]
    public function Pagefichierpersonnel(Request $request,UserRepository $userRepository,PaginatorInterface $paginator): Response
    {
        return $this->render('agent/pageFichierPersonnel.html.twig');
    }
    #[\Symfony\Component\Routing\Attribute\Route('/pageagentspourdoc', name: 'pageagentspourdoc')]
    public function listeagentsavecdocs(Request $request,UserRepository $userRepository,PaginatorInterface $paginator): Response
    {

        $listeusers = $userRepository->findUsers('ROLE_AGENT');



        return $this->render('agent/listeagents.html.twig',[
            'agents' => $listeusers,
        ]);
    }



    #[Route('/creerclient', name: 'app_formulaire_client')]
    public function creerclient(SiteRepository $siteRepository): Response
    {
        $site = $siteRepository->findAll();
        return $this->render('client/new.html.twig', [
            'sites' => $site

        ]);
    }
    #[Route('/suiviphotocentredetri', name: 'app_suiviphotocentredetri')]
    public function suiviphotocentredetri(ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();
        return $this->render('accueil/suivicentredetriphotos.html.twig', [
            'clients'=>$clients
        ]);
    }

    /**
     * Route pour exporter les données (sans centres de tri ni photos)
     */
    #[Route('/exportdonations', name: 'export_donations')]
    public function exportDonations(
        ClientRepository $clientRepository,
        EvenementRepository $eventRepository,
        SiteRepository $siteRepository,
        DonationRepository $donationRepository,
        Request $request
    ): Response {
        // Récupérer les paramètres depuis la requête
        $clientId = $request->request->get('client');
        $evenementid = $request->request->get('evenement');

        $evenement = $eventRepository->findOneBy(['id' => $evenementid]);

        $woodType = $request->request->get('woodType');
       if($woodType != null){
        $donations = $donationRepository->findByEvenementAndWoodType($evenement, $woodType);
       }
       else{
           $donations =$evenement->getDonations();
       }
        $donationsByHall = [];
        $labels = [];
        $datasets = [];
        $count = [];

        foreach ($donations as $donation) {
            $hall = $donation->getHall();
            if (!isset($donationsByHall[$hall])) {
                $donationsByHall[$hall] = [];
                $count[$hall] = 0;
            }
            $donationsByHall[$hall][] = $donation;
            $count[$hall] += 1;


        }
        foreach ($labels as $label){
            $datasets[] = $count[$label];
        }
        $siteId = $request->request->get('site');
        $typeContenant = $request->request->get('contenant');
        $startDate = $request->request->get('start_date');

        // Récupérer les entités correspondantes
        $client = $clientRepository->findOneBy(['id' => $clientId]);
        $site = $siteRepository->findOneBy(['id' => $siteId]);
        $nomsite = $site->getNom();
        // Générer une date (si nécessaire)
        $date = null;
        if ($startDate) {
            $date = new \DateTime($startDate);
        }

        // Effectuer ici des actions spécifiques selon vos besoins, par exemple :
        // Filtrer des donations par client, site, événement, etc.
        // Pour l'instant, nous allons simplement passer ces variables à la vue.

        return $this->render('donation_search/export_donations.html.twig', [
            'client' => $client,
            'evenement' => $evenement,
            'site' => $site,
            'type_contenant' => $typeContenant,
            'start_date' => $date,
            'donationsByHall' => $donationsByHall,
            'labels'=>$labels,
            'datasets'=>$datasets
        ]);
    }
    #[Route('/creerevenement', name: 'app_formulaire_evenement')]
    public function creerevenement(SiteRepository $siteRepository,ClientRepository $clientRepository): Response
    {
        $site = $siteRepository->findAll();
        $clients = $clientRepository->findAll();
        return $this->render('evenement/new.html.twig', [
            'sites' => $site,
            'clients'=>$clients
        ]);
    }

    #[Route('/gererhall', name: 'app_gererhall')]
    public function gererhall(EntityManagerInterface $entityManager,HallRepository $hallRepository,DonationRepository $donationRepository): Response
    {
        $donation = $donationRepository->findAll();
        foreach ($donation as $d){
            $hall = $hallRepository->find(["id"=>intval($d->getHall())]);
            $d->setHallentity($hall);
            $hall->addDonation($d);
            $entityManager->persist($hall);

        }
        return $this->render('accueil/pageremerciement.html.twig');

    }
    #[Route('/donations/graph', name: 'app_donations_graph')]
    public function donationsGraph(
        EvenementRepository $eventRepository,
        HallRepository $hallRepository,
        DonationRepository $donationRepository,
        Request $request
    ): Response {
        $evenementId = $request->query->get('evenementid');
        $evenement = $eventRepository->findOneBy(['id' => $evenementId]);
        $woodType = $request->request->get('woodType');

        // Récupérer les donations en fonction du type de bois s'il est spécifié
        if ($woodType != null) {
            $donations = $donationRepository->findByEvenementAndWoodType($evenement, $woodType);
        } else {
            $donations = $evenement->getDonations();
        }

        // Objectifs pour chaque hall
        $objectives = [
            'HALL 1' => 572,
            'HALL 2.2' => 138,
            'HALL 2.3' => 9,
            'HALL 3' => 337,
            'HALL 4' => 147,
            'HALL 5.1' => 26,
            'HALL 5.2' => 49,
            'HALL 5.3' => 40,
            'HALL 6' => 137,
            'HALL 7.2' => 254,
        ];

        // Initialiser les donations et donations acceptées pour chaque hall à 0
        $donationsByHall = array_fill_keys(array_keys($objectives), 0);
        $acceptedDonationsByHall = array_fill_keys(array_keys($objectives), 0); // Nouvelle colonne pour les donations acceptées

        // Compter les donations par hall et les donations acceptées
        foreach ($donations as $donation) {
            $hallId = $donation->getHall();
            $hall = $hallRepository->find($hallId);

            if ($hall) {
                $hallName = $hall->getNom();

                if (!isset($totalDonationsByHall[$hallName])) {
                    $totalDonationsByHall[$hallName] = 0;
                    $donationsByHall[$hallName] = [];
                    $acceptedDonationsByHall[$hallName] = 0;  // Initialise le compteur de donations acceptées
                }

                // Ajouter la donation au tableau
                $totalDonationsByHall[$hallName]++;
                $donationsByHall[$hallName][] = $donation;

                // Si le don est accepté, incrémenter le compteur des donations acceptées
                if ($donation->getDonateMaterials() == 1) {
                    $acceptedDonationsByHall[$hallName]++;
                }
            }
        }

        // Préparer les données pour le graphique
        $labels = array_keys($objectives);
        $donationsData = [];
        $objectivesData = [];
        $acceptedDonationsData = []; // Nouvelle colonne pour les donations acceptées

        // Remplir les tableaux de données pour le graphique
        foreach ($labels as $hall) {
            // Vérifie si le hall existe dans les donations avant de l'ajouter au tableau
            $donationsData[] = isset($totalDonationsByHall[$hall]) ? $totalDonationsByHall[$hall] : 0; // Si le hall n'existe pas, on met 0
            $objectivesData[] = $objectives[$hall]; // Objectif pour ce hall
            $acceptedDonationsData[] = isset($acceptedDonationsByHall[$hall]) ? $acceptedDonationsByHall[$hall] : 0; // Donations acceptées
        }

        // Préparer les données à passer à la vue
        $chartData = [
            'labels' => $labels,
            'donations' => $donationsData,  // Les donations par hall
            'objectives' => $objectivesData, // Les objectifs par hall
            'acceptedDonations' => $acceptedDonationsData  // Les donations acceptées par hall
        ];

        // Rendre la vue avec les données du graphique
        return $this->render('donation_search/donations_graph.html.twig', [
            'chartData' => $chartData,
        ]);
    }


    #[Route('/admin/inventaire', name: 'admin_inventaire')]
    public function inventaire(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(InventaireCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //

    }
    #[Route('/admin/photoarchives', name: 'admin_photoarchives')]
    public function index16(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(PhotosarchivesCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //

    }
    #[Route('/admin/interfacesuivi', name: 'admin_interfacesuivi')]
    public function index8(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(SuiviCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //

    }
        #[Route('/admin/site', name: 'admin_site')]
    public function index3(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(SiteCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }
    #[Route('/admin/commentaire', name: 'admin_commentaire')]
    public function index7(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(CommentaireCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }
    #[Route('/admin/client', name: 'admin_client')]
    public function index2(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ClientCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }
    #[Route('/admin/suivi-centre-de-tri', name: 'suivicentredetricrud')]
    public function index5(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(SuivicentredetriCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    #[Route('/admin/evenement', name: 'admin_evenement')]
    public function index9(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(EvenementCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }
    #[Route('/afficherphotos/{id}', name: 'app_suivi_photo', methods: ['GET','POST'])]
    public function photosuivi(Request $request,Suivi $suivi, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        $photos = $suivi->getPhotosarchives();
        return $this->render('accueil/photos.html.twig', [
            'photos'=>$photos,
        ]);

    }
    #[Route('/ajouterphotosuivi/{id}', name: 'ajouter_suivi_photo', methods: ['GET','POST'])]
    public function ajouterphotosuivi(Request $request,Suivi $suivi, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {

        return $this->render('suivi/ajoutphotosuivi.html.twig', [
            'suivi'=>$suivi,
        ]);

    }
    #[Route('/ajouterphoto-form', name: 'ajouterphoto-form', methods: ['GET','POST'])]
    public function ajouterphotoform(Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {

        return $this->render('accueil/ajoutphotomultiple.html.twig');

    }
    #[Route('/photoscommentaire/{id}', name: 'app_commentaire_photo', methods: ['GET','POST'])]
    public function photocommentaire(Request $request,Commentaire $commentaire, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        $photos = $commentaire->getPhotos();
        return $this->render('accueil/photos.html.twig', [
            'photos'=>$photos,
        ]);

    }
    #[Route('/ajoutphotoscommentaire/{id}', name: 'app_ajout_commentaire_photo', methods: ['GET','POST'])]
    public function ajoutphotocommentaire(Request $request,Commentaire $commentaire, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {

        $evenement = $commentaire->getEvenement();

        return $this->render('evenement/ajoutphotocommentaire.html.twig', [
            'evenement'=>$evenement,
            'commentaire'=>$commentaire
        ]);

    }
    #[Route('/recherchepdfphotosuivi', name: 'app_recherche_photosuivi', methods: ['GET','POST'])]
    public function recherchepdfphotosuivi(ClientRepository $clientRepository ,SuiviRepository $suiviRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $clients= $clientRepository->findAll();
        return $this->render('accueil/recherchepdfphotosuivi.html.twig', [
            'clients' => $clients,
        ]);
    }


    #[Route('/exportpdfphotosuivi', name: 'app_exportpdfphotosuivi')]
    public function exportpdfphotosuivi(PhotosarchivesRepository $photosarchivesRepository,CentredetriRepository $centredetriRepository,Request $request,EvenementRepository $evenementRepository,SiteRepository $siteRepository,ClientRepository $clientRepository,HallRepository $hallRepository): Response
    {
        $evenementid = $request->query->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$evenementid]);
        // Récupérer les paramètres de recherche depuis la requête
        $clientId = $request->query->get('client');
        $siteid = $request->request->get('site');
        $client = $clientRepository->findOneBy(['id'=>$clientId]);
        $centredetrid = $request->request->get('centredetri');
        $date1 = $request->request->get('start_date');
        $date = new \DateTime($date1);
        $previouscentre = null;
        $site = $siteRepository->findOneBy(['id'=>$siteid]);
        $nomsite=$site->getNom();



        $projectDir = $this->getParameter('kernel.project_dir');
        if($centredetrid != null){
            $photosByCentreDetri = [];

                $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
                $previouscentre = $centredetri;
                if($date == null){
                    $photos = $photosarchivesRepository->findPhotosByCentre($centredetri);

                }else {
                    $photos = $photosarchivesRepository->findBy(['centredetri'=>$centredetri,'dateupload'=>$date]);
                }
                foreach ($photos as $photo) {
                    $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                        if (!isset($photosByCentreDetri[$centredetri->getNom()])) {
                            $photosByCentreDetri[$centredetri->getNom()] = [];
                        }

                        $photosByCentreDetri[$centredetri->getNom()][] = [
                            'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                            'base64String' => $base64,
                        ];
                    }
                }



        }else{
            $photosByCentreDetri = [];
            $centredetri = $centredetriRepository->findAll();
            foreach ($centredetri as $centreDetri) {
                if($date == null){
                    $photos = $photosarchivesRepository->findPhotosByCentre($centreDetri);

                }else {
                    $photos = $photosarchivesRepository->findBy(['centredetri'=>$centredetri,'dateupload'=>$date]);
                }
                foreach ($photos as $photo) {
                    $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                        if (!isset($photosByCentreDetri[$centreDetri->getNom()])) {
                            $photosByCentreDetri[$centreDetri->getNom()] = [];
                        }

                        $photosByCentreDetri[$centreDetri->getNom()][] = [
                            'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                            'base64String' => $base64,
                        ];
                    }
                }
            }

        }

        // Renvoyer les résultats au Twig
        return $this->render('accueil/visualisationpdf.html.twig', [
        'photosByCentreDetri' => $photosByCentreDetri,
            'date'=>$date1,
            'previousclient'=> $clientId,
            'previousevenement'=>$evenementid,
            'previouscentredetri'=>$previouscentre,
            'previoussite'=>$site,
    ]);
    }

    #[Route('/app_exportpdfphotosuivi', name: 'app_exportpdfphotosuivi2')]
    public function exportpdfphotosuivi2(PhotosarchivesRepository $photosarchivesRepository,CentredetriRepository $centredetriRepository,Request $request,EvenementRepository $evenementRepository,SiteRepository $siteRepository,ClientRepository $clientRepository,HallRepository $hallRepository): Response
    {
        $evenementid = $request->query->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$evenementid]);
        // Récupérer les paramètres de recherche depuis la requête
        $clientId = $request->query->get('client');
        $siteid = $request->request->get('site');
        $site = $siteRepository->findOneBy(['id' => $siteid ]);
        $nomsite= $site->getNom();
        $client = $clientRepository->findOneBy(['id'=>$clientId]);
        $centredetrid = $request->request->get('centredetri');
        $date1 = $request->request->get('start_date');
        $date = new \DateTime($date1);

        $projectDir = $this->getParameter('kernel.project_dir');
        if($centredetrid != null){
            $photosByCentreDetri = [];

            $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
            if($date == null){
                $photos = $photosarchivesRepository->findPhotosByCentre($centredetri);

            }else {
                $photos = $photosarchivesRepository->findBy(['centredetri'=>$centredetri,'dateupload'=>$date]);
            }
            foreach ($photos as $photo) {
                $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                    if (!isset($photosByCentreDetri[$centredetri->getNom()])) {
                        $photosByCentreDetri[$centredetri->getNom()] = [];
                    }

                    $photosByCentreDetri[$centredetri->getNom()][] = [
                        'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                        'base64String' => $base64,
                    ];
                }
            }



        }else{
            $photosByCentreDetri = [];
            $centredetri = $centredetriRepository->findAll();
            foreach ($centredetri as $centreDetri) {
                if($date == null){
                    $photos = $photosarchivesRepository->findPhotosByCentre($centreDetri);

                }else {
                    $photos = $photosarchivesRepository->findPhotosByDateAndCentre($date,$centreDetri);
                }
                foreach ($photos as $photo) {
                    $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                        if (!isset($photosByCentreDetri[$centreDetri->getNom()])) {
                            $photosByCentreDetri[$centreDetri->getNom()] = [];
                        }

                        $photosByCentreDetri[$centreDetri->getNom()][] = [
                            'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                            'base64String' => $base64,
                        ];
                    }
                }
            }

        }

        $html = $this->render('accueil/visualisationpdf.html.twig', [
            'photosByCentreDetri' => $photosByCentreDetri,
            'date'=>$date1,
            'previousclient'=> $clientId,
            'previousevenement'=>$evenementid,
            'previouscentredetri'=>null,
            'previoussite'=>$site,
        ]);

        // Configurer Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', realpath(__DIR__ . '/../../public/uploads/bachenoire.png'));
        $options->setTempDir('temp');
        $this->domPdf->setOptions($options);
        $this->domPdf->setBasePath('uploads');
        $this->domPdf->setPaper([0, 0, 595.28, 841.89 * 3]); // Largeur A4 et hauteur ajustée (3 fois la hauteur A4 ici, à ajuster)

        // Charger le contenu HTML et générer le PDF
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();

        // Créer une réponse Symfony avec le PDF
        $response = new Response(
            $this->domPdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
            ]
        );

        // Ajouter les en-têtes pour forcer le téléchargement
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $nomsite.'-suivi des photos-'.$date1.'.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;


    }

    #[Route('/interfacesynthese', name: 'app_synthese')]
    public function interfaceaccueil(ClientRepository $clientRepository,HallRepository $hallRepository): Response
    {
        $clients= $clientRepository->findAll();
        $halls = $hallRepository->findAll();
        return $this->render('accueil/synthesegenerale.html.twig', [
            'clients'=>$clients,
            'halls'=>$halls,
            'previousclient'=> null,
            'previousevenement'=>null,
            'previoushall'=>null,
            'previousmatiere'=>null,
            'previoussite'=>null,
            'cumul_poids'=>null,
            'datedebut'=>null,
            'datedefin'=>null,
        ]);
    }
    #[\Symfony\Component\Routing\Attribute\Route('/pdf-synthesegenerale', name: 'app_synthesegenerale_pdf')]
    public function gererpdfsynthesegenerale(SiteRepository $siteRepository,CentredetriRepository $centredetriRepository, EvenementRepository $evenementRepository, FormulaireRepository $formulaireRepository, Request $request, ClientRepository $clientRepository, HallRepository $hallRepository, SuiviRepository $suiviRepository):Response
    {
        // Convertir l'image en base64
        $projectDir = $this->getParameter('kernel.project_dir');
        $images = [
            'Carton' => $this->base64EncodeImage($projectDir . '/assets/images/boite-de-livraison.png'),
            'Plastique souple' => $this->base64EncodeImage($projectDir . '/assets/images/poubelle.png'),
            'Bois' => $this->base64EncodeImage($projectDir . '/assets/images/journaux.png'),
            'PET' => $this->base64EncodeImage($projectDir . '/assets/images/equipement.png'),
            'Canettes' => $this->base64EncodeImage($projectDir . '/assets/images/canette-de-soda.png'),
            'Verre' => $this->base64EncodeImage($projectDir . '/assets/images/verre-deau.png'),
            'Moquette' => $this->base64EncodeImage($projectDir . '/assets/images/tapis.png'),
            'DR' => $this->base64EncodeImage($projectDir . '/assets/images/dechetrecyclable.png'),
            'Biodechet' => $this->base64EncodeImage($projectDir . '/assets/images/equipement.png'),
            'D3EDEEE' => $this->base64EncodeImage($projectDir . '/assets/images/entrepot.jpg'),
            'Déchets dangereux' => $this->base64EncodeImage($projectDir . '/assets/images/matieres-dangereuses.png'),
            'Catalogues et journaux' => $this->base64EncodeImage($projectDir . '/assets/images/nouvelles.png'),
            'Déchets médicaux' => $this->base64EncodeImage($projectDir . '/assets/images/dechetsmedicaux.png'),
            'Huiles usagées' => $this->base64EncodeImage($projectDir . '/assets/images/huile-pour-bebe.png'),
            'Bâche' => $this->base64EncodeImage($projectDir . '/assets/images/bache.png'),
            'Mobilier' => $this->base64EncodeImage($projectDir . '/assets/images/tiroir.png'),
            'default' => $this->base64EncodeImage($projectDir . '/assets/images/equipement.png'),
        ];


        // Récupérer les paramètres de recherche depuis la requête
        $clients= $clientRepository->findAll();
        $halls = $hallRepository->findAll();
        $evenementid = $request->query->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$evenementid]);
        // Récupérer les paramètres de recherche depuis la requête
        $clientId = $request->query->get('client');
        $siteid = $request->query->get('site');
        $site = $siteRepository->findOneBy(['id'=>$siteid]);
        $client = $clientRepository->findOneBy(['id'=>$clientId]);
        $matiere= $request->query->get('flux');
        $centredetrid = $request->query->get('centredetri');
        $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);


        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        $date = (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d');

        $cumulPoids = $suiviRepository->searchCumulPoids($client, $centredetri, $startDate, $endDate,$evenement,$matiere);
        $dateMin = $suiviRepository->getMinDate();
        $dateMax = $suiviRepository->getMaxDate();

        // Récupérer le contenu Twig
        $html = $this->renderView('accueil/synthesegeneralepdf.html.twig', [
            'clients'=>$clients,
            'halls'=>$halls,
            'previousclient'=> $client,
            'previousevenement'=>$evenement,
            'previoushall'=>$centredetri,
            'previoussite'=>$site,
            'previousmatiere'=>$matiere,
            'cumul_poids' => $cumulPoids['formatedResult'],
            'poidsTotal'=>$cumulPoids['poidsTotal'],
            'datedebut'=>$startDate,
            'datedefin'=>$endDate,
            'images'=>$images
        ]);

        // Configurer Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', realpath(__DIR__ . '/../../public/uploads/bachenoire.png'));
        $options->setTempDir('temp');
        $this->domPdf->setOptions($options);
        $this->domPdf->setBasePath('uploads');
        // Charger le contenu HTML et générer le PDF
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();

        // Créer une réponse Symfony avec le PDF
        $response = new Response(
            $this->domPdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
            ]
        );

        // Ajouter les en-têtes pour forcer le téléchargement
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'file.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;




    }
    #[Route('/search', name: 'search_results')]
    public function searchResults(CentredetriRepository $centredetriRepository,SiteRepository $siteRepository,Request $request,EvenementRepository $evenementRepository,SuiviRepository $suiviRepository,HallRepository $hallRepository,ClientRepository $clientRepository)
    {
        $clients= $clientRepository->findAll();
        $halls = $hallRepository->findAll();
        $evenementid = $request->query->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$evenementid]);
        // Récupérer les paramètres de recherche depuis la requête
        $clientId = $request->query->get('client');
        $siteid = $request->query->get('site');
        $site = $siteRepository->findOneBy(['id'=>$siteid]);
        $client = $clientRepository->findOneBy(['id'=>$clientId]);
        $matiere= $request->query->get('flux');
        $centredetrid = $request->query->get('centredetri');
        $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');


        // Effectuer la recherche en fonction des paramètres
        $cumulPoids = $suiviRepository->searchCumulPoids($client, $centredetri, $startDate, $endDate,$evenement,$matiere);

        // Renvoyer les résultats au Twig
        return $this->render('accueil/synthesegenerale.html.twig', [
            'clients'=>$clients,
            'halls'=>$halls,
            'previousclient'=> $client,
            'previousevenement'=>$evenement,
            'previoushall'=>$centredetri,
            'previoussite'=>$site,
            'previousmatiere'=>$matiere,
            'cumul_poids' => $cumulPoids['formatedResult'],
            'poidsTotal'=>$cumulPoids['poidsTotal'],
            'datedebut'=>$startDate,
            'datedefin'=>$endDate,
        ]);

    }
    #[\Symfony\Component\Routing\Attribute\Route('/submit-donneesagent', name: 'app_submit_donnees_agent')]
    public function formulairedonneesagent(Request $request,UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {

        $userlogin = $request->request->get('id');
        $user = $userRepository->findOneBy(['id'=>$userlogin]);

        // Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');

        $user->setNom($nom);
        $user->setPrenom($prenom);

        $datedenaissance1 = $request->request->get('datedenaissance');
        $datedenaissance = \DateTime::createFromFormat('Y-m-d', $datedenaissance1);

        $user->setDatedenaissance($datedenaissance);

        $lieudenaissance = $request->request->get('lieudenaissance');
        $adresse = $request->request->get('adresse');
        $contact = $request->request->get('contact');
        $rib = $request->files->get('rib');

        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

        if ($rib != null){


            $nomrib = uniqid() . '.' . $rib->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $rib->move(
                $uploadDirectory,
                $nomrib
            );
            $user->setRib($nomrib);
            $user->setRib($nomrib);
        }
        $siret = $request->request->get('siret');

        $user->setSiret($siret);
        $securiteSociale = $request->request->get('securite_sociale');
        $user->setSecuritesociale($securiteSociale);
        $email = $request->request->get('email');
        $user->setEmail($email);
        $contactUrgence = $request->request->get('contact_urgence');
        $user->setContacturgence($contactUrgence);
        $contactUrgenceTel = $request->request->get('contact_urgence_tel');
        $user->setContacturgencetel($contactUrgenceTel);
        $photo = $request->files->get('photo');

        if ($photo != null) {
            $nomphoto = uniqid() . '.' . $photo->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $photo->move(
                $uploadDirectory,
                $nomphoto
            );
            $user->setPhoto($nomphoto);
        }
        $permis = $request->files->get('permis');
        if ($permis != null) {

            $nompermis = uniqid() . '.' . $permis->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $permis->move(
                $uploadDirectory,
                $nompermis
            );
            $user->setPermis($nompermis);
        }
        $autres = $request->files->get('autres');
        $nomautres='';
        if($autres != null){
            foreach ($autres as $uploadedFile) {
                // Vérifier si le fichier a bien été téléchargé

                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadedFile->move(
                    $uploadDirectory,
                    $newFilename
                );
                $nomautres .= $newFilename . ' ';
            }
            $user->setAutres($nomautres);
        }




        $user->setLieudenaissance($lieudenaissance);
        $user->setAdresse($adresse);
        $user->setContact($contact);
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_gerer_profill_agent');

    }
    #[Route('/exportpdfphotosuivifinal', name: 'app_exportpdfphotosuivifinal')]
    public function exportpdfphotosuivifinal(
        PhotosarchivesRepository $photosarchivesRepository,
        CentredetriRepository $centredetriRepository,
        Request $request,
        EvenementRepository $evenementRepository,
        SiteRepository $siteRepository,
        ClientRepository $clientRepository,
        HallRepository $hallRepository
    ): Response {
        $evenementid = $request->query->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id' => $evenementid]);
        $clientId = $request->query->get('client');
        $siteid = $request->request->get('site');
        $typecontenant = $request->request->get('contenant');
        $site = $siteRepository->findOneBy(['id' => $siteid]);
        $nomsite = $site->getNom();
        $client = $clientRepository->findOneBy(['id' => $clientId]);
        $centredetrid = $request->request->get('centredetri');
        $previouscentre = null;
        $date1 = $request->request->get('start_date');
        $date = new \DateTime($date1);
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/logomillenium.png';
        $imagePath2 = $this->getParameter('kernel.project_dir') . '/public/images/reuplogo.png';
        $imagePath3 = $this->getParameter('kernel.project_dir') . '/public/images/LogoJO.png';
        $base64Image = $this->base64EncodeImage($imagePath);
        $base64Image2 = $this->base64EncodeImage($imagePath2);
        $base64Image3 = $this->base64EncodeImage($imagePath3);

        $projectDir = $this->getParameter('kernel.project_dir');
        if($centredetrid != null){
            $photosByCentreDetri = [];

            $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
            $previouscentre = $centredetri;

            if($date1 == null){
                if($typecontenant == null){
                    $photos = $photosarchivesRepository->findPhotosByCentre($centredetri);
                }else{
                    $photos = $photosarchivesRepository->findPhotosByCentreandtypecontenant($centredetri,$typecontenant);
                }

            }else {
                if($typecontenant == null) {
                    $photos = $photosarchivesRepository->findPhotosByDateAndCentre($date1, $centredetri);
                }else{
                    $photos = $photosarchivesRepository->findPhotosByDateAndCentreAndTypecontenant($date1, $centredetri,$typecontenant);

                }
            }
            foreach ($photos as $photo) {
                $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                if (file_exists($path)) {
                    $compressedPath = $this->compressImage($path); // Compresser l'image avant de la charger
                    $type = pathinfo($compressedPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($compressedPath);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                    if (!isset($photosByCentreDetri[$centredetri->getNom()])) {
                        $photosByCentreDetri[$centredetri->getNom()] = [];
                    }

                    $photosByCentreDetri[$centredetri->getNom()][] = [
                        'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                        'base64String' => $base64,
                    ];
                }
            }

        } else {
            $photosByCentreDetri = [];
            $centredetri = $centredetriRepository->findAll();
            foreach ($centredetri as $centreDetri) {
                if($date1 == null){
                    $photos = $photosarchivesRepository->findPhotosByCentre($centreDetri);

                }else {
                    $photos = $photosarchivesRepository->findPhotosByDateAndCentre($date1,$centreDetri);
                }
                foreach ($photos as $photo) {
                    $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                    if (file_exists($path)) {
                        $compressedPath = $this->compressImage($path); // Compresser l'image avant de la charger
                        $type = pathinfo($compressedPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($compressedPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                        if (!isset($photosByCentreDetri[$centreDetri->getNom()])) {
                            $photosByCentreDetri[$centreDetri->getNom()] = [];
                        }

                        $photosByCentreDetri[$centreDetri->getNom()][] = [
                            'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                            'base64String' => $base64,
                        ];
                    }
                }
            }
        }
        return $this->render('accueil/visualisationpdf.html.twig', [
            'photosByCentreDetri' => $photosByCentreDetri,
            'datedujour'=>$date1,
            'previousclient'=> $clientId,
            'previousevenement'=>$evenementid,
            'previouscentredetri'=>$previouscentre,
            'base64Image' => $base64Image,
            'base64Image2' => $base64Image2,
            'base64Image3' => $base64Image3,
            'previoussite'=>$site,
        ]);


    }

    #[\Symfony\Component\Routing\Attribute\Route('/visuevenement', name: 'app_visu_evenement')]
    public function redirectvisuevenement(EvenementRepository $evenementRepository): Response
    {
        $evenement =  $evenementRepository->findAll();

        return $this->render('accueil/visuevenement.html.twig', [
            'evenements' => $evenement]);
    }

    #[Route('/app_exportpdfphotosuivitraitement', name: 'app_exportpdfphotosuivitraitement')]
    public function app_exportpdfphotosuivitraitement(
        PhotosarchivesRepository $photosarchivesRepository,
        CentredetriRepository $centredetriRepository,
        Request $request,
        EvenementRepository $evenementRepository,
        SiteRepository $siteRepository,
        ClientRepository $clientRepository,
        HallRepository $hallRepository
    ): Response {
        $evenementid = $request->query->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id' => $evenementid]);
        $clientId = $request->query->get('client');
        $siteid = $request->request->get('site');
        $site = $siteRepository->findOneBy(['id' => $siteid]);
        $nomsite = $site->getNom();
        $client = $clientRepository->findOneBy(['id' => $clientId]);
        $centredetrid = $request->request->get('centredetri');
        $previouscentre = null;
        $date1 = $request->request->get('start_date');
        $date = new \DateTime($date1);
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/logomillenium.png';
        $imagePath2 = $this->getParameter('kernel.project_dir') . '/public/images/reuplogo.png';
        $imagePath3 = $this->getParameter('kernel.project_dir') . '/public/images/LogoJO.png';
        $base64Image = $this->base64EncodeImage($imagePath);
        $base64Image2 = $this->base64EncodeImage($imagePath2);
        $base64Image3 = $this->base64EncodeImage($imagePath3);

        $projectDir = $this->getParameter('kernel.project_dir');
        if($centredetrid != null){
            $photosByCentreDetri = [];

            $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
            $previouscentre = $centredetri;

            if($date1 == null){
                $photos = $photosarchivesRepository->findPhotosByCentre($centredetri);

            }else {
                $photos = $photosarchivesRepository->findPhotosByDateAndCentre($date1,$centredetri);
            }
            foreach ($photos as $photo) {
                $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                if (file_exists($path)) {
                    $compressedPath = $this->compressImage($path); // Compresser l'image avant de la charger
                    $type = pathinfo($compressedPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($compressedPath);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                    if (!isset($photosByCentreDetri[$centredetri->getNom()])) {
                        $photosByCentreDetri[$centredetri->getNom()] = [];
                    }

                    $photosByCentreDetri[$centredetri->getNom()][] = [
                        'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                        'base64String' => $base64,
                    ];
                }
            }

        } else {
            $photosByCentreDetri = [];
            $centredetri = $centredetriRepository->findAll();
            foreach ($centredetri as $centreDetri) {
                if($date1 == null){
                    $photos = $photosarchivesRepository->findPhotosByCentre($centreDetri);

                }else {
                    $photos = $photosarchivesRepository->findPhotosByDateAndCentre($date1,$centreDetri);
                }
                foreach ($photos as $photo) {
                    $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                    if (file_exists($path)) {
                        $compressedPath = $this->compressImage($path); // Compresser l'image avant de la charger
                        $type = pathinfo($compressedPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($compressedPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        // Ajouter l'objet Photo et l'image base64 dans le tableau par centre de tri
                        if (!isset($photosByCentreDetri[$centreDetri->getNom()])) {
                            $photosByCentreDetri[$centreDetri->getNom()] = [];
                        }

                        $photosByCentreDetri[$centreDetri->getNom()][] = [
                            'objetPhoto' => $photo, // Ajouter l'objet Photo complet
                            'base64String' => $base64,
                        ];
                    }
                }
            }
        }

        $html = $this->render('accueil/visualisationpdfinal.html.twig', [
            'photosByCentreDetri' => $photosByCentreDetri,
            'datedujour'=>$date1,
            'previousclient'=> $clientId,
            'previousevenement'=>$evenementid,
            'previouscentredetri'=>$previouscentre,
            'base64Image' => $base64Image,
            'base64Image2' => $base64Image2,
            'base64Image3' => $base64Image3,
            'previoussite'=>$site,
        ])->getContent();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('dpi', 96);
        $options->set('isPhpEnabled', false);
        $options->set('chroot', realpath($projectDir . '/public/uploads'));
        $options->setTempDir($projectDir . '/var/tmp');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 595.28, 841.89 * 3]);
        $dompdf->render();

        $response = new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $nomsite . '-suivi_des_photos-' . $date1 . '.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    private function compressImage(string $source): string
    {
        $info = getimagesize($source);
        $destination = tempnam(sys_get_temp_dir(), 'compressed_') . image_type_to_extension($info[2]);

        if ($info['mime'] === 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, 35);
        } elseif ($info['mime'] === 'image/png') {
            $image = imagecreatefrompng($source);
            imagepng($image, $destination, 9);
        }

        imagedestroy($image);

        return $destination;
    }

#[Route('/app_exportpdfphotosuivi3', name: 'app_exportpdfphotosuivi3')]
public function exportpdfphotosuivi3(
    PhotosarchivesRepository $photosarchivesRepository,
    CentredetriRepository $centredetriRepository,
    Request $request,
    EvenementRepository $evenementRepository,
    SiteRepository $siteRepository,
    ClientRepository $clientRepository,
    HallRepository $hallRepository
): Response {
    $evenementid = $request->query->get('evenement');
    $evenement = $evenementRepository->findOneBy(['id' => $evenementid]);
    $clientId = $request->query->get('client');
    $siteid = $request->request->get('site');
    $site = $siteRepository->findOneBy(['id' => $siteid]);
    $nomsite = $site->getNom();
    $client = $clientRepository->findOneBy(['id' => $clientId]);
    $centredetrid = $request->request->get('centredetri');
    $date1 = $request->request->get('start_date');
    $date = new \DateTime($date1);

    $photosByCentreDetri = [];
    if ($centredetrid != null) {
        $centredetri = $centredetriRepository->findOneBy(['id' => $centredetrid]);
        if ($date == null) {
            $photos = $photosarchivesRepository->findPhotosByCentre($centredetri);
        } else {
            $photos = $photosarchivesRepository->findBy(['centredetri' => $centredetri, 'dateupload' => $date]);
        }

        foreach ($photos as $photo) {
            $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
            if (file_exists($path)) {
                $compressedBase64 = $this->compressAndEncodeImage($path);
                if (!isset($photosByCentreDetri[$centredetri->getNom()])) {
                    $photosByCentreDetri[$centredetri->getNom()] = [];
                }
                $photosByCentreDetri[$centredetri->getNom()][] = [
                    'objetPhoto' => $photo,
                    'base64String' => $compressedBase64,
                ];
            }
        }
    } else {
        $centredetri = $centredetriRepository->findAll();
        foreach ($centredetri as $centreDetri) {
            if ($date == null) {
                $photos = $photosarchivesRepository->findPhotosByCentre($centreDetri);
            } else {
                $photos = $photosarchivesRepository->findPhotosByDateAndCentre($date, $centreDetri);
            }

            foreach ($photos as $photo) {
                $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $photo->getNom();
                if (file_exists($path)) {
                    $compressedBase64 = $this->compressAndEncodeImage($path);
                    if (!isset($photosByCentreDetri[$centreDetri->getNom()])) {
                        $photosByCentreDetri[$centreDetri->getNom()] = [];
                    }
                    $photosByCentreDetri[$centreDetri->getNom()][] = [
                        'objetPhoto' => $photo,
                        'base64String' => $compressedBase64,
                    ];
                }
            }
        }
    }

    $html = $this->render('accueil/visualisationpdf.html.twig', [
        'photosByCentreDetri' => $photosByCentreDetri,
        'date' => $date1,
        'previousclient' => $clientId,
        'previousevenement' => $evenementid,
        'previouscentredetri' => null,
        'previoussite' => $site,
    ]);

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('chroot', realpath(__DIR__ . '/../../public/uploads/bachenoire.png'));
    $options->setTempDir('temp');
    $this->domPdf->setOptions($options);
    $this->domPdf->setBasePath('uploads');
    $this->domPdf->setPaper([0, 0, 595.28, 841.89 * 3]);

    $this->domPdf->loadHtml($html);
    $this->domPdf->render();

    $response = new Response(
        $this->domPdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
        ]
    );

    $disposition = HeaderUtils::makeDisposition(
        HeaderUtils::DISPOSITION_ATTACHMENT,
        $nomsite . '-suivi des photos-' . $date1 . '.pdf'
    );
    $response->headers->set('Content-Disposition', $disposition);

    return $response;
}
    private function compressAndEncodeImage(string $path): string
    {
        $source = Tinify::fromFile($path);
        $compressedPath = $path . '_compressed.jpg';
        $source->toFile($compressedPath);

        $data = file_get_contents($compressedPath);
        return 'data:image/jpeg;base64,' . base64_encode($data);
    }


    #[Route('/recherchesynthesedetaille', name: 'app_recherche_synthese_detaille')]
    public function recherchesynthesedetaille(ClientRepository $clientRepository,HallRepository $hallRepository): Response
    {  $clients= $clientRepository->findAll();

        return $this->render('accueil/synthesedetaillerecherche.html.twig', [
            'clients'=>$clients,

        ]);
    }
    private function base64EncodeImage($imagePath)
    {
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
        $imageData = file_get_contents($imagePath);
        return 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
    }
    /*
       #[Route('/synthesedetaille', name: 'app_synthese_detaille')]
       public function synthesedetaille(Request $request,CentredetriRepository $centredetriRepository,EvenementRepository $evenementRepository,SuiviRepository $suiviRepository,ClientRepository $clientRepository,HallRepository $hallRepository): Response
       {
           // Convertir l'image en base64
           $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/logomillenium.png';
           $imagePath2 = $this->getParameter('kernel.project_dir') . '/public/images/reuplogo.png';

           $base64Image = $this->base64EncodeImage($imagePath);
           $base64Image2 = $this->base64EncodeImage($imagePath2);
           $clients= $clientRepository->findAll();
           $halls = $hallRepository->findAll();
           // Récupérer les paramètres de recherche depuis la requête
           $evenementid = $request->query->get('evenement');
           $evenement = $evenementRepository->findOneBy(['id'=>$evenementid]);
           $clientId = $request->query->get('client');
           $client = $clientRepository->findOneBy(['id'=>$clientId]);
           $centredetrid = $request->query->get('centredetri');
           $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
           $matiere = $request->query->get('flux');
           $startDate = $request->query->get('start_date');
           $endDate = $request->query->get('end_date');

           $resultat = $suiviRepository->getSuivisGroupedByDateAndMatiere($client, $centredetri, $startDate, $endDate,$evenement,$matiere);

           return $this->render('accueil/synthesedetaille.html.twig', [
               'suivisGroupes' => $resultat,
               'client' => $clientId,
               'start_Date' => $startDate,
               'end_Date' => $endDate,
               'clientsuivi'=>$client,
               'centredetrisuivi'=>$centredetri,
               'evenementsuivi'=>$evenement,
               'base64Image' => $base64Image,
               'base64Image2' => $base64Image2,
           ]);
       }

      */
       #[Route('/synthesedetaille', name: 'app_synthese_detaille')]
       public function synthesedetaille(Request $request,CentredetriRepository $centredetriRepository,EvenementRepository $evenementRepository,SuiviRepository $suiviRepository,ClientRepository $clientRepository,HallRepository $hallRepository): Response
       {
           $clients= $clientRepository->findAll();
           $halls = $hallRepository->findAll();
           // Récupérer les paramètres de recherche depuis la requête
           $evenementid = $request->query->get('evenement');
           $evenement = $evenementRepository->findOneBy(['id'=>$evenementid]);
           $clientId = $request->query->get('client');
           $client = $clientRepository->findOneBy(['id'=>$clientId]);
           $centredetrid = $request->query->get('centredetri');
           $centredetri =$centredetriRepository->findOneBy(['id'=>$centredetrid]);
           $matiere = $request->query->get('flux');
           $startDate = $request->query->get('start_date');
           $endDate = $request->query->get('end_date');
           $resultat  = $suiviRepository->getSuivisGroupedByDateAndMatiere($client, $centredetri, $startDate, $endDate,$evenement,$matiere);
           $date = (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d');

           return $this->render('accueil/synthesedetaille2.html.twig', [
               'clients'=>$clients,
               'suivisGroupes'=>$resultat['groupedSuivis'],
               'cumulpoids'=>$resultat['poidsTotal'],
               'client'=>$client,
               'hall'=>$centredetri,
               'start_Date'=>$startDate,
               'end_Date'=>$endDate,
               'clientsuivi'=>$client,
               'centredetrisuivi'=>$centredetri,
               'evenementsuivi'=>$evenement,
               'evenement'=>$evenement,
               'matiere'=>$matiere,
               'date'=>$date
           ]);
       }

    #[\Symfony\Component\Routing\Attribute\Route('/formulairesite', name: 'app_site_form_site', methods: ['GET', 'POST'])]
    public function nouvform(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('site/new.html.twig', [

        ]);
    }

    #[Route('/creersuivi', name: 'app_suivi')]
    public function creersuivi(ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();

        return $this->render('suivi/index2.html.twig', [
            'clients' => $clients

        ]);
    }

    #[Route('/creercommentaire', name: 'app_creer_commentaire')]
    public function creercommentaire(EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->findAll();

        return $this->render('evenement/commentairevenement.html.twig', [
            'evenements' => $evenement

        ]);
    }


    #[Route('/admin/suivi', name: 'admin_suivi')]
    public function index4(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend

        return $this->render('accueil/test.html.twig');


        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    #[Route('/formulairesubmit', name: 'app_formulaire',methods: ['GET','POST'])]
    public function formulaire(Request $request,EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données du formulaire
        $nomCompagnie = (string) $request->request->get('company_name');
        $hall = (string) $request->request->get('hall');
        $alleeNumeroStand = (string) $request->request->get('aisle_booth_number');
        $contactStandiste = (string) $request->request->get('contact_builder');
        $rse = (string) $request->request->get('csr_form');
        $donnerDesMateriaux = (string) $request->request->get('donate_materials');
        $donnerDuBois = (string) $request->request->get('donate_wood');
        // Récupérer la quantité de bois et les transformer en chaîne de caractères
        $quantiteBois = '';
        if ($donnerDuBois === 'Yes') {
            $woodTypes = $request->request->all('wood_type');
            $woodQuantities = $request->request->all('quantity');
            if (!empty($woodTypes) && !empty($woodQuantities)) {
                foreach ($woodTypes as $key => $woodType) {
                    $quantity = isset($woodQuantities[$key]) ? (string) $woodQuantities[$key] : '';
                    $quantiteBois .= $woodType . '(' . $quantity . '), ';
                }
                $quantiteBois = rtrim($quantiteBois, ', ');
            } else {
                $quantiteBois = 'Pas de don de bois';
            }
        } else {
            $quantiteBois = 'Pas de don de bois';
        }
        // Récupérer les autres données du formulaire
        $quantiteFourniture = (string) $request->request->get('furniture_quantity');
        $quantiteAutresMateriaux = (string) $request->request->get('other_materials_quantity');
        $commentaire = (string) $request->request->get('comments');

        // Créer une nouvelle instance de l'entité Formulaire et lui attribuer les valeurs
        $formulaire = new Formulaire();
        $formulaire->setNomcompagnie($nomCompagnie)
            ->setHall($hall)
            ->setAlleenumerostand($alleeNumeroStand)
            ->setContactstandiste($contactStandiste)
            ->setRse($rse)
            ->setDonnerdesmateriaux($donnerDesMateriaux)
            ->setDonnerbois($quantiteBois)
            ->setQuantitefourniture($quantiteFourniture)
            ->setQuantiteautresmateriaux($quantiteAutresMateriaux)
            ->setCommentaire($commentaire);

        // Enregistrer l'entité dans la base de données
        $entityManager->persist($formulaire);
        $entityManager->flush();

        // Rediriger ou afficher une réponse appropriée
        return $this->redirectToRoute('app_accueil');
    }

    #[Route('/synthese-evenement/{id}', name: 'app_evenement_synthese_custom', methods: ['GET','POST'])]
    public function synthesevenement(SuiviRepository $suiviRepository, Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $client=null;
        $hall =null;
        $startDate =null;
        $endDate = null;

        // Effectuer la recherche en fonction des paramètres
        $cumulPoids = $suiviRepository->searchCumulPoids($client, $hall, $startDate, $endDate,$evenement,null);

        // Renvoyer les résultats au Twig
        return $this->render('accueil/synthesegeneralevenement.html.twig', [
            'cumul_poids' => $cumulPoids['formatedResult'],

        ]);
    }
    #[Route('/synthese-centre/{id}', name: 'app_centre_synthese_custom', methods: ['GET','POST'])]
    public function synthesecentre(SuiviRepository $suiviRepository, Request $request, Centredetri $centredetri, EntityManagerInterface $entityManager): Response
    {

        // Renvoyer les résultats au Twig
        return $this->render('accueil/synthesecentre.html.twig', [
            'centredetri' => $centredetri,
        ]);
    }
    #[Route('/consulter-suivi-centre/{id}', name: 'app_consulter_suivicentre_custom', methods: ['GET','POST'])]
    public function consultersuivicentre(SuiviRepository $suiviRepository, Request $request, Suivicentredetri $suivicentredetri, EntityManagerInterface $entityManager): Response
    {

        return $this->render('accueil/consultersuivicentre.html.twig', [
            'suivicentredetri' => $suivicentredetri,
        ]);
    }
    #[Route('/consulterphotoavecsuivi', name: 'app_consulter_suiviavecphoto_custom', methods: ['GET','POST'])]
    public function consulterphotoavecsuivi(SuiviRepository $suiviRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
             $suiviavecphoto = $suiviRepository->searchsuiviavecphoto(null);
        return $this->render('accueil/consultersuiviavecphoto.html.twig', [
            'suivicentredetri' => $suiviavecphoto,
        ]);
    }

    #[Route('/dispatch-bac', name: 'app_dispatch_bacs_custom', methods: ['GET','POST'])]
    public function dispatchbacs(SuivicentredetriRepository $suivicentredetri,CentredetriRepository $centredetriRepository,SuiviRepository $suiviRepository, Request $request,  EntityManagerInterface $entityManager): Response
    {
       $centredetris = $centredetriRepository->findAll();
       $suivis= [];
       foreach ($centredetris as $centredetri){
           $suivi = $centredetri->getSuivicentredetris()->last();
           if($suivi != null){

               $suivis[] = $suivi;
           }
       }

        return $this->render('accueil/dispatchbac.html.twig', [
            'centredetris' => $suivis,
        ]);
    }

    #[Route('/ajout-suivi-centre-de-tri', name: 'app_suivi_centredetri', methods: ['GET','POST'])]
    public function ajoutsuivicentredetri( Request $request,CentredetriRepository $centredetriRepository, EntityManagerInterface $entityManager): Response
    {


        return $this->render('accueil/suivicentredetri.html.twig', [
            'centredetri' => $centredetriRepository->findAll(),
        ]);
    }
    #[Route('/admin/centredetri', name: 'app_crud_centredetri', methods: ['GET','POST'])]
    public function centredetricrud( Request $request,CentredetriRepository $centredetriRepository, EntityManagerInterface $entityManager): Response
    {

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(CentredetriCrudController::class)->generateUrl());
    }

    #[Route('/supprimer-centredetri/{id}', name: 'app_suivicentre_delete_custom', methods: ['GET','POST'])]
    public function deletecentredetri(Request $request, Centredetri $centredetri, EntityManagerInterface $entityManager): Response
    {
        $lesuivis= $centredetri->getSuivicentredetris();
        $leshalls =$centredetri->getHalls();




        if(!$lesuivis->isEmpty()){

            foreach ($lesuivis as $suivi){
                $centredetri->removeSuivicentredetri($suivi);
                $entityManager->remove($suivi);

            }

        }
        if(!$leshalls->isEmpty()){

            foreach ($leshalls as $hall){
                $centredetri->removeHall($hall);
            }

        }
        $entityManager->remove($centredetri);
        $entityManager->flush();


        return $this->redirectToRoute('app_crud_centredetri', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/supprimer-evenement/{id}', name: 'app_evenement_delete_custom', methods: ['GET','POST'])]
    public function deleteevenement(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $client= $evenement->getLeclient();
        $lesuivis= $evenement->getSuivis();
        $leshalls =$evenement->getLeshalls();
        $lessites =  $evenement->getSites();
        $commentaires = $evenement->getCommentaires();


        $client->removeEvenement($evenement);
        if(!$lessites->isEmpty()){

            foreach ($lessites as $site){
                $evenement->removeSite($site);
            }

        }
        if(!$commentaires->isEmpty()){

            foreach ($commentaires as $commentaire){
                $evenement->removeCommentaire($commentaire);
            }

        }

        if(!$lesuivis->isEmpty()){

            foreach ($lesuivis as $suivi){
                $evenement->removeSuivi($suivi);
            }

        }
        if(!$leshalls->isEmpty()){

            foreach ($leshalls as $hall){
                $evenement->removeLeshall($hall);
            }

        }
        $entityManager->remove($evenement);
        $entityManager->flush();


        return $this->redirectToRoute('admin_evenement', [], Response::HTTP_SEE_OTHER);
    }



    public function configureDashboard(): Dashboard
    {
        $imageUrl = '/images/logoreuptrans.png';
        $imageTag = '<img src="'.$imageUrl.'" style="height: 70px;">';

        return Dashboard::new()
            ->setTitle($imageTag);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/profile-agent', name: 'app_gerer_profill_agent', methods: ['GET'])]
    public function profilegant(Request $request,UserRepository $userRepository,PaginatorInterface $paginator): Response
    {
        $userlocal = $this->getUser()->getUserIdentifier();
        $user= $userRepository->findOneBy(['login'=>$userlocal]);
        $nomautres = $user->getAutres();
        if($nomautres != null){
            $fileNames = $this->getFileNamesArray($nomautres);

        }else{
            $fileNames = null;
        }

        return $this->render('agent/profile.html.twig',[
            'autres' => $fileNames
        ]);
    }
    function arrayToFileNames(array $fileNamesArray): string
    {
        // Utiliser implode pour joindre les éléments du tableau en une chaîne de caractères, séparés par des espaces
        $fileNames = implode(' ', $fileNamesArray);

        return $fileNames;
    }
    function getFileNamesArray(string $fileNames): array
    {
        // Utiliser explode pour séparer la chaîne en un tableau de noms de fichiers
        $fileNamesArray = explode(' ', trim($fileNames));

        // Enlever les éléments vides éventuels (au cas où il y a des espaces en trop)
        $fileNamesArray = array_filter($fileNamesArray, fn($value) => !is_null($value) && $value !== '');

        return $fileNamesArray;
    }
    #[Route('/supprimer-document-agent', name: 'app_supprimer_document')]
    public function suppressiondonneesagent(Request $request,UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {

        $userlogin = $request->request->get('id');
        $user = $userRepository->findOneBy(['id'=>$userlogin]);
        $document = $request->request->get('document');
        $nomsautres = $user->getAutres();
        if($nomsautres != null){
            $tableaudesnoms = $this->getFileNamesArray($nomsautres);
        }
        else{
            $tableaudesnoms = [];
        }




        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

        if ( $document == 'rib'){

            $ancienRib = $user->getRib();
            if ($ancienRib) {
                $ancienRibPath = $uploadDirectory . '/' . $ancienRib;
                if (file_exists($ancienRibPath)) {
                    unlink($ancienRibPath);
                }
            }

            $user->setRib(null);
        }


        if ($document == 'photo') {

            $anciennephoto = $user->getPhoto();
            if ($anciennephoto) {
                $ancienRibPath = $uploadDirectory . '/' . $anciennephoto;
                if (file_exists($ancienRibPath)) {
                    unlink($ancienRibPath);
                }
            }

            $user->setPhoto(null);
        }
        if ($document == 'permis') {
            $anciennepermis = $user->getPhoto();
            if ($anciennepermis) {
                $ancienRibPath = $uploadDirectory . '/' . $anciennepermis;
                if (file_exists($ancienRibPath)) {
                    unlink($ancienRibPath);
                }
            }

            $user->setPermis(null);
        }else{
            foreach ($tableaudesnoms as $cle => $tableaudesnom ){
                if($tableaudesnom == $document){
                    unset($tableaudesnoms[$cle]);
                    $ancienRibPath = $uploadDirectory . '/' . $document;
                    if (file_exists($ancienRibPath)) {
                        unlink($ancienRibPath);
                    }
                    $user->setAutres($this->arrayToFileNames($tableaudesnoms));
                    break;
                }
            }

        }


        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_gerer_profill_agent');
    }
    #[Route('/ajouter-inventaire', name: 'app_ajouter_inventaire')]
    public function submitForm(Request $request,EntityManagerInterface $em): Response
    {
        $longueur = $request->request->get('longueur');
        $largeur = $request->request->get('largeur');
        $hauteur = $request->request->get('hauteur');
        $diametre = $request->request->get('diametre');
        $uniteDiametre = $request->request->get('unite-diametre');
        $materiaux = $request->request->get('materiaux');
        $color1 = $request->request->get('color1');
        $marque = $request->request->get('marque');
        $modele = $request->request->get('modele');
        $etat = $request->request->get('etat');
        $description = $request->request->get('description');

        // Données du diagnostic
        $reemploi = $request->request->get('reemploi');
        $reutilisation = $request->request->get('reutilisation');
        $recyclage = $request->request->get('recyclage');
        $precisions = $request->request->get('precisions');
        $modeAssemblage = $request->request->get('modeAssemblage');
        $methodologieDepose = $request->request->get('methodologieDepose');
        $modaliteTransport = $request->request->get('modaliteTransport');
        $conditionnement = $request->request->get('conditionnement');
        $risquesPrecautions = $request->request->get('risquesPrecautions');

        // Données des actions
        $etape = $request->request->get('etape');
        $quantite = $request->request->get('quantite');
        $uniteQuantite = $request->request->get('unite-quantite');
        $actions = $request->request->get('actions');
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');
        $localisation = $request->request->get('localisation');

        // Traitement des données (par exemple, enregistrement dans la base de données)
        $product = new Inventaire();
        $product->setLongueur($longueur);
        $product->setLargeur($largeur);
        $product->setHauteur($hauteur);
        $product->setDiametre($diametre);
        $product->setUniteDiametre($uniteDiametre);
        $product->setMateriaux($materiaux);
        $product->setColor1($color1);
        $product->setMarque($marque);
        $product->setModele($modele);
        $product->setEtat($etat);
        $product->setDescription($description);

        $product->setReemploi($reemploi);
        $product->setReutilisation($reutilisation);
        $product->setRecyclage($recyclage);
        $product->setPrecisions($precisions);
        $product->setModeAssemblage($modeAssemblage);
        $product->setMethodologieDepose($methodologieDepose);
        $product->setModaliteTransport($modaliteTransport);
        $product->setConditionnement($conditionnement);
        $product->setRisquesPrecautions($risquesPrecautions);

        $product->setEtape($etape);
        $product->setQuantite($quantite);
        $product->setUniteQuantite($uniteQuantite);
        $product->setActions($actions);
        $product->setDateDebut(new \DateTime($dateDebut));
        $product->setDateFin(new \DateTime($dateFin));
        $product->setLocalisation($localisation);

        // Sauvegarde du produit dans la base de données
        $em->persist($product);
        $em->flush();

        // Retourner une réponse ou rediriger vers une autre page
        return $this->redirectToRoute('admin_inventaire');

    }

    #[Route('/modification-donneesagent', name: 'app_modification_donnees_agent')]
    public function modificationdonneesagent(Request $request,UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {

        $userlogin = $request->request->get('id');
        $user = $userRepository->findOneBy(['id' => $userlogin]);

        $rib = $request->files->get('rib');


        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

        if ($rib != null) {

            $ancienRib = $user->getRib();
            if ($ancienRib) {
                $ancienRibPath = $uploadDirectory . '/' . $ancienRib;
                if (file_exists($ancienRibPath)) {
                    unlink($ancienRibPath);
                }
            }
            $nomrib = uniqid() . '.' . $rib->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $rib->move(
                $uploadDirectory,
                $nomrib
            );
            $user->setRib($nomrib);
        }

        $photo = $request->files->get('photo');

        if ($photo != null) {

            $anciennephoto = $user->getPhoto();
            if ($anciennephoto) {
                $ancienRibPath = $uploadDirectory . '/' . $anciennephoto;
                if (file_exists($ancienRibPath)) {
                    unlink($ancienRibPath);
                }
            }
            $nomphoto = uniqid() . '.' . $photo->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $photo->move(
                $uploadDirectory,
                $nomphoto
            );
            $user->setPhoto($nomphoto);
        }
        $permis = $request->files->get('permis');
        if ($permis != null) {
            $anciennepermis = $user->getPhoto();
            if ($anciennepermis) {
                $ancienRibPath = $uploadDirectory . '/' . $anciennepermis;
                if (file_exists($ancienRibPath)) {
                    unlink($ancienRibPath);
                }
            }
            $nompermis = uniqid() . '.' . $permis->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $permis->move(
                $uploadDirectory,
                $nompermis
            );
            $user->setPermis($nompermis);
        }

        $autredoc = $request->files->get('autredoc');
        $ancienautre =  $request->request->get('nomautre');

        if ($autredoc != null) {

            $nomsautres = $user->getAutres();
            $tableaudesnoms = $this->getFileNamesArray($nomsautres);
            $nouveaunomautre = uniqid() . '.' . $autredoc->guessExtension();
            // Déplacer le fichier téléchargé vers le dossier de destination
            $autredoc->move(
                $uploadDirectory,
                $nouveaunomautre
            );

            foreach ($tableaudesnoms as $cle => $tableaudesnom) {

                if ($tableaudesnom == $ancienautre) {
                    $tableaudesnoms[$cle]= $nouveaunomautre;
                    $ancienRibPath = $uploadDirectory . '/' . $ancienautre;
                    if (file_exists($ancienRibPath)) {
                        unlink($ancienRibPath);
                    }
                    $user->setAutres($this->arrayToFileNames($tableaudesnoms));
                    break;
                }
            }

        }




        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_gerer_profill_agent');

    }

    public function configureMenuItems(): iterable
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        foreach($roles as $role){
            if($role==='ROLE_ADMIN'){

                yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
                yield MenuItem::linkToCrud('Client', 'fa fa-user',Client::class);
                yield MenuItem::linkToCrud('Evenement', 'fa fa-calendar',Evenement::class);
                yield MenuItem::linkToCrud('Donation', 'fa fa-gift',Donation::class);

                yield MenuItem::linkToRoute('Inventaire', 'fa fa-shopping-basket','app_show_inventaire');

                yield MenuItem::linkToRoute('Reporting photos', 'fa fa-picture-o','app_recherche_photosuivi');

                yield MenuItem::linkToRoute('Suivi-photo', 'fa fa-address-card','app_suiviphotocentredetri');

                yield MenuItem::linkToCrud('Suivi', 'fa fa-pencil-square-o',Suivi::class);
                yield MenuItem::linkToCrud('Suivi', 'fa fa-pencil-square-o',User::class);
                yield MenuItem::linkToRoute('Agent', 'fa fa-address-card','pageagentspourdoc');
                yield MenuItem::linkToCrud('Incident', 'fa fa-exclamation-triangle',Commentaire::class);
                yield MenuItem::linkToCrud('Site', 'fa fa-map-marker',Site::class);
                yield MenuItem::linkToCrud('Photo', 'fa fa-picture-o',Photosarchives::class);
                yield MenuItem::linkToCrud('Centres de tri', 'fa fa-recycle',Centredetri::class);
                yield MenuItem::linkToCrud('Suivi centre de tri','fa fa-pencil-square-o',Suivicentredetri::class);
                yield MenuItem::linkToCrud('Materiels', 'fa fa-archive',Materiels::class);
                yield MenuItem::linkToCrud('Bacs', 'fa fa-shopping-basket',Bacs::class);
            }
        }

        yield MenuItem::linkToRoute('Retour accueil','fa fa-arrow-left','app_accueil');




        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
