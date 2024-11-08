<?php

namespace App\Controller\Admin;

use App\Entity\Bacs;
use App\Entity\Centredetri;
use App\Entity\Materiels;
use App\Entity\Suivicentredetri;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SuivicentredetriCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Suivicentredetri::class;
    }

    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function configureActions(Actions $actions):Actions
    {
        $ajout = Action::new('Ajouter suivi')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_suivi_centredetri')
            ->createAsGlobalAction();
        $consulter = Action::new('Consulter suivi')
            ->linkToCrudAction('consultersuivicentredetri');


        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX,$ajout)
            ->add(Crud::PAGE_INDEX,$consulter);


    }




public function consultersuivicentredetri(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
{
    $suivicentredetri = $adminContext->getEntity()->getInstance();

    return $this->redirectToRoute('app_consulter_suivicentre_custom',['id'=>  $suivicentredetri->getId()]);

}
    /*
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Suivicentredetri) {

            $barrierages = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Barrières à louer']);
            $bachesnoires = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'bâches noires']);
            $tabledetri = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Tables de tri']);
            $compacteur = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Compacteurs avec lève-conteneur']);
            $balances = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'balances']);
            $Marquage = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Marquage sol']);
            $supportsachs = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Support Sachs']);
            $Signaletiques = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Signalétique P24']);
            $Tonnelle = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'Tonnelle']);
            $bacdr660 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac dr', 'volume' => '660']);
            $bacdr240 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac dr', 'volume' => '240']);
            $bacrecycle240 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac recycle', 'volume' => '240']);
            $bacrecycle660 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac recycle', 'volume' => '660']);
            $bacbiodechet120 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom' => 'Bac biodechet', 'volume' => '120']);

            $bacbiodechet120->setAlloues($bacbiodechet120->getAlloues() + $entityInstance->getBacbiodechet120());
            $bacbiodechet120->setDisponible($bacbiodechet120->getDisponible() - $entityInstance->getBacbiodechet120());
            $bachesnoires->setAlloues($bachesnoires->getAlloues() + $entityInstance->getBachenoires());
            $bachesnoires->setDisponible($bachesnoires->getDisponible() - $entityInstance->getBachenoires());
            $bacrecycle660->setAlloues($bacrecycle660->getAlloues() + $entityInstance->getBacrecycle660());
            $bacrecycle660->setDisponible($bacrecycle660->getDisponible() - $entityInstance->getBacrecycle660());
            $bacrecycle240->setAlloues($bacrecycle240->getAlloues() + $entityInstance->getBacrecycle240());
            $bacrecycle240->setDisponible($bacrecycle240->getDisponible() - $entityInstance->getBacrecycle240());
            $bacdr240->setAlloues($bacdr240->getAlloues() + $entityInstance->getBacdr240());
            $bacdr240->setDisponible($bacdr240->getDisponible() - $entityInstance->getBacdr240());
            $bacdr660->setAlloues($bacdr660->getAlloues() + $entityInstance->getBacdr660());
            $bacdr660->setDisponible($bacdr660->getDisponible() - $entityInstance->getBacdr660());
            $barrierages->setAlloues($barrierages->getAlloues() + $entityInstance->getBarrierages());
            $barrierages->setDisponibles($barrierages->getDisponibles() - $entityInstance->getBarrierages());
            $tabledetri->setAlloues($tabledetri->getAlloues() + $entityInstance->getTabledetri());
            $tabledetri->setDisponibles($tabledetri->getDisponibles() - $entityInstance->getTabledetri());
            $compacteur->setAlloues($compacteur->getAlloues() + $entityInstance->getCompacteur());
            $compacteur->setDisponibles($compacteur->getDisponibles() - $entityInstance->getCompacteur());
            $balances->setAlloues($balances->getAlloues() + $entityInstance->getBalances());
            $balances->setDisponibles($balances->getDisponibles() - $entityInstance->getBalances());
            $Marquage->setAlloues($Marquage->getAlloues() + $entityInstance->getMarquageausol());
            $Marquage->setDisponibles($Marquage->getDisponibles() - $entityInstance->getMarquageausol());
            $supportsachs->setAlloues($supportsachs->getAlloues() + $entityInstance->getSupportsachs());
            $supportsachs->setDisponibles($supportsachs->getDisponibles() - $entityInstance->getSupportsachs());
            $Signaletiques->setAlloues($Signaletiques->getAlloues() + $entityInstance->getSignaletiques());
            $Signaletiques->setDisponibles($Signaletiques->getDisponibles() - $entityInstance->getSignaletiques());
            $Tonnelle->setAlloues($Tonnelle->getAlloues() + $entityInstance->getTonnelle());
            $Tonnelle->setDisponibles($Tonnelle->getDisponibles() - $entityInstance->getTonnelle());


            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
    */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        if ($entityInstance instanceof Suivicentredetri) {

            $centreancien = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
            $bachesnoires = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type' => 'bâches noires']);
            $barrierages = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Barrières à louer']);
            $tabledetri = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Tables de tri']);
            $compacteur = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Compacteurs avec lève-conteneur']);
            $balances = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'balances']);
            $Marquage = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Marquage sol']);
            $supportsachs = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Support Sachs']);
            $Signaletiques = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Signalétique P24']);
            $Tonnelle = $this->entityManager->getRepository(Materiels::class)->findOneBy(['type'=>'Tonnelle']);
            $bacdr660 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac dr','volume'=>'660']);
            $bacdr240 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac dr','volume'=>'240']);
            $bacrecycle240 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac recycle','volume'=>'240']);
            $bacrecycle660 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac recycle','volume'=>'660']);
            $bacbiodechet120 = $this->entityManager->getRepository(Bacs::class)->findOneBy(['nom'=>'Bac biodechet','volume'=>'120']);

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
            } if($entityInstance->getBacdr660() < $centreancien["bacdr660"]){

                $bacdr660->setAlloues($bacdr660->getAlloues() - ($centreancien["bacdr660"] - $entityInstance->getBacdr660()));
                $bacdr660->setDisponible($bacdr660->getDisponible()+ ($centreancien["bacdr660"] -$entityInstance->getBacdr660()) );
            }elseif ($entityInstance->getBacdr660() > $centreancien["bacdr660"]){
                $bacdr660->setAlloues($bacdr660->getAlloues() + ( $entityInstance->getBacdr660()- $centreancien["bacdr660"]));
                $bacdr660->setDisponible($bacdr660->getDisponible() - ( $entityInstance->getBacdr660()- $centreancien["bacdr660"]) );
            }
            if($entityInstance->getBacdr240() < $centreancien["bacdr240"]){

                $bacdr240->setAlloues($bacdr240->getAlloues() - ($centreancien["bacdr240"] - $entityInstance->getBacdr240()));
                $bacdr240->setDisponible($bacdr240->getDisponible()+ ($centreancien["bacdr240"] -$entityInstance->getBacdr240()) );
            }elseif ($entityInstance->getBacdr240() > $centreancien["bacdr240"]){
                $bacdr240->setAlloues($bacdr240->getAlloues() + ( $entityInstance->getBacdr240()- $centreancien["bacdr240"]));
                $bacdr240->setDisponible($bacdr240->getDisponible() - ( $entityInstance->getBacdr240()- $centreancien["bacdr240"]) );
            }
            if($entityInstance->getBacrecycle240() < $centreancien["bacrecycle240"]){

                $bacrecycle240->setAlloues($bacrecycle240->getAlloues() - ($centreancien["bacrecycle240"] - $entityInstance->getBacrecycle240()));
                $bacrecycle240->setDisponible($bacrecycle240->getDisponible()+ ($centreancien["bacrecycle240"] -$entityInstance->getBacrecycle240()) );
            }elseif ($entityInstance->getBacrecycle240() > $centreancien["bacrecycle240"]){
                $bacrecycle240->setAlloues($bacrecycle240->getAlloues() + ( $entityInstance->getBacrecycle240()- $centreancien["bacrecycle240"]));
                $bacrecycle240->setDisponible($bacrecycle240->getDisponible() - ( $entityInstance->getBacrecycle240()- $centreancien["bacrecycle240"]) );
            }
            if($entityInstance->getBacrecycle660() < $centreancien["bacrecycle660"]){

                $bacrecycle660->setAlloues($bacrecycle660->getAlloues() - ($centreancien["bacrecycle660"] - $entityInstance->getBacrecycle660()));
                $bacrecycle660->setDisponible($bacrecycle660->getDisponible()+ ($centreancien["bacrecycle660"] -$entityInstance->getBacrecycle660()) );
            }elseif ($entityInstance->getBacrecycle660() > $centreancien["bacrecycle660"]){
                $bacrecycle660->setAlloues($bacrecycle660->getAlloues() + ( $entityInstance->getBacrecycle660()- $centreancien["bacrecycle660"]));
                $bacrecycle660->setDisponible($bacrecycle660->getDisponible() - ( $entityInstance->getBacrecycle660()- $centreancien["bacrecycle660"]) );
            }
            if($entityInstance->getBacbiodechet120() < $centreancien["bacbiodechet120"]){

                $bacbiodechet120->setAlloues($bacbiodechet120->getAlloues() - ($centreancien["bacbiodechet120"] - $entityInstance->getBacbiodechet120()));
                $bacbiodechet120->setDisponible($bacbiodechet120->getDisponible()+ ($centreancien["bacbiodechet120"] -$entityInstance->getBacbiodechet120()) );
            }elseif ($entityInstance->getBacbiodechet120() > $centreancien["bacbiodechet120"]){
                $bacbiodechet120->setAlloues($bacbiodechet120->getAlloues() + ( $entityInstance->getBacbiodechet120()- $centreancien["bacbiodechet120"]));
                $bacbiodechet120->setDisponible($bacbiodechet120->getDisponible() - ( $entityInstance->getBacbiodechet120()- $centreancien["bacbiodechet120"]) );
            }
            if($entityInstance->getBachenoires() < $centreancien["bachenoires"]){

                $bachesnoires->setAlloues($bachesnoires->getAlloues() - ($centreancien["bachenoires"] - $entityInstance->getBachenoires()));
                $bachesnoires->setDisponible($bachesnoires->getDisponible()+ ($centreancien["bachenoires"] -$entityInstance->getBachenoires()) );
            }elseif ($entityInstance->getBachenoires() > $centreancien["bachenoires"]){
                $bachesnoires->setAlloues($bachesnoires->getAlloues() + ( $entityInstance->getBachenoires()- $centreancien["bachenoires"]));
                $bachesnoires->setDisponible($bachesnoires->getDisponible() - ( $entityInstance->getBachenoires()- $centreancien["bachenoires"]) );
            }



            $entityManager->flush();
        }
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Suivi ')
            ->setEntityLabelInPlural('Suivis des centres de tri')
            ->setSearchFields(['centredetri.nom','datedesoumission'])
            ->setDateFormat('dd/MM/yyyy');// Ajoutez cette ligne pour activer la recherche par commentaire
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('centredetri','Centre de tri'),
            DateField::new('datedesoumission','Date de soumission')->setFormat('dd/MM/yyyy'),
            TextField::new('phase','Phase')->onlyOnForms(),
            TextareaField::new('Commentaire','Commentaire')->onlyOnForms(),
            IntegerField::new('Barrierages','Barriérages')->onlyOnForms(),
            IntegerField::new('bachenoires','Baches')->onlyOnForms(),
            TextField::new('epi','EPI')->onlyOnForms(),
            IntegerField::new('marquageausol','Marquage au sol')->onlyOnForms(),
            IntegerField::new('tonnelle','Tonnelles')->onlyOnForms(),
            IntegerField::new('signaletiques','Signaletiques')->onlyOnForms(),
            IntegerField::new('supportsachs','Support sachs')->onlyOnForms(),
            IntegerField::new('balances','Balance')->onlyOnForms(),
            IntegerField::new('compacteur','Compacteur')->onlyOnForms(),
            IntegerField::new('tabledetri','Table de tri')->onlyOnForms(),
            TextField::new('vidoirliquide','Vidoir liquide')->onlyOnForms(),
            TextField::new('troussedesecours','Trousse de secours')->onlyOnForms(),
            TextField::new('materielsdenettoyage','Materiels de nettoyage')->onlyOnForms(),
            IntegerField::new('bacdr660','Bac dr 660L')->onlyOnForms(),
            IntegerField::new('bacdr240','Bac dr(240L)')->onlyOnForms(),
            IntegerField::new('bacrecycle240','Bac recycle(240l)')->onlyOnForms(),
            IntegerField::new('bacrecycle660','Bac recycle(660l)')->onlyOnForms(),
            IntegerField::new('bacbiodechet120','Bac biodechet(120l)')->onlyOnForms(),
            IntegerField::new('bennebois','Benne bois')->onlyOnForms(),
            IntegerField::new('bennecarton','Benne carton')->onlyOnForms(),
            IntegerField::new('benneplastiques','Benne plastique')->onlyOnForms(),

        ];
    }

}
