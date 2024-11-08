<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\DBAL\Types\Type;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Config\Security\PasswordHasherConfig;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
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

/*
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('login','Login'),
            TextField::new('password','Mot de passe')->setFormType(Type\),
            TextField::new('prenom','Prénom'),
            TextField::new('nom','Nom'),



            TextEditorField::new('description'),
        ];
    }
*/

}
