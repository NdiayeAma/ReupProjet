<?php

namespace App\Controller\Admin;

use App\Entity\Materiels;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MaterielsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Materiels::class;
    }
    public function configureActions(Actions $actions):Actions
    {


        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action
                    ->setHtmlAttributes([
                        'style' => 'background-color: #3565AE;'
                    ]);

            });





    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('photo','visuel')->setUploadDir("public/uploads")->setBasePath('uploads')->setRequired(false),
            TextField::new('type','Type matériel'),
            IntegerField::new('besoin','Besoins'),
            IntegerField::new('livres','Livrés'),
            IntegerField::new('disponibles','Disponible')->setCssClass('text-success'),
            IntegerField::new('commandes','Commandés'),
            IntegerField::new('alloues','Alloués')->setCssClass('text-danger'),
        ];
    }
}
