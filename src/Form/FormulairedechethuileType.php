<?php

namespace App\Form;

use App\Entity\Formulairedechethuile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulairedechethuileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomhall')
            ->add('allee')
            ->add('nombredesacs')
            ->add('quantitesacs')
            ->add('nombredebidons')
            ->add('commentaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formulairedechethuile::class,
        ]);
    }
}
