<?php

namespace App\Controller\Admin;

use App\Entity\Donation;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DonationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Donation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', 'ID')->hideOnForm(),  // Primary key, hidden in forms
            AssociationField::new('evenement', 'Événement'),  // Foreign key association
            TextField::new('company_name', 'Entreprise'),
            TextField::new('hall', 'Hall'),
            AssociationField::new('Hallentity','hall'),
            TextField::new('aisle_booth_number', 'Stand'),
            TextField::new('contact_builder', 'Contact'),
            ArrayField::new('wood_types', 'Types de bois')
                ->setFormType(CollectionType::class)
                ->setFormTypeOptions([
                    'entry_type' => ChoiceType::class,
                    'entry_options' => [
                        'choices' => [
                            'Chêne' => 'oak',
                            'Pin' => 'pine',
                            'Érable' => 'maple',
                            'Acajou' => 'mahogany',
                            'Cèdre' => 'cedar',
                        ],
                        'multiple' => true, // Permet de sélectionner plusieurs types de bois
                        'expanded' => false, // Menu déroulant pour les types de bois
                    ],
                ])
                ->hideOnIndex(),
            ChoiceField::new('wood_types', 'Types de bois')
                ->setChoices([
                    'Chêne' => 'oak',
                    'Pin' => 'pine',
                    'Érable' => 'maple',
                    'Acajou' => 'mahogany',
                    'Cèdre' => 'cedar'
                ])
                ->allowMultipleChoices() // Permet de sélectionner plusieurs valeurs
                ->renderExpanded(false) // Rend un select au lieu de checkboxes
                ->hideOnIndex(),
            BooleanField::new('csr_form_downloaded', 'Guide'),
            ChoiceField::new('wood_types', 'Types de bois')
                ->setChoices([
                    'Bois massif' => 'Bois-massif',
                    'Contre-plaqué' => 'Contre-plaque',
                    'Mélaminé' => 'Melamine',
                    'Aggloméré' => 'Agglomere',
                    'Parquet' => 'Parquet',
                    'OSB' => 'OSB',
                    'Bois MDF' => 'BOIS MDF'
                ])
                ->allowMultipleChoices() // Permet de sélectionner plusieurs types de bois
                ->renderExpanded(false) // Aff
                ->hideOnIndex(),
            BooleanField::new('donate_materials', 'Matériaux'),
            BooleanField::new('donate_wood', 'Bois'), ArrayField::new('wood_quantities', 'Quantités de bois')->hideOnIndex(),
            TextField::new('furniture_quantity', 'Mobilier')->hideOnIndex(),
            NumberField::new('other_materials_quantity', 'Autres matériaux (quantité)')->hideOnIndex(),
            TextareaField::new('comments', 'Commentaires')->hideOnIndex(),
            BooleanField::new('confirmation', 'Confirmation'),
            BooleanField::new('reception', 'Réception'),
            DateTimeField::new('dateupload','Date')->setFormat('Y-MM-dd'),
            BooleanField::new('sensibilisation', 'Sensibilisation'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Donation')
            ->setEntityLabelInPlural('Donations')
            ->setSearchFields(['evenement.nom']);
    }
}
