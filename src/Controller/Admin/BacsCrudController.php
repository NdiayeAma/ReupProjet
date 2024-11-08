<?php

namespace App\Controller\Admin;

use App\Entity\Bacs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BacsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bacs::class;
    }
    public function configureActions(Actions $actions):Actions
    {
        $Dispatch = Action::new('Dispatch des bacs')
            ->linkToRoute('app_dispatch_bacs_custom')
        ->createAsGlobalAction();


        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setHtmlAttributes([
                        'style' => 'background-color: #3565AE;'
                    ]);

            })
            ->add(Crud::PAGE_INDEX,$Dispatch)

            ;



    }
    public function Dispatchdesbacs(AdminContext $adminContext): RedirectResponse
    {
        $bac = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_dispatch_bacs_custom',['id'=>  $bac->getId()]);

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('photobac','visuel')->setUploadDir("public/uploads")->setBasePath('uploads')->setRequired(false),
            TextField::new('nom','Type bac'),
            IntegerField::new('volume','Volume'),
            IntegerField::new('livres','Livrés'),
            IntegerField::new('disponible','Disponible')->setCssClass('text-success'),
            IntegerField::new('commandes','Commandés'),
            IntegerField::new('alloues','Alloués')->setCssClass('text-danger'),
        ];
    }

}
