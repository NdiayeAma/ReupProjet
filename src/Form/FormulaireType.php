<?php

namespace App\Form;

use App\Entity\Formulaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomcompagnie')
            ->add('hall')
            ->add('alleenumerostand')
            ->add('contactstandiste')
            ->add('rse')
            ->add('donnerdesmateriaux')
            ->add('donnerbois')
            ->add('quantitefourniture')
            ->add('quantiteautresmateriaux')
            ->add('commentaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formulaire::class,
        ]);
    }
}
