<?php

namespace App\Controller;

use App\Entity\Formulaire;
use App\Entity\Photosarchives;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CentredetriRepository;
use App\Repository\ClientRepository;
use App\Repository\EvenementRepository;
use App\Repository\FormulaireRepository;
use App\Repository\HallRepository;
use App\Repository\SiteRepository;
use App\Repository\SuiviRepository;
use App\Repository\UserRepository;
use Container6xLJ7iO\getPdfServiceService;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use FontLib\Table\Type\post;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Snappy\Pdf;
use ZipArchive;


#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AgentController extends AbstractController
{
    private $domPdf;
    public function __construct(Dompdf $domPdf)
    {
        $this->domPdf = $domPdf;
    }


    #[Route('/gerer', name: 'app_gerer_personnel', methods: ['GET'])]
    public function index(Request $request,UserRepository $userRepository,PaginatorInterface $paginator): Response
    {
        $listeusers = $userRepository->findUsers('ROLE_AGENT');

        return $this->render('agent/index.html.twig', [
            'agents' => $listeusers,
            'erreur'=> ''
        ]);
    }

    function getFileNamesArray(string $fileNames): array
    {
        // Utiliser explode pour séparer la chaîne en un tableau de noms de fichiers
        $fileNamesArray = explode(' ', trim($fileNames));

        // Enlever les éléments vides éventuels (au cas où il y a des espaces en trop)
        $fileNamesArray = array_filter($fileNamesArray, fn($value) => !is_null($value) && $value !== '');

        return $fileNamesArray;
    }
    #[Route('/telechargerdocuagent/{id}', name: 'telecharger_documents_agent', methods: ['GET'])]
    public function downloaddocumentagent(Request $request,int $id,UserRepository $userRepository): BinaryFileResponse
    {
        $user = $userRepository->findOneBy(['id'=>$id]);
        $nomphoto = $user->getPhoto();
        $nompermis = $user->getPermis();
        $nomrib = $user->getRib();
        $nomautres = $user->getAutres();


        // Répertoire des fichiers à télécharger
        $fileDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/';

        // Créer un fichier ZIP temporaire
        $zipName = 'documents-' . $user->getNom() . '.zip';
        $zipPath = sys_get_temp_dir() . '/' . $zipName;

        // Créer une instance de ZipArchive
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Impossible de créer l\'archive ZIP.');
        }
        if($nomphoto != null ){ $photochemin = $fileDirectory . $nomphoto;
            if (file_exists($photochemin)) {
                $zip->addFile($photochemin, 'Photo-'.$user->getNom(). '.' . pathinfo($nomphoto, PATHINFO_EXTENSION));
            }}
        if($nomrib != null ){ $ribchemin = $fileDirectory . $nomrib;
            if (file_exists($ribchemin)) {
                $zip->addFile($ribchemin, 'rib-'.$user->getNom(). '.' . pathinfo($nomrib, PATHINFO_EXTENSION));
            }
        }
        if($nompermis != null ){$permichemin = $fileDirectory . $nompermis;
            if (file_exists($permichemin)) {
                $zip->addFile($permichemin, 'Permis-'.$user->getNom(). '.' . pathinfo($nompermis, PATHINFO_EXTENSION));
            }
        }



        // Ajouter chaque fichier au ZIP
        if($nomautres != ''){
        $fileNames = $this->getFileNamesArray($nomautres);
        $conteur=0;
        foreach ($fileNames as $fileName) {
            $conteur += 1;
            $filePath = $fileDirectory . $fileName;

            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'autredocument-'.$conteur. '.' . pathinfo($fileName, PATHINFO_EXTENSION));
            }
        }
        }

        $zip->close();

        // Créer une réponse pour le téléchargement du ZIP
        $response = new BinaryFileResponse($zipPath);

        // Définir le type MIME du fichier ZIP
        $response->headers->set('Content-Type', 'application/zip');

        // Définir le nom du fichier à télécharger
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $zipName
        );

        return $response;
    }



    #[Route('/{id}/supprimer', name: 'app_agent_supprimer', methods: ['GET'])]
    public function supprimerProfesseur($id,Request $request,EntityManagerInterface $entityManager,UserRepository $userRepository,PaginatorInterface $paginator): Response
    {
        $user = $userRepository->findOneBy(['id' => $id ]);
        $entityManager->remove($user);
        $entityManager->flush();
        $listeusersupdate = $userRepository->findUsers('ROLE_AGENT');
        return $this->render('agent/index.html.twig', [
            'agents' => $listeusersupdate,
            'erreur'=>''
        ]);
    }
    #[Route('/{id}/changerole', name: 'app_agent_role', methods: ['GET'])]
    public function changerRole($id,Request $request,UserRepository $userRepository,PaginatorInterface $paginatorn,EntityManagerInterface  $entityManager ): Response
    {
        $user = $userRepository->findOneBy(['id' => $id ]);
        $user->setRoles(['ROLE_ADMIN','ROLE_AGENT']);
        $entityManager->persist($user);
        $entityManager->flush();
        $listeusers = $userRepository->findUsers('ROLE_AGENT');
        return $this->render('agent/index.html.twig', [
            'agents' => $listeusers,
            'erreur'=> ''
        ]);
    }

    #[Route('/ajoutagent', name: 'app_ajouter_agent')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )
                ->setRoles(['ROLE_AGENT']);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_gerer_personnel');
        }

        return $this->render('agent/ajoutagent.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/pdf/{id}', name: 'app_formulaire_pdf',methods : ['GET'])]
    public function gererpdf(FormulaireRepository $formulaireRepository,Request $request,int $id):Response
    {
        $formulaire = $formulaireRepository->findOneBy(['id'=> $id]);
        // Récupérer le contenu Twig

        $html =$this->render('formulaire/show.html.twig', [
            'formulaire' => $formulaire,
        ]);




        // Configurer Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $this->domPdf->setOptions($options);

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
    private function base64EncodeImage($imagePath)
    {
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
        $imageData = file_get_contents($imagePath);
        return 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
    }



    #[Route('/pdf-suivi', name: 'app_suivi_pdf')]
    public function gererpdfsuivi(CentredetriRepository $centredetriRepository, EvenementRepository $evenementRepository, FormulaireRepository $formulaireRepository, Request $request, ClientRepository $clientRepository, HallRepository $hallRepository, SuiviRepository $suiviRepository):Response
    {
        // Convertir l'image en base64
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/logomillenium.png';
        $imagePath2 = $this->getParameter('kernel.project_dir') . '/public/images/reuplogo.png';
        $base64Image = $this->base64EncodeImage($imagePath);
        $base64Image2 = $this->base64EncodeImage($imagePath2);

        // Récupérer les paramètres de recherche depuis la requête
        $clientId = $request->request->get('client');
        $evenementid = $request->request->get('evenementid');
        $matiere = $request->request->get('nomatiere');
        $evenement = $evenementRepository->findOneBy(['id' => $evenementid]);
        $client = $clientRepository->findOneBy(['id' => $clientId]);
        $centredetriid = $request->request->get('centredetri');
        $centredetri = $centredetriRepository->findOneBy(['id' => $centredetriid]);
        $nomsite = $centredetri->getSite()->getNom();
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');
        $resultat = $suiviRepository->getSuivisGroupedByDateAndMatiere($client, $centredetri, $startDate, $endDate, $evenement, $matiere);
        $date = (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d');

        $dates = array_keys($resultat);
        $minDate = min($dates);
        $maxDate = max($dates);


        // Récupérer le contenu Twig
        $html = $this->renderView('accueil/synthesedetaille.html.twig', [
            'suivisGroupes' => $resultat['groupedSuivis'],
            'cumulpoids'=>$resultat['poidsTotal'],
            'client' => $clientId,
            'hall' => $centredetriid,
            'start_Date' => $startDate,
            'end_Date' => $endDate,
            'minDate'=>$minDate,
            'maxDate'=>$maxDate,
            'centredetrisuivi'=>$centredetri,
            'evenementsuivi'=> $evenementRepository->findOneBy(['id' => $evenementid]),
            'base64Image' => $base64Image,
            'base64Image2' => $base64Image2,
            'date'=>$date
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
            $nomsite.'-suivi des flux-'.$date.'.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;




    }

  /*
    #[Route('/pdf-suivi', name: 'app_suivi_pdf',methods : ['GET','POST'])]
    public function gererpdfsuivi(CentredetriRepository $centredetriRepository, EvenementRepository $evenementRepository, FormulaireRepository $formulaireRepository, Request $request, ClientRepository $clientRepository, HallRepository $hallRepository, SuiviRepository $suiviRepository):void
    {

        // Récupérer les paramètres de recherche depuis la requête
        $clientId = $request->query->get('client');
        $evenementid = $request->query->get('evenement');
        $matiere = $request->request->get('nomatiere');
        $evenement = $evenementRepository->findOneBy(['id' => $evenementid]);
        $client = $clientRepository->findOneBy(['id' => $clientId]);
        $centredetriid = $request->query->get('centredetri');
        $centredetri = $centredetriRepository->findOneBy(['id' => $centredetriid]);
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        $resultat = $suiviRepository->getSuivisGroupedByDateAndMatiere($client, $centredetri, $startDate, $endDate, $evenement, $matiere);

        // Récupérer le contenu Twig
        $html = $this->renderView('accueil/synthesedetaille.html.twig', [
            'suivisGroupes' => $resultat,
            'client' => $clientId,
            'hall' => $centredetriid,
            'start_Date' => $startDate,
            'end_Date' => $endDate,
        ]);

        // Debug: Afficher le HTML rendu et vérifier les URLs des images
        file_put_contents('debug.html', $html);

        // Configurer Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', realpath(__DIR__ . '/../../assets/images'));

        $dompdf = new Dompdf($options);

        // Charger le contenu HTML et générer le PDF
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('ama.pdf', ["Attachment" => 0]);


    }
  */


    #[Route('/touslesites', name:"get_touslesites", methods:["GET"])]

    public function gettouslesites(ClientRepository $clientRepository, SiteRepository $siteRepository): JsonResponse
    {
        // Récupérez les sites associés au client
        $sites = $siteRepository->findAll();

        // Convertissez les données en tableau associatif pour le JSON
        $data = [];
        foreach ($sites as $site) {
            $data[] = [
                'id' => $site->getId(),
                'nom' => $site->getNom(),
            ];
        }

        // Retournez les données au format JSON
        return new JsonResponse($data);
    }


     #[Route('/get_sites_by_client/{clientId}', name:"get_sites_by_client", methods:["GET"])]

    public function getSitesByClient(ClientRepository $clientRepository, $clientId): JsonResponse
    {
        // Récupérez les sites associés au client
        $sites = $clientRepository->find($clientId)->getSites();

        // Convertissez les données en tableau associatif pour le JSON
        $data = [];
        foreach ($sites as $site) {
            $data[] = [
                'id' => $site->getId(),
                'nom' => $site->getNom(),
            ];
        }

        // Retournez les données au format JSON
        return new JsonResponse($data);
    }
    #[Route('/get_sites_by_evenement/{evenementId}', name:"get_sites_by_evenement", methods:["GET"])]

    public function getSitesByEvenement(EvenementRepository $evenementRepository, $evenementId): JsonResponse
    {
        // Récupérez les sites associés au client
        $sites = $evenementRepository->find($evenementId)->getSites();

        // Convertissez les données en tableau associatif pour le JSON
        $data = [];
        foreach ($sites as $site) {
            $data[] = [
                'id' => $site->getId(),
                'nom' => $site->getNom(),
            ];
        }

        // Retournez les données au format JSON
        return new JsonResponse($data);
    }

    #[Route('/get_evenements_by_client/{clientId}', name:"get_evenements_by_client", methods:["GET"])]

    public function getevenementsByClient(ClientRepository $clientRepository, $clientId): JsonResponse
    {
        // Récupérez les sites associés au client
        $evenements = $clientRepository->find($clientId)->getEvenements();

        // Convertissez les données en tableau associatif pour le JSON
        $data = [];
        foreach ($evenements as $evenement) {
            $data[] = [
                'id' => $evenement->getId(),
                'nom' => $evenement->getNom(),
                'datedebut'=>$evenement->getDatedebut(),
                'datefin'=>$evenement->getDatefin()
            ];
        }

        // Retournez les données au format JSON
        return new JsonResponse($data);
    }


      #[Route("/get_halls_by_site/{siteId}", name:"get_halls_by_site", methods:["GET"])]

    public function getHallsBySite(SiteRepository $siteRepository, $siteId): JsonResponse
    {
        // Récupérez les halls associés au site
        $halls = $siteRepository->find($siteId)->getLeshalls();

        // Convertissez les données en tableau associatif pour le JSON
        $data = [];
        foreach ($halls as $hall) {
            $data[] = [
                'id' => $hall->getId(),
                'nom' => $hall->getNom(),
            ];
        }

        // Retournez les données au format JSON
        return new JsonResponse($data);
    }

    #[Route("/get_centres_by_site/{siteId}", name:"get_centres_by_site", methods:["GET"])]

    public function getcentressBySite(SiteRepository $siteRepository, $siteId): JsonResponse
    {
        // Récupérez les halls associés au site
        $centres = $siteRepository->find($siteId)->getCentredetris();

        // Convertissez les données en tableau associatif pour le JSON
        $data = [];
        foreach ($centres as $centre) {
            $data[] = [
                'id' => $centre->getId(),
                'nom' => $centre->getNom(),
            ];
        }

        // Retournez les données au format JSON
        return new JsonResponse($data);
    }

}
