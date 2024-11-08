<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Entity\Suivi;
use ContainerF8x3SGM\getSuivi2Service;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use phpDocumentor\Reflection\Types\Parent_;

class SuiviCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Suivi::class;
    }
    public function configureActions(Actions $actions):Actions
    {


        $creersuivi = Action::new('Créer un suivi')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_suivi')
            ->createAsGlobalAction();
        $supprimersuivi= Action::new('Supprimer suivi')
            ->linkToCrudAction('supprimersuivi');


        $consultersuivi = Action::new('Consulter les suivis')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('admin_suivi')
            ->createAsGlobalAction();
        $consultersuiviavecphoto = Action::new('Suivis avec photo')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_consulter_suiviavecphoto_custom')
            ->createAsGlobalAction();

        $consulterPhotos = Action::new('Photos du suivi')
            ->linkToCrudAction('photosuivi');

        $ajouterphotosuivi = Action::new('Ajouter des photos')
            ->linkToCrudAction('ajoutphotosuivi');

        return $actions
            ->disable(Action::DELETE)
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX,$creersuivi)
            ->add(Crud::PAGE_INDEX,$supprimersuivi)
            ->add(Crud::PAGE_INDEX,$consultersuivi)
            ->add(Crud::PAGE_INDEX,$consultersuiviavecphoto)
            ->add(Crud::PAGE_INDEX,$ajouterphotosuivi)
            ->add(Crud::PAGE_INDEX,$consulterPhotos);



    }

    public function supprimersuivi(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $suivi = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_suivi_delete_custom',['id'=>  $suivi->getId()]);

    }
    public function photosuivi(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $suivi = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_suivi_photo',['id'=>  $suivi->getId()]);

    }
    public function ajoutphotosuivi(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $suivi = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('ajouter_suivi_photo',['id'=>  $suivi->getId()]);

    }


    public function configureFields(string $pageName): iterable
    {




        return [
            AssociationField::new('auteur','Auteur')->setDisabled(),
            TextField::new('flux','Déchet'),
            AssociationField::new('evenement','Evénement'),
            TextField::new('typecontenant','Type contenant'),
            TextField::new('qualitedetribennes','Qualité de tri')->hideOnIndex(),
            NumberField::new('poids','Poids(T)'),
            IntegerField::new('collecte','Nombre contenants'),
            TextField::new('exutoire','Exutoire')->hideOnIndex(),
            TextField::new('numerobordereau','N° bordereau')->hideOnIndex(),

            AssociationField::new('centredetris','Centre de tri'),
            AssociationField::new('leclient','client'),
        TextareaField::new('commentaire', 'Commentaires')->formatValue(function ($value, $entity) {
            // Raccourcir le commentaire à 50 caractères et ajouter des points de suspension
            return strlen($value) > 10? substr($value, 0, 17) . '...' : $value;
        })->setRequired(false),
        DateField::new('datedesoumission','Date'),
            TimeField::new('heure','Heure'),


        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Suivi')
            ->setEntityLabelInPlural('Suivis')
            ->setSearchFields(['commentaire','flux','typecontenant','hall.nom','leclient.nom','auteur.prenom','centredetris.nom']); // Ajoutez cette ligne pour activer la recherche par commentaire
    }


 /*
    public function configureCrud(Crud $crud): Crud
    {
       return Crud::new()
           ->overrideTemplates([
               'crud/new' => 'accueil.html.twig',
           ]);
    }
 */



}
