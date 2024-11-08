<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SiteCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Site::class;
    }
    public function configureActions(Actions $actions):Actions
    {
        $supprimer = Action::new('Supprimer site')
            ->linkToCrudAction('supprimersite');
        $creersite = Action::new('Creer un site')
            ->linkToCrudAction('creersite')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('app_site_form_site')
            ->createAsGlobalAction();

        return $actions
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, $creersite)
        ->add(Crud::PAGE_INDEX,$supprimer);


    }
    public function supprimersite(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $site = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_site_delete_custom',['id'=>  $site->getId()]);

    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            AssociationField::new('leshalls','Zones')->setFormTypeOption('attr', ['required' => 'required'])->setRequired(true)->hideOnForm(),
            AssociationField::new('evenements','Evenements'),
        ];
    }

}
