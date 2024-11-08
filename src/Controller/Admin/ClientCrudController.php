<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Repository\ClientRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use http\Env\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class
ClientCrudController extends AbstractCrudController
{
    public const ACTION_SUPPRIMER = 'duplicatesupp';
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureActions(Actions $actions):Actions
    {

        $supprimer = Action::new('Supprimer client')
            ->linkToCrudAction('supprimerclient');
        $creerclient = Action::new('Créer un client')
            ->setCssClass('btn btn-primary action-foo')
            ->setHtmlAttributes([
                    'style' => 'background-color: #3565AE;'
                ])
            ->linkToRoute('app_formulaire_client')
            ->createAsGlobalAction();

        return $actions
            ->disable(Action::DELETE)
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX,$creerclient)
            ->add(Crud::PAGE_INDEX, $supprimer);



    }

/*
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Vérifiez si l'entité est un objet Client
        if ($entityInstance instanceof Client) {
            // Accédez aux nouvelles données associées au client, par exemple les nouveaux sites ou halls
            $newSites = $entityInstance->getSites();
            $newHalls = $entityInstance->getLeshalls();
            $clientancien = $entityManager->getRepository(Client::class)->findOneBy(['id'=>$entityInstance->getId()]);


            // Modifiez manuellement le client en conséquence, par exemple en ajoutant ou supprimant des références aux sites ou halls
            // Notez que vous devez ajuster cette logique en fonction de vos besoins métier spécifiques
            foreach ($newSites as $site) {

                // Assurez-vous que le site n'est pas déjà associé au client pour éviter les doublons
                $site->addLeclient($clientancien);
            }

            foreach ($newHalls as $hall) {
                if (!$clientancien->getSites()->contains($hall)) {
                    $hall->setClient($clientancien);
                }
            }

            // Persistez les modifications
            $entityManager->persist($clientancien);
            $entityManager->flush();
        }
    }
*/

    public function supprimerclient(AdminContext $adminContext): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $client = $adminContext->getEntity()->getInstance();

        return $this->redirectToRoute('app_client_delete_custom',['id'=>  $client->getId()]);

    }
    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('nom'),
            AssociationField::new('evenements','Evenements'),



        ];
    }

}
