<?php

namespace App\Controller\Admin;

use App\Entity\Bacs;
use App\Entity\Centredetri;
use App\Entity\Materiels;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Select;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CentredetriCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Centredetri::class;
    }
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function configureActions(Actions $actions):Actions
    {
        $graphereporting = Action::new('Reporting des centres')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_graphecentre')
            ->createAsGlobalAction();
        $synthese = Action::new('Synthése du centre')
            ->linkToCrudAction('synthese');
        $supprimer = Action::new('Supprimer suivi')
            ->linkToCrudAction('supprimersuivicentredetri');

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Créer un centre')
                    ->setHtmlAttributes([
                        'style' => 'background-color: #3565AE;'
                    ]);

            })
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX,$graphereporting)
            ->add(Crud::PAGE_INDEX,$synthese)
            ->add(Crud::PAGE_INDEX, $supprimer);





    }
    public function synthese(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $centre = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_centre_synthese_custom',['id'=>  $centre->getId()]);

    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Centredetri) return;
        $newHalls = $entityInstance->getHalls();

     /*
        $barrierages = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Barrières à louer']);
        $tabledetri = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Tables de tri']);
        $compacteur = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Compacteurs avec lève-conteneur']);
        $balances = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'balances']);
        $Marquage = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Marquage sol']);
        $supportsachs = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Support Sachs']);
        $Signaletiques = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Signalétique P24']);
        $Tonnelle = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Tonnelle']);
        $bacdr660 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac dr','volume'=>'660']);
        $bacdr240  = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac dr','volume'=>'240']);


        $bacdr660->setAlloues($bacdr660->getAlloues() + $entityInstance->getBacdr660());
        $bacdr660->setDisponible($bacdr660->getDisponible()-$entityInstance->getBacdr660());

        $barrierages->setAlloues($barrierages->getAlloues() + $entityInstance->getBarrierages());
        $barrierages->setDisponibles($barrierages->getDisponibles()-$entityInstance->getBarrierages());
        $tabledetri->setAlloues($tabledetri->getAlloues() + $entityInstance->getTabledetri());
        $tabledetri->setDisponibles($tabledetri->getDisponibles()-$entityInstance->getTabledetri());
        $compacteur->setAlloues($compacteur->getAlloues() + $entityInstance->getCompacteur());
        $compacteur->setDisponibles($compacteur->getDisponibles()-$entityInstance->getCompacteur());
        $balances->setAlloues($balances->getAlloues() + $entityInstance->getBalances());
        $balances->setDisponibles($balances->getDisponibles()-$entityInstance->getBalances());
        $Marquage->setAlloues($Marquage->getAlloues() + $entityInstance->getMarquageausol());
        $Marquage->setDisponibles($Marquage->getDisponibles()-$entityInstance->getMarquageausol());
        $supportsachs->setAlloues($supportsachs->getAlloues() + $entityInstance->getSupportsachs());
        $supportsachs->setDisponibles($supportsachs->getDisponibles()-$entityInstance->getSupportsachs());
        $Signaletiques->setAlloues($Signaletiques->getAlloues() + $entityInstance->getSignaletiques());
        $Signaletiques->setDisponibles($Signaletiques->getDisponibles()-$entityInstance->getSignaletiques());
        $Tonnelle->setAlloues($Tonnelle->getAlloues() + $entityInstance->getTonnelle());
        $Tonnelle->setDisponibles($Tonnelle->getDisponibles()-$entityInstance->getTonnelle());

      */
        foreach ($newHalls as $hall) {
            $hall->setCentredetri($entityInstance);

        }
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        if ($entityInstance instanceof Centredetri) {

            $newHalls = $entityInstance->getHalls();
            /*
            $centreancien = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);

            $barrierages = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Barrières à louer']);
            $tabledetri = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Tables de tri']);
            $compacteur = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Compacteurs avec lève-conteneur']);
            $balances = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'balances']);
            $Marquage = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Marquage sol']);
            $supportsachs = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Support Sachs']);
            $Signaletiques = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Signalétique P24']);
            $Tonnelle = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Tonnelle']);
            $bacdr660 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac dr','volume'=>'660']);

            if($entityInstance->getBarrierages() < $centreancien["Barrierages"]){

                $barrierages->setAlloues($barrierages->getAlloues() - ($centreancien["Barrierages"] - $entityInstance->getBarrierages()));
                $barrierages->setDisponibles($barrierages->getDisponibles()+ ($centreancien["Barrierages"] -$entityInstance->getBarrierages()) );
            }elseif ($entityInstance->getBarrierages() > $centreancien["Barrierages"]){
                $barrierages->setAlloues($barrierages->getAlloues() + ( $entityInstance->getBarrierages()- $centreancien["Barrierages"]));
                $barrierages->setDisponibles($barrierages->getDisponibles() - ( $entityInstance->getBarrierages()- $centreancien["Barrierages"]) );
            }
            if($entityInstance->getTonnelle() < $centreancien["tonnelle"]){

                $Tonnelle->setAlloues($Tonnelle->getAlloues() - ($centreancien["tonnelle"] - $entityInstance->getTonnelle()));
                $Tonnelle->setDisponibles($Tonnelle->getDisponibles()+ ($centreancien["tonnelle"] -$entityInstance->getTonnelle()) );
            }elseif ($entityInstance->getTonnelle() > $centreancien["tonnelle"]){
                $Tonnelle->setAlloues($Tonnelle->getAlloues() + ( $entityInstance->getTonnelle()- $centreancien["tonnelle"]));
                $Tonnelle->setDisponibles($Tonnelle->getDisponibles() - ( $entityInstance->getTonnelle()- $centreancien["tonnelle"]) );
            }
            if($entityInstance->getBacdr660() < $centreancien["bacdr660"]){

                $bacdr660->setAlloues($bacdr660->getAlloues() - ($centreancien["bacdr660"] - $entityInstance->getBacdr660()));
                $bacdr660->setDisponible($bacdr660->getDisponible()+ ($centreancien["bacdr660"] -$entityInstance->getBacdr660()) );
            }elseif ($entityInstance->getTonnelle() > $centreancien["bacdr660"]){
                $bacdr660->setAlloues($bacdr660->getAlloues() + ( $entityInstance->getBacdr660()- $centreancien["bacdr660"]));
                $bacdr660->setDisponible($bacdr660->getDisponible() - ( $entityInstance->getBacdr660()- $centreancien["bacdr660"]) );
            }
            if($entityInstance->getSignaletiques() < $centreancien["signaletiques"]){

                $Signaletiques->setAlloues($Signaletiques->getAlloues() - ($centreancien["signaletiques"] - $entityInstance->getSignaletiques()));
                $Signaletiques->setDisponibles($Signaletiques->getDisponibles()+ ($centreancien["signaletiques"] -$entityInstance->getSignaletiques()) );
            }elseif ($entityInstance->getSignaletiques() > $centreancien["signaletiques"]){
                $Signaletiques->setAlloues($Signaletiques->getAlloues() + ( $entityInstance->getSignaletiques()- $centreancien["signaletiques"]));
                $Signaletiques->setDisponibles($Signaletiques->getDisponibles() - ( $entityInstance->getSignaletiques()- $centreancien["signaletiques"]) );
            }
            if($entityInstance->getSupportsachs() < $centreancien["supportsachs"]){

                $supportsachs->setAlloues($supportsachs->getAlloues() - ($centreancien["supportsachs"] - $entityInstance->getSupportsachs()));
                $supportsachs->setDisponibles($supportsachs->getDisponibles()+ ($centreancien["supportsachs"] -$entityInstance->getSupportsachs()) );
            }elseif ($entityInstance->getSupportsachs() > $centreancien["supportsachs"]){
                $supportsachs->setAlloues($supportsachs->getAlloues() + ( $entityInstance->getSupportsachs()- $centreancien["supportsachs"]));
                $supportsachs->setDisponibles($supportsachs->getDisponibles() - ( $entityInstance->getSupportsachs()- $centreancien["supportsachs"]) );
            }
            if($entityInstance->getMarquageausol() < $centreancien["marquageausol"]){

                $Marquage->setAlloues($Marquage->getAlloues() - ($centreancien["marquageausol"] - $entityInstance->getMarquageausol()));
                $Marquage->setDisponibles($Marquage->getDisponibles()+ ($centreancien["marquageausol"] -$entityInstance->getMarquageausol()) );
            }elseif ($entityInstance->getSupportsachs() > $centreancien["marquageausol"]){
                $Marquage->setAlloues($Marquage->getAlloues() + ( $entityInstance->getMarquageausol()- $centreancien["marquageausol"]));
                $Marquage->setDisponibles($Marquage->getDisponibles() - ( $entityInstance->getMarquageausol()- $centreancien["marquageausol"]) );
            }
            if($entityInstance->getBalances() < $centreancien["balances"]){

                $balances->setAlloues($balances->getAlloues() - ($centreancien["balances"] - $entityInstance->getBalances()));
                $balances->setDisponibles($balances->getDisponibles()+ ($centreancien["balances"] -$entityInstance->getBalances()) );
            }elseif ($entityInstance->getBalances() > $centreancien["balances"]){
                $balances->setAlloues($balances->getAlloues() + ( $entityInstance->getBalances()- $centreancien["balances"]));
                $balances->setDisponibles($balances->getDisponibles() - ( $entityInstance->getBalances()- $centreancien["balances"]) );
            }
            if($entityInstance->getCompacteur() < $centreancien["compacteur"]){

                $compacteur->setAlloues($compacteur->getAlloues() - ($centreancien["compacteur"] - $entityInstance->getCompacteur()));
                $compacteur->setDisponibles($compacteur->getDisponibles()+ ($centreancien["compacteur"] -$entityInstance->getCompacteur()) );
            }elseif ($entityInstance->getCompacteur() > $centreancien["compacteur"]){
                $compacteur->setAlloues($compacteur->getAlloues() + ( $entityInstance->getCompacteur()- $centreancien["compacteur"]));
                $compacteur->setDisponibles($compacteur->getDisponibles() - ( $entityInstance->getCompacteur()- $centreancien["compacteur"]) );
            }
            if($entityInstance->getTabledetri() < $centreancien["tabledetri"]){

                $tabledetri->setAlloues($tabledetri->getAlloues() - ($centreancien["tabledetri"] - $entityInstance->getTabledetri()));
                $tabledetri->setDisponibles($tabledetri->getDisponibles()+ ($centreancien["tabledetri"] -$entityInstance->getTabledetri()) );
            }elseif ($entityInstance->getTabledetri() > $centreancien["tabledetri"]){
                $tabledetri->setAlloues($tabledetri->getAlloues() + ( $entityInstance->getTabledetri()- $centreancien["tabledetri"]));
                $tabledetri->setDisponibles($tabledetri->getDisponibles() - ( $entityInstance->getTabledetri()- $centreancien["tabledetri"]) );
            }
           */




            foreach ($newHalls as $hall) {
                    $hall->setCentredetri($entityInstance);

            }

            $entityManager->flush();
        }
    }
    public function supprimersuivicentredetri(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $suivicentre = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_suivicentre_delete_custom',['id'=>  $suivicentre->getId()]);

    }



    public function configureFields(string $pageName): iterable
    {




        return [

            AssociationField::new('site','site'),
            TextField::new('nom')->setRequired(true),
            NumberField::new('taille','Taille(m2)')->setRequired(true),
            ChoiceField::new('phase')
                ->setChoices([
                    'Construction' => 'Construction',
                    'Opérationnel' => 'Opérationnel'
                ])
                ->setRequired(true)
                ->setTemplatePath('admin/fields/phase.html.twig'),
            IntegerField::new('Barrierages','Barriérages')->onlyOnForms(),
            IntegerField::new('bachenoires','Baches')->onlyOnForms(),
            IntegerField::new('epi','EPI')->onlyOnForms(),
            IntegerField::new('marquageausol','Marquage au sol')->onlyOnForms(),
            IntegerField::new('tonnelle','Tonnelles')->onlyOnForms(),
            IntegerField::new('signaletiques','Signaletiques')->onlyOnForms(),
            IntegerField::new('supportsachs','Support sachs')->onlyOnForms(),
            IntegerField::new('balances','Balance')->onlyOnForms(),
            IntegerField::new('compacteur','Compacteur')->onlyOnForms(),
            IntegerField::new('tabledetri','Table de tri')->onlyOnForms(),
            IntegerField::new('vidoirliquide','Vidoir liquide')->onlyOnForms(),
            IntegerField::new('troussedesecours','Trousse de secours')->onlyOnForms(),
            IntegerField::new('materielsdenettoyage','Materiels de nettoyage')->onlyOnForms(),
            IntegerField::new('bacdr660','Bac dr 660L')->onlyOnForms(),
            IntegerField::new('bacdr240','Bac dr(240L)')->onlyOnForms(),
            IntegerField::new('bacrecycle240','Bac recycle(240l)')->onlyOnForms(),
            IntegerField::new('bacrecycle660','Bac recycle(660l)')->onlyOnForms(),
            IntegerField::new('bacbiodechet120','Bac biodechet(120l)')->onlyOnForms(),
            IntegerField::new('bennebois','Benne bois')->onlyOnForms(),
            IntegerField::new('bennecarton','Benne carton')->onlyOnForms(),
            IntegerField::new('benneplastiques','Benne plastique')->onlyOnForms(),





            AssociationField::new('halls','Zones')->setFormTypeOption('attr', ['required' => 'required'])->setRequired(true),

        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Centre de tri')
            ->setEntityLabelInPlural('Centres de tri');

    }


}
