<?php

namespace App\Controller\Admin;

use App\Entity\Evenement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EvenementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Evenement::class;
    }
    public function configureActions(Actions $actions):Actions
    {

        $supprimer = Action::new('Supprimer un événement')
            ->linkToCrudAction('supprimerevenement');
        $synthese = Action::new('Synthése générale')
            ->linkToCrudAction('syntheseevenement');
        $creerevenement = Action::new('Créer un événement')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_formulaire_evenement')
            ->createAsGlobalAction();
        $redirectionlien = Action::new('Lien donation')
            ->linkToCrudAction('lienevenement');

        return $actions
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX,$creerevenement)
            ->add(Crud::PAGE_INDEX,$supprimer)
            ->add(Crud::PAGE_INDEX,$synthese)
            ->add(Crud::PAGE_INDEX,$redirectionlien)
            ;



    }
    public function supprimerevenement(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $evenement = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_evenement_delete_custom',['id'=>  $evenement->getId()]);

    }
    public function lienevenement(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $evenement = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_lien_donation',['id'=>  $evenement->getId(),'name'=>$evenement->getNom()]);

    }
    public function syntheseevenement(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $evenement = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_evenement_synthese_custom',['id'=>  $evenement->getId()]);

    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            DateField::new('datedebut','Date de debut'),
            DateField::new('datefin','Date de fin'),
            AssociationField::new('commentaires','Incidents')
                ->onlyOnIndex(),
            AssociationField::new('sites','sites')->setFormTypeOption('attr', ['required' => 'required'])->setRequired(true),
            AssociationField::new('leshalls','halls')->setRequired(true),
        ];
    }

}
