<?php

namespace App\Controller;

use App\Entity\Bacs;
use App\Entity\Client;
use App\Entity\Commentaire;
use App\Entity\Evenement;
use App\Entity\Formulaire;
use App\Entity\Formulairedechethuile;
use App\Entity\Hall;
use App\Entity\Materiels;
use App\Entity\Photosarchives;
use App\Entity\Site;
use App\Entity\Suivi;
use App\Entity\Suivicentredetri;
use App\Entity\User;
use App\Repository\CentredetriRepository;
use App\Repository\PlanningRepository;
use App\Repository\UserRepository;
use Cassandra\Time;
use DateTimeZone;

use App\Form\SiteType;
use App\Repository\ClientRepository;
use App\Repository\CommentaireRepository;
use App\Repository\EvenementRepository;
use App\Repository\FormulairedechethuileRepository;
use App\Repository\HallRepository;
use App\Repository\SiteRepository;
use App\Repository\SuiviRepository;
use App\Service\PdfService;
use Container6xLJ7iO\getPdfServiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use function PHPUnit\Framework\equalTo;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AccueilController extends AbstractController
{
    #[Route('/update-planning-year/{year}', name: 'update_planning_year')]
    public function updatePlanningYear(
        int $year,
        PlanningRepository $planningRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer tous les enregistrements de planning
        $plannings = $planningRepository->findAll();

        // Parcourir chaque planning et mettre à jour les années des dates
        foreach ($plannings as $planning) {
            $debut = $planning->getDebut();
            $fin = $planning->getFin();

            if ($debut && $fin) {
                // Modifier l'année des dates
                $newDebut = (clone $debut)->setDate($year, $debut->format('m'), $debut->format('d'));
                $newFin = (clone $fin)->setDate($year, $fin->format('m'), $fin->format('d'));

                // Mettre à jour les dates dans l'entité
                $planning->setDebut($newDebut);
                $planning->setFin($newFin);

                // Persister l'entité mise à jour
                $entityManager->persist($planning);
            }
        }

        // Exécuter les modifications dans la base de données
        $entityManager->flush();

        return new Response('Les années des dates de début et de fin ont été mises à jour.');
    }

    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {

        return $this->render('accueil/accueil.html.twig', [
        ]);
    }
    #[Route('/dash', name: 'app_dash')]
    public function dash(): Response
    {

        return $this->render('accueil/testdashbord.html.twig', [
        ]);
    } 
    #[Route('/show-inventaire', name: 'app_show_inventaire')]
    public function inventaire(SiteRepository $siteRepository): Response
    {
        $sites = $siteRepository->findAll();
        return $this->render('accueil/showinventaire.html.twig', ['sites'=>$sites
        ]);
    }
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/creamoncompte', name: 'temp')]
    public function index3(EntityManagerInterface $entityManager): void
    {
        $user = new User();
        $user->setLogin('LOSKI230')

            ->setPassword($this->passwordHasher->hashPassword(
                $user,'Cashflow23$'
            ))
            ->setNom('LO')
            ->setPrenom('Mouhamadou')
            ->setRoles(['ROLE_ADMIN']);




        $entityManager->persist($user);
        $entityManager->flush();
    }
    #[Route('/graphe', name: 'app_graphecentre')]
    public function testgraphe(ChartBuilderInterface $chartBuilder,SuiviRepository $suiviRepository): Response
    {
        // Récupérer les données depuis le repository
        $data = $suiviRepository->getNbContenantByCentreDetri();

        // Initialisation des tableaux pour les labels et les datasets
        $labels = [];
        $datasets = [];
        foreach ($data as $item) {
            $centreNom = $item['centre_nom'];
            if (!in_array($centreNom, $labels)) {
                $labels[] = $centreNom;
            }
        }

        // Parcourir les données récupérées
        foreach ($data as $item) {
            $centreNom = $item['centre_nom'];
            $flux = $item['flux'];
            $nbDeContenants = $item['nb_de_contenants'];

            // Si le centre de tri n'est pas encore dans les labels, l'ajouter


            // Vérifier si le dataset pour ce flux existe déjà, sinon le créer
            if (!isset($datasets[$flux])) {
                // Initialiser les données du dataset avec des zéros pour tous les centres de tri connus
                $datasets[$flux] = [
                    'label' => $flux,
                    'backgroundColor' => $this->getColorForFlux($flux),
                    'data' => array_fill(0, count($labels), 0)
                ];
            }

            // Trouver l'index du centre de tri dans le tableau des labels
            $index = array_search($centreNom, $labels);

            // Ajouter le nombre de contenants à l'index correspondant dans le dataset du flux
            $datasets[$flux]['data'][$index] += $nbDeContenants;
        }

        // Convertir les datasets en un tableau indexé pour Chart.js
        $datasets = array_values($datasets);

        // Créer le graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
        $chart->setOptions([
            'scales' => [
                'x' => ['stacked' => true],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'min' => 0,
                    'suggestedMax' => 50,
                ],
            ],
        ]);

        // Rendre la vue Twig avec les données du graphique
        return $this->render('accueil/testgraphe.html.twig', [
            'chart' => $chart,
            'chartData' => [
                'labels' => $labels,
                'datasets' => $datasets
            ]
        ]);

    }
    #[Route('/graphehall', name: 'app_graphecentrehall')]
    public function testgraphehall(ChartBuilderInterface $chartBuilder,SuiviRepository $suiviRepository): Response
    {
        // Récupérer les données depuis le repository
        $data = $suiviRepository->getNbContenantByCentreDetri();

        // Initialisation des tableaux pour les labels et les datasets
        $labels = [];
        $datasets = [];
        foreach ($data as $item) {
            $centreNom = $item['centre_nom'];
            if (!in_array($centreNom, $labels)) {
                $labels[] = $centreNom;
            }
        }

        // Parcourir les données récupérées
        foreach ($data as $item) {
            $centreNom = $item['centre_nom'];
            $flux = $item['flux'];
            $nbDeContenants = $item['nb_de_contenants'];

            // Si le centre de tri n'est pas encore dans les labels, l'ajouter


            // Vérifier si le dataset pour ce flux existe déjà, sinon le créer
            if (!isset($datasets[$flux])) {
                // Initialiser les données du dataset avec des zéros pour tous les centres de tri connus
                $datasets[$flux] = [
                    'label' => $flux,
                    'backgroundColor' => $this->getColorForFlux($flux),
                    'data' => array_fill(0, count($labels), 0)
                ];
            }

            // Trouver l'index du centre de tri dans le tableau des labels
            $index = array_search($centreNom, $labels);

            // Ajouter le nombre de contenants à l'index correspondant dans le dataset du flux
            $datasets[$flux]['data'][$index] += $nbDeContenants;
        }

        // Convertir les datasets en un tableau indexé pour Chart.js
        $datasets = array_values($datasets);

        // Créer le graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
        $chart->setOptions([
            'scales' => [
                'x' => ['stacked' => true],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'min' => 0,
                    'suggestedMax' => 50,
                ],
            ],
        ]);

        // Rendre la vue Twig avec les données du graphique
        return $this->render('accueil/testgraphe.html.twig', [
            'chart' => $chart,
            'chartData' => [
                'labels' => $labels,
                'datasets' => $datasets
            ]
        ]);

    }
    private function getColorForFlux($flux)
    {
        // Tableau associatif avec les flux et leurs couleurs correspondantes
        $colors = [
            'DR' => 'rgba(255, 99, 132, 0.2)',           // Rouge
            'Recyclable' => 'rgba(54, 162, 235, 0.2)',   // Bleu
            'Bois' => '#664d03',         // Jaune
            'Carton' => 'rgba(75, 192, 192, 0.2)',       // Turquoise
            'Plastique souple' => 'rgba(153, 102, 255, 0.2)',  // Violet
            'PET' => 'rgba(255, 159, 64, 0.2)',          // Orange
            'Verre' => 'rgba(255, 99, 132, 0.2)',        // Rouge (répété)
            'Moquette' => 'rgba(54, 162, 235, 0.2)',     // Bleu (répété)
            'Biodechet' => 'rgba(255, 206, 86, 0.2)',    // Jaune (répété)
            'D3EDEEE' => 'rgba(75, 192, 192, 0.2)',      // Turquoise (répété)
            'Déchets dangereux' => 'rgba(153, 102, 255, 0.2)',  // Violet (répété)
            'Catalogues et journaux' => 'rgba(255, 159, 64, 0.2)',  // Orange (répété)
            'Déchets médicaux' => 'pink',     // Orchidée
            'Huiles usagées' => 'rgba(255, 165, 0, 0.2)',  // Orange rougeâtre
            'Bâche' => 'rgba(173, 255, 47, 0.2)',        // Vert chartreuse
            'Mobilier' => 'rgba(128, 0, 128, 0.2)',      // Violet foncé
            'Emballages en mélange' => '#ffc107',
            'Emballages Vides Souillés' => '#fff3cd'
        ];

        // Retourne la couleur correspondante au flux, ou une couleur par défaut si non trouvée
        return $colors[$flux] ?? 'rgba(0, 0, 0, 0.2)'; // Couleur par défaut (noir transparent)
    }




    #[Route('/accueilredirection', name: 'app_rediriger_admin')]
    public function redirectadmin(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        foreach($roles as $role){
            if($role==='ROLE_ADMIN'){
                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('accueil/accueil.html.twig', [
        ]);
    }




    #[Route('/submit-suivi-form', name: 'submit_suivi_form')]
    public function submitSuiviForm(UserRepository $userRepository ,Request $request,CentredetriRepository $centredetriRepository,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager,HallRepository $hallRepository,ClientRepository $clientRepository): Response
    {
        $suiviData = $request->request->all('suivi');
        $login = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['login'=>$login]);
        $idevenement = $request->request->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$idevenement]);
        $idcentredetri = $request->request->get('centredetri');
        $idclient = $request->request->get('client');
        $client = $clientRepository->findOneBy(['id'=>$idclient]);
        $centredetri = $centredetriRepository->findOneBy(['id'=>$idcentredetri]);
        $textecommentaire=$request->request->get('commentaire');
        $photos = $request->files->all('photo');
        $dateTime = new \DateTime('now', new DateTimeZone('Europe/Paris'));
        $dateTimepourheure = new \DateTime('now', new DateTimeZone('Europe/Paris'));
        $dateTime->setTime(0,0,0);


        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $suivi = new Suivi();


        // Parcourir chaque fichier téléchargé
        foreach ($photos as $uploadedFile) {
            // Vérifier si le fichier a bien été téléchargé

            // Générer un nom unique pour le fichier
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            $photo = new Photosarchives();
            $photo->setNom($newFilename);
            $photo->setDateupload($dateTime);
            $suivi->addPhotosarchive($photo);
            // Déplacer le fichier téléchargé vers le dossier de destination
            $uploadedFile->move(
                $uploadDirectory,
                $newFilename
            );

            $entityManager->persist($photo);
            // Vous pouvez maintenant enregistrer le nom du fichier dans la base de données
            // ou faire d'autres traitements nécessaires

        }

        foreach ($suiviData as $data) {

            $suivi->setFlux($data['flux']);
            if($data['typedecontenant']=='autre') {

                $suivi->setTypecontenant($request->request->get('autrenomcontenant'));

            }
            else{
                $suivi->setTypecontenant($data['typedecontenant']);

            }
            if($data['volumecontenant']=='autre') {
                $suivi->setVolumecontenant($request->request->get('autrechoixvolume'));

            }
            else{
                $suivi->setVolumecontenant($data['volumecontenant']);

            }
            if($data['poids']!=null){

                $suivi->setPoids($data['poids']);

            }else{

                $suivi->setPoids(0);
            }

            $suivi->setTauxderemplissage($data['tauxderemplissage']);
            $suivi->setCollecte($data['collecte']);
            $suivi->setCumulflux(0);
            $suivi->setQualitedetribennes($data['qualitedutribennes']);
            $suivi->setEstimatifbennes($data['estimatifbennes']);
            $suivi->setCollectebennes($data['collectebennes']);
            $suivi->setCumulbennes(0);
            $suivi->setDatedesoumission($dateTime);
            $suivi->setLeclient($client);
            $suivi->setAuteur($user);
            $suivi->setExutoire('on verra');
            $suivi->setHeure($dateTimepourheure);
            if($textecommentaire!=null){
                $suivi->setCommentaire($textecommentaire);
            }else{
                $suivi->setCommentaire("Aucun commentaire");
            }


            $centredetri->addSuivi($suivi);
            $evenement->addSuivi($suivi);

            $entityManager->persist($suivi);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Suivi  ajouté  avec succès !');

        $user = $this->getUser();
        $roles = $user->getRoles();
        foreach($roles as $role){
            if($role==='ROLE_ADMIN'){
                return $this->redirectToRoute('admin');
            }

        }
        return $this->redirectToRoute('app_suivi');

    }
    #[Route('/handlesuiviphotocentredetri', name: 'handlesuiviphotocentredetri', methods: ['GET', 'POST'])]
