<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class CommentaireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commentaire::class;
    }
    public function configureActions(Actions $actions):Actions
    {


        $creercom = Action::new('Ajouter un incident')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_creer_commentaire')
            ->createAsGlobalAction();

        $consulterphotos = Action::new('Consulter photos')
            ->linkToCrudAction('photocommentaire');

        $ajouterphotos = Action::new('Ajouter photos')
            ->linkToCrudAction('ajouterphotocommentaire');


        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX,$creercom)
            ->add(Crud::PAGE_INDEX,$consulterphotos)
            ->add(Crud::PAGE_INDEX,$ajouterphotos);



    }
    public function ajouterphotocommentaire(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $commentaire = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_ajout_commentaire_photo',['id'=>  $commentaire->getId()]);

    }
    public function photocommentaire(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $commentaire = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_commentaire_photo',['id'=>  $commentaire->getId()]);

    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('datesoumission'),
            TextareaField::new('libelle','commentaire'),
            AssociationField::new('evenement','Evenement'),
            AssociationField::new('photos','Photos'),

        ];
    }

}
