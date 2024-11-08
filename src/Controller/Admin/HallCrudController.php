<?php

namespace App\Controller\Admin;

use App\Entity\Hall;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Registry\TemplateRegistry;

class HallCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Hall::class;
    }
    public function configureActions(Actions $actions):Actions
    {

        return $actions
            ->disable(Action::DELETE);

    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('nom'),
            AssociationField::new('site','site')->setFormTypeOption('attr', ['required' => 'required'])->setRequired(true)->hideOnForm(),

        ];
    }

}
