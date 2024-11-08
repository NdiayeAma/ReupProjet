<?php

namespace App\Controller\Admin;

use App\Entity\Photosarchives;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class PhotosarchivesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photosarchives::class;
    }
    public function configureActions(Actions $actions):Actions
    {

        $ajouterphotomultiple = Action::new('Ajout photos multiple')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                'style' => 'background-color: #3565AE;'
            ])
            ->linkToRoute('ajouterphoto-form')
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX,$ajouterphotomultiple)
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
            TextField::new('nom')->onlyOnIndex(),
            TextField::new('titre'),
            AssociationField::new('centredetri','Centre de tri'),
            AssociationField::new('commentaires','Incident')->onlyOnIndex(),
            AssociationField::new('suivis','Suivi')->onlyOnIndex(),
            DateField::new('dateupload'),
            TextareaField::new('commentaire', 'Commentaires')->formatValue(function ($value, $entity) {
                // Raccourcir le commentaire à 50 caractères et ajouter des points de suspension
                return strlen($value) > 10? substr($value, 0, 17) . '...' : $value;
            })->setRequired(false),
            ImageField::new('nom','visuel')->setUploadDir("public/uploads")->setBasePath('uploads')->onlyOnIndex(),
            ImageField::new('nom','Image')->setFormType(FileUploadType::class)->setUploadDir("public/uploads")->setBasePath('uploads')->setRequired(false)->onlyOnForms(),

        ];
    }

}