public function handleForm(EntityManagerInterface $entityManager,CentredetriRepository $centredetriRepository,Request $request, SluggerInterface $slugger): Response
{
    if ($request->isMethod('POST')) {
        $formData = $request->request->all();
        $files = $request->files->all();
        $idcentredetri = $request->request->get('centredetri');
        $centredetri = $centredetriRepository->findOneBy(['id'=>$idcentredetri]);
        $dateTime = new \DateTime('now', new DateTimeZone('Europe/Paris'));
        $dateTimepourheure = new \DateTime('now', new DateTimeZone('Europe/Paris'));
        $dateTime->setTime(0,0,0);


        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';


        foreach ($files as $key => $file) {
            if ($file) {
                $commentKey = 'commentaire' . $key;
                $titrekey = 'titre' . $key;
                $titre = $formData[$titrekey] ?? '';
                $commentaire = $formData[$commentKey] ?? '';
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                $photo = new Photosarchives();
                $photo->setNom($newFilename);
                $photo->setTitre($titre);
                $photo->setDateupload($dateTime);
                $photo->setCommentaire($commentaire);
                $centredetri->addPhotosarchive($photo);
                // Déplacer le fichier téléchargé vers le dossier de destination
                $file->move(
                    $uploadDirectory,
                    $newFilename
                );

                $entityManager->persist($photo);
            }


        }
        $entityManager->flush();



    }
    return $this->redirectToRoute('app_suiviphotocentredetri'); // Redirect to avoid form resubmission

}
    #[Route('/submit-photo-suivi-form', name: 'submit_photo_suivi_form')]
    public function submitphotoSuiviForm(Request $request,SuiviRepository $suiviRepository,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager,HallRepository $hallRepository,ClientRepository $clientRepository): Response
    {
        $idsuivi = $request->request->get('idsuivi');
        $suivi = $suiviRepository->findOneBy(['id'=>$idsuivi]);
        $photos = $request->files->all('photo');
        $dateTime = new \DateTime();

        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';


        // Parcourir chaque fichier téléchargé
        foreach ($photos as $uploadedFile) {
            // Vérifier si le fichier a bien été téléchargé

            // Générer un nom unique pour le fichier
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            $photo = new Photosarchives();
            $photo->setNom($newFilename);
            $photo->setDateupload($dateTime);
            $suivi->addPhotosarchive($photo);
            // Déplacer le fichier téléchargé vers le dossier de destination
            $uploadedFile->move(
                $uploadDirectory,
                $newFilename
            );

            $entityManager->persist($photo);

            // Vous pouvez maintenant enregistrer le nom du fichier dans la base de données
            // ou faire d'autres traitements nécessaires

        }


        $entityManager->flush();

        // Rediriger vers une autre page ou afficher un message de succès
        return $this->redirectToRoute('admin_suivi');
    }
    #[Route('/submit-photo-multiple-form', name: 'submit-photo-multiple-form')]
    public function submitphotomultipleForm(Request $request,SuiviRepository $suiviRepository,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager,HallRepository $hallRepository,ClientRepository $clientRepository): Response
    {
        $photos = $request->files->all('photo');
        $dateTime = new \DateTime();

        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';


        // Parcourir chaque fichier téléchargé
        foreach ($photos as $uploadedFile) {
            // Vérifier si le fichier a bien été téléchargé

            // Générer un nom unique pour le fichier
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            $photo = new Photosarchives();
            $photo->setNom($newFilename);
            $photo->setDateupload($dateTime);
            // Déplacer le fichier téléchargé vers le dossier de destination
            $uploadedFile->move(
                $uploadDirectory,
                $newFilename
            );

            $entityManager->persist($photo);
            // Vous pouvez maintenant enregistrer le nom du fichier dans la base de données
            // ou faire d'autres traitements nécessaires

        }


        $entityManager->flush();

        // Rediriger vers une autre page ou afficher un message de succès
        return $this->redirectToRoute('admin_photoarchives');
    }
    #[Route('/ajoutphotocommentaire', name: 'ajoutphoto_commentaire_form')]
    public function ajoutphotoCommentaireForm(Request $request,CommentaireRepository $commentaireRepository,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager,HallRepository $hallRepository,ClientRepository $clientRepository): Response
    {
        $idevenement = $request->request->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$idevenement]);
        $idcommentaire =  $request->request->get('idcommentaire');
        $commentaire = $commentaireRepository->findOneBy(['id'=>$idcommentaire]);

        $photos = $request->files->all('photo');
        $dateTime = new \DateTime();

        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

        // Parcourir chaque fichier téléchargé
        foreach ($photos as $uploadedFile) {
            // Vérifier si le fichier a bien été téléchargé

            // Générer un nom unique pour le fichier
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            $photo = new Photosarchives();
            $photo->setNom($newFilename);
            $photo->setDateupload($dateTime);
            $commentaire->addPhoto($photo);
            // Déplacer le fichier téléchargé vers le dossier de destination
            $uploadedFile->move(
                $uploadDirectory,
                $newFilename
            );

            $entityManager->persist($photo);
            // Vous pouvez maintenant enregistrer le nom du fichier dans la base de données
            // ou faire d'autres traitements nécessaires

        }
        $entityManager->flush();

        // Rediriger vers une autre page ou afficher un message de succès
        return $this->redirectToRoute('admin_commentaire');
    }
    #[Route('/creationcommentaire', name: 'submit_commentaire_form')]
    public function submitCommentaireForm(Request $request,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager,HallRepository $hallRepository,ClientRepository $clientRepository): Response
    {
        $idevenement = $request->request->get('evenement');
        $evenement = $evenementRepository->findOneBy(['id'=>$idevenement]);
        $textecommentaire=$request->request->get('commentaire');
        $photos = $request->files->all('photo');
        $dateTime = new \DateTime();
        $date = $request->request->get('date');
        $datesoumission = \DateTime::createFromFormat('Y-m-d', $date);

        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $commentaire = new Commentaire();


        // Parcourir chaque fichier téléchargé
        foreach ($photos as $uploadedFile) {
            // Vérifier si le fichier a bien été téléchargé

            // Générer un nom unique pour le fichier
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            $photo = new Photosarchives();
            $photo->setNom($newFilename);
            $photo->setDateupload($dateTime);
            $commentaire->addPhoto($photo);
            // Déplacer le fichier téléchargé vers le dossier de destination
            $uploadedFile->move(
                $uploadDirectory,
                $newFilename
            );

            $entityManager->persist($photo);
            // Vous pouvez maintenant enregistrer le nom du fichier dans la base de données
            // ou faire d'autres traitements nécessaires

        }
        $commentaire->setLibelle($textecommentaire);
        $commentaire->setEvenement($evenement);
        $commentaire->setDatesoumission($datesoumission);
        $entityManager->persist($commentaire);

        $entityManager->flush();

        // Rediriger vers une autre page ou afficher un message de succès
        return $this->redirectToRoute('admin_commentaire');
    }
    #[Route('/creersite', name: 'app_site_creer')]
    public function newsite(Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        $site = new Site();
        $nomsite = $request->request->get('nomsite');

        $site->setNom($nomsite);
        $halls = $request->request->all('halls');

        foreach ($halls as $data){
            $hall = new Hall();
            $hall->setNom($data['nomhall']);
            $site->addLeshall($hall);
            $entityManager->persist($hall);
        }

        $entityManager->persist($site);


        $entityManager->flush();

        return $this->redirectToRoute('admin_site');

    }

    #[Route('/submit-client-form', name: 'app_formulaire')]
    public function clientformulairesubmit(ClientRepository $clientRepository,HallRepository $hallRepository,Request $request,EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        $client = new Client();
        $nomclient = $request->request->get('nomclient');
        $client->setNom($nomclient);
        $idsite = $request->request->get('site');
        $site=$siteRepository->findOneBy(['id'=>$idsite]);
        $client->addSite($site);
        $datedebut = $request->request->get('datedebut');
        $datefin = $request->request->get('datefin');
        $datedebut2 = \DateTime::createFromFormat('Y-m-d', $datedebut);
        $datefin2 = \DateTime::createFromFormat('Y-m-d', $datefin);

        $halls = $request->request->all('halls');
        foreach ($halls as $hall){
            $entitehall = $hallRepository->findOneBy(['id'=>$hall]);
            $client->addLeshall($entitehall);
        }
        $client->setDatedebut($datedebut2);
        $client->setDatefin($datefin2);
        $entityManager->persist($client);
        $entityManager->flush();

        return $this->redirectToRoute('admin_client');

    }

    #[Route('/submitclient', name: 'app_creer_client')]
    public function submitformclient(Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        // Récupérer les données du formulaire
        $client = new Client();
        $nomClient = $request->request->get('nomclient');
        $datedebut = $request->request->get('datedebut');
        $datefin = $request->request->get('datefin');

        // Tableau pour stocker les sites et leurs halls
        $sites = [];

        // Parcourir les données du formulaire pour récupérer les sites et les halls
        foreach ($request->request->all() as $key => $value) {
            // Vérifier si la clé correspond à un site
            if (strpos($key, 'site_') === 0) {
                // Extraire l'index du site
                $siteIndex = substr($key, strlen('site_'));

                // Récupérer le nom du site
                $siteName = $value;
                $site = new Site();
                $site->setNom($siteName);
                // Récupérer le nombre de halls pour ce site
                $hallNumber = $request->request->get('hallNumber_' . $siteIndex);

                // Tableau pour stocker les halls
                $halls = [];

                // Parcourir les données pour récupérer les noms des halls
                for ($i = 1; $i <= $hallNumber; $i++) {
                    $hallKey = 'sitehall_' . $siteIndex . '_hall_' . $i;
                    if ($request->request->has($hallKey)) {
                        $hallName = $request->request->get($hallKey);
                        $hall = new Hall();
                        $hall->setNom($hallName);
                        $site->addLeshall($hall);
                        $client->addLeshall($hall);
                        $entityManager->persist($hall);
                    }
                }
                $client->addSite($site);
                $entityManager->persist($site);


            }

        }
        $datedebut2 = \DateTime::createFromFormat('Y-m-d', $datedebut);
        $datefin2 = \DateTime::createFromFormat('Y-m-d', $datefin);
        $client->setNom($nomClient);
        $client->setDatedebut($datedebut2);
        $client->setDatefin($datefin2);
        $entityManager->persist($client);
        $entityManager->flush();

        // Faire quelque chose avec les données récupérées
        // Par exemple, enregistrer dans la base de données

        // Redirection ou rendu de la réponse
        return $this->redirectToRoute('admin_client');
    }
    #[Route('/creationclient', name: 'app_creer_client_final')]
    public function submitformclientfinal(HallRepository $hallRepository,Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository): Response
    {
        $client = new Client();
        $evenement = new Evenement();
        $nomevenement = $request->request->get('nomevenement');
        $evenement->setNom($nomevenement);
        $nomClient = $request->request->get('nomclient');
        $datedebut = $request->request->get('datedebut');
        $datefin = $request->request->get('datefin');
        $idsite = $request->request->get('site');
        if($idsite!=null){
            $site=$siteRepository->findOneBy(['id'=>$idsite]);
            $halls = $request->request->all('halls');
            if($halls!=null){
                $evenement->addSite($site);
            }
            foreach ($halls as $hall){
                $entitehall = $hallRepository->findOneBy(['id'=>$hall]);
                $evenement->addLeshall($entitehall);
            }


        }
        // Parcourir les données du formulaire pour récupérer les sites et les halls
        foreach ($request->request->all() as $key => $value) {
            // Vérifier si la clé correspond à un site
            if (strpos($key, 'site_') === 0) {
                // Extraire l'index du site
                $siteIndex = substr($key, strlen('site_'));

                // Récupérer le nom du site
                $siteName = $value;
                $site = new Site();
                $site->setNom($siteName);
                // Récupérer le nombre de halls pour ce site
                $hallNumber = $request->request->get('hallNumber_' . $siteIndex);

                // Tableau pour stocker les halls
                $halls = [];

                // Parcourir les données pour récupérer les noms des halls
                for ($i = 1; $i <= $hallNumber; $i++) {
                    $hallKey = 'sitehall_' . $siteIndex . '_hall_' . $i;
                    if ($request->request->has($hallKey)) {
                        $hallName = $request->request->get($hallKey);
                        $hall = new Hall();
                        $hall->setNom($hallName);
                        $site->addLeshall($hall);
                        $evenement->addLeshall($hall);
                        $entityManager->persist($hall);
                    }
                }
                $evenement->addSite($site);
                $entityManager->persist($site);


            }

        }
        $datedebut2 = \DateTime::createFromFormat('Y-m-d', $datedebut);
        $datefin2 = \DateTime::createFromFormat('Y-m-d', $datefin);
        $client->setNom($nomClient);
        $evenement->setDatedebut($datedebut2);
        $evenement->setDatefin($datefin2);
        $client->addEvenement($evenement);
        $entityManager->persist($client);
        $entityManager->persist($evenement);
        $entityManager->flush();





        return $this->redirectToRoute('admin_client');
    }
    #[Route('/creationevenement', name: 'app_creer_evenement_final')]
    public function submitformevenement(HallRepository $hallRepository,Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository,ClientRepository $clientRepository): Response
    {
        $idclient =  $request->request->get('client');
        $client=$clientRepository->findOneBy(['id'=>$idclient]);
        $evenement = new Evenement();
        $nomevenement = $request->request->get('nomevenement');
        $evenement->setNom($nomevenement);
        $datedebut = $request->request->get('datedebut');
        $datefin = $request->request->get('datefin');
        $idsite = $request->request->get('site');
        if($idsite!=null){
            $site=$siteRepository->findOneBy(['id'=>$idsite]);
            $halls = $request->request->all('halls');
            if($halls!=null){
                $evenement->addSite($site);
            }
            foreach ($halls as $hall){
                $entitehall = $hallRepository->findOneBy(['id'=>$hall]);
                $evenement->addLeshall($entitehall);
            }


        }
        // Parcourir les données du formulaire pour récupérer les sites et les halls
        foreach ($request->request->all() as $key => $value) {
            // Vérifier si la clé correspond à un site
            if (strpos($key, 'site_') === 0) {
                // Extraire l'index du site
                $siteIndex = substr($key, strlen('site_'));

                // Récupérer le nom du site
                $siteName = $value;
                $site = new Site();
                $site->setNom($siteName);
                // Récupérer le nombre de halls pour ce site
                $hallNumber = $request->request->get('hallNumber_' . $siteIndex);

                // Tableau pour stocker les halls
                $halls = [];

                // Parcourir les données pour récupérer les noms des halls
                for ($i = 1; $i <= $hallNumber; $i++) {
                    $hallKey = 'sitehall_' . $siteIndex . '_hall_' . $i;
                    if ($request->request->has($hallKey)) {
                        $hallName = $request->request->get($hallKey);
                        $hall = new Hall();
                        $hall->setNom($hallName);
                        $site->addLeshall($hall);
                        $evenement->addLeshall($hall);
                        $entityManager->persist($hall);
                    }
                }
                $evenement->addSite($site);
                $entityManager->persist($site);


            }

        }
        $datedebut2 = \DateTime::createFromFormat('Y-m-d', $datedebut);
        $datefin2 = \DateTime::createFromFormat('Y-m-d', $datefin);

        $montageDebut = $request->request->get('montageDebut');
        $montageFin = $request->request->get('montageFin');
        $exploitationDebut = $request->request->get('exploitationDebut');
        $exploitationFin = $request->request->get('exploitationFin');
        $demontageDebut = $request->request->get('demontageDebut');
        $demontageFin = $request->request->get('demontageFin');

        $evenement->setMontageDebut(\DateTime::createFromFormat('Y-m-d', $montageDebut));
        $evenement->setMontageFin(\DateTime::createFromFormat('Y-m-d', $montageFin));
        $evenement->setExploitationDebut(\DateTime::createFromFormat('Y-m-d', $exploitationDebut));
        $evenement->setExploitationFin(\DateTime::createFromFormat('Y-m-d', $exploitationFin));
        $evenement->setDemontageDebut(\DateTime::createFromFormat('Y-m-d', $demontageDebut));
        $evenement->setDemontageFin(\DateTime::createFromFormat('Y-m-d', $demontageFin));
        $evenement->setDatedebut($datedebut2);
        $evenement->setDatefin($datefin2);
        $client->addEvenement($evenement);
        $entityManager->persist($evenement);
        $entityManager->flush();





        return $this->redirectToRoute('admin_client');
    }


    #[Route('/formulairedechethuilesubmit', name: 'formulaire_dechet_huile',methods: ['POST','GET'])]
    public function formulairedechethuilesubmit(Request $request,EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {

            $nomHall = $request->request->get('nom_hall');
            $allee = $request->request->get('allee');
            $nombreSacs = $request->request->get('nombre_sacs');
            $quantiteSacs = $request->request->get('quantite_sacs');
            $bidonHuile = $request->request->get('bidon_huile') === 'oui';
            $nombreBidons = $request->request->get('nombre_bidons');
            $commentaire = $request->request->get('commentaire');

            $formulaire = new Formulairedechethuile();
            $formulaire->setNomhall($nomHall);
            $formulaire->setAllee($allee);
            if($nombreSacs!=null){
                $formulaire->setNombredesacs($nombreSacs);
                $formulaire->setQuantitesacs($quantiteSacs);
            }else{
                $formulaire->setNombredesacs(0);
                $formulaire->setQuantitesacs('Vide');
            }
            if($nombreBidons!=null) {

                $formulaire->setNombredebidons($bidonHuile);
            }else{
                $formulaire->setNombredebidons(0);
            }
            if($commentaire!=null){
                $formulaire->setCommentaire($commentaire);
            }else{
                $formulaire->setCommentaire('Aucun commentaire');
            }

            $entityManager->persist($formulaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_formulairedechethuile_index'); // Vous pouvez remplacer 'formulaire' par le nom de la route vers laquelle vous souhaitez rediriger
        }


        return $this->render('accueil/formulairedechethuile.html.twig');
    }
    #[Route('/ajoutsuivicentredetri', name: 'suivicentredetri_new')]
    public function new(Request $request, EntityManagerInterface $entityManager,CentredetriRepository $centredetriRepository): Response
    {
        if ($request->isMethod('POST')) {
            $suivicentredetri = new Suivicentredetri();
            $dateTime = new \DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateTime->setTime(0,0,0);
            $photos = $request->files->all('photo');

            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

            foreach ($photos as $uploadedFile) {
                // Vérifier si le fichier a bien été téléchargé

                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
                $photo = new Photosarchives();
                $photo->setNom($newFilename);
                $photo->setDateupload($dateTime);
                $suivicentredetri->addPhoto($photo);
                // Déplacer le fichier téléchargé vers le dossier de destination
                $uploadedFile->move(
                    $uploadDirectory,
                    $newFilename
                );

                $entityManager->persist($photo);
                // Vous pouvez maintenant enregistrer le nom du fichier dans la base de données
                // ou faire d'autres traitements nécessaires

            }
            $entityManager->flush();
            $barrieragesmat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Barrières à louer']);
            $bachesnoiresmat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'bâches noires']);
            $tabledetrimat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Tables de tri']);
            $compacteurmat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Compacteurs avec lève-conteneur']);
            $balancesmat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'balances']);
            $Marquagemat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Marquage sol']);
            $supportsachsmat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Support Sachs']);
            $Signaletiquesmat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Signalétique P24']);
            $Tonnellemat = $entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Tonnelle']);
            $bacdr660mat = $entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac dr', 'volume' => '660']);
            $bacdr240mat = $entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac dr', 'volume' => '240']);
            $bacrecycle240mat = $entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac recycle', 'volume' => '240']);
            $bacrecycle660mat = $entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac recycle', 'volume' => '660']);
            $bacbiodechet120mat = $entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac biodechet', 'volume' => '120']);



            $idcentre = $request->request->get('centredetri');
            $centredetri= $centredetriRepository->findOneBy(['id'=>$idcentre]);

            $bachecentredetri = $centredetri->getBachenoires();
            $troussescentredetri = $centredetri->getTroussedesecours();
            $barrieragescentredetri = $centredetri->getBarrierages();
            $marquagecentredetri = $centredetri->getMarquageausol();
            $tonnellecentredetri= $centredetri->getTonnelle();
            $signaletiquescentredetri = $centredetri->getSignaletiques();
            $supportssachscentredetri = $centredetri->getSupportsachs();
            $balancescentredetri=  $centredetri->getBalances();
            $compacteurcentredetri = $centredetri->getCompacteur();
            $tabledetricentredetri =  $centredetri->getTabledetri();
            $Bacdr660centredetri = $centredetri->getBacdr660();
            $Bacdr240centredetri= $centredetri->getBacdr240();
            $Bacrecycle240centredetri=$centredetri->getBacrecycle240();
            $Bacrecycle660centredetri = $centredetri->getBacrecycle660();
            $bacbiodechetcentredetri = $centredetri->getBacbiodechet120();
            $benneboiscentredetri= $centredetri->getBennebois();
            $bennecartoncentredetri = $centredetri->getBennecarton();
            $benneplastiquescentredetri =$centredetri->getBenneplastiques();

            $bachenoires = $request->request->get('Bâches');
            $epi = $request->request->get('epi');
            $troussedesecours = $request->request->get('troussedesecours');
            $materielsdenettoyage = $request->request->get('materielsdenettoyage');
            $vidoirliquide = $request->request->get('vidoirliquide');
            $phase = $request->request->get('phase');
            $barrierages = $request->request->get('barrierages');
            $marquageausol = $request->request->get('marquageausol');
            $tonnelle = $request->request->get('tonnelle');
            $signaletiques = $request->request->get('signaletiques');
            $supportsachs = $request->request->get('supportsachs');
            $balances = $request->request->get('balances');
            $compacteur = $request->request->get('compacteur');
            $tabledetri = $request->request->get('tabledetri');
            $bacdr660 = $request->request->get('bacdr660');
            $bacdr240 = $request->request->get('bacdr240');
            $bacrecycle240 = $request->request->get('bacrecycle240');
            $bacrecycle660 = $request->request->get('bacrecycle660');
            $bacbiodechet120 = $request->request->get('bacbiodechet120');
            $bennebois = $request->request->get('bennebois');
            $bennecarton = $request->request->get('bennecarton');
            $benneplastiques = $request->request->get('benneplastiques');

            $bacbiodechet120mat->setAlloues($bacbiodechet120mat->getAlloues() + $bacbiodechet120);
            $bacbiodechet120mat->setDisponible($bacbiodechet120mat->getDisponible() - $bacbiodechet120);
            $bachesnoiresmat->setAlloues($bachesnoiresmat->getAlloues() + $bachenoires);
            $bachesnoiresmat->setDisponibles($bachesnoiresmat->getDisponibles() - $bachenoires);
            $bacrecycle660mat->setAlloues($bacrecycle660mat->getAlloues() + $bacrecycle660);
            $bacrecycle660mat->setDisponible($bacrecycle660mat->getDisponible() - $bacrecycle660);
            $bacrecycle240mat->setAlloues($bacrecycle240mat->getAlloues() + $bacrecycle240);
            $bacrecycle240mat->setDisponible($bacrecycle240mat->getDisponible() - $bacrecycle240);
            $bacdr240mat->setAlloues($bacdr240mat->getAlloues() + $bacdr240);
            $bacdr240mat->setDisponible($bacdr240mat->getDisponible() - $bacdr240);
            $bacdr660mat->setAlloues($bacdr660mat->getAlloues() + $bacdr660);
            $bacdr660mat->setDisponible($bacdr660mat->getDisponible() - $bacdr660);
            $barrieragesmat->setAlloues($barrieragesmat->getAlloues() + $barrierages);
            $barrieragesmat->setDisponibles($barrieragesmat->getDisponibles() - $barrierages);
            $tabledetrimat->setAlloues($tabledetrimat->getAlloues() + $tabledetri);
            $tabledetrimat->setDisponibles($tabledetrimat->getDisponibles() - $tabledetri);
            $compacteurmat->setAlloues($compacteurmat->getAlloues() + $compacteur);
            $compacteurmat->setDisponibles($compacteurmat->getDisponibles() - $compacteur);
            $balancesmat->setAlloues($balancesmat->getAlloues() + $balances);
            $balancesmat->setDisponibles($balancesmat->getDisponibles() - $balances);
            $Marquagemat->setAlloues($Marquagemat->getAlloues() + $marquageausol);
            $Marquagemat->setDisponibles($Marquagemat->getDisponibles() - $marquageausol);
            $supportsachsmat->setAlloues($supportsachsmat->getAlloues() + $supportsachs);
            $supportsachsmat->setDisponibles($supportsachsmat->getDisponibles() - $supportsachs);
            $Signaletiquesmat->setAlloues($Signaletiquesmat->getAlloues() + $signaletiques);
            $Signaletiquesmat->setDisponibles($Signaletiquesmat->getDisponibles() - $signaletiques);
            $Tonnellemat->setAlloues($Tonnellemat->getAlloues() + $tonnelle);
            $Tonnellemat->setDisponibles($Tonnellemat->getDisponibles() - $tonnelle);



            if ($bachecentredetri == $bachenoires  && $barrieragescentredetri == $barrierages
            && $marquagecentredetri == $marquageausol && $tonnellecentredetri == $tonnelle && $signaletiquescentredetri == $signaletiques && $supportssachscentredetri == $supportsachs && $balancescentredetri == $balances
            && $compacteurcentredetri == $compacteur && $tabledetricentredetri == $tabledetri && $Bacdr660centredetri == $bacdr660 && $Bacdr240centredetri == $bacdr240 && $Bacrecycle240centredetri == $bacrecycle240 && $Bacrecycle660centredetri == $bacrecycle660
            && $benneboiscentredetri == $bennebois && $bennecartoncentredetri == $bennecarton && $benneplastiquescentredetri == $benneplastiques && $materielsdenettoyage == 'Présent' && $epi =='Présent' && $vidoirliquide=='Présent' && $bacbiodechetcentredetri == $bacbiodechet120 && $troussedesecours == 'Présent') {


              $suivicentredetri->setPhase('Opérationnel');
              $centredetri->setPhase('Opérationnel');

            }
            else{
                $suivicentredetri->setPhase('Construction');
                $centredetri->setPhase('Construction');
            }

            $suivicentredetri->setCommentaire($request->request->get('commentaire'));
            $suivicentredetri->setDatedesoumission($dateTime);
            $suivicentredetri->setBachenoires($bachenoires);
            $suivicentredetri->setEpi($epi);
            $suivicentredetri->setTroussedesecours($troussedesecours);
            $suivicentredetri->setMaterielsdenettoyage($materielsdenettoyage);
            $suivicentredetri->setVidoirliquide($vidoirliquide);
            $suivicentredetri->setBarrierages($barrierages);
            $suivicentredetri->setMarquageausol($marquageausol);
            $suivicentredetri->setTonnelle($tonnelle);
            $suivicentredetri->setSignaletiques($signaletiques);
            $suivicentredetri->setSupportsachs($supportsachs);
            $suivicentredetri->setBalances($balances);
            $suivicentredetri->setCompacteur($compacteur);
            $suivicentredetri->setTabledetri($tabledetri);
            $suivicentredetri->setBacdr660($bacdr660);
            $suivicentredetri->setBacdr240($bacdr240);
            $suivicentredetri->setBacrecycle240($bacrecycle240);
            $suivicentredetri->setBacrecycle660($bacrecycle660);
            $suivicentredetri->setBacbiodechet120($bacbiodechet120);
            $suivicentredetri->setBennebois($bennebois);
            $suivicentredetri->setBennecarton($bennecarton);
            $suivicentredetri->setBenneplastiques($benneplastiques);
            $suivicentredetri->setCentredetri($centredetri);

            $entityManager->persist($suivicentredetri);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_crud_centredetri');
    }


}
