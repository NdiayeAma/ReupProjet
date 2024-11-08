<?php

namespace App\Form;

use App\Entity\Hall;
use App\Entity\Suivi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('flux')
            ->add('typecontenant')
            ->add('volumecontenant')
            ->add('poids')
            ->add('tauxderemplissage')
            ->add('collecte')
            ->add('cumulflux')
            ->add('qualitedetribennes')
            ->add('estimatifbennes')
            ->add('collectebennes')
            ->add('cumulbennes')
            ->add('datedesoumission', null, [
                'widget' => 'single_text',
            ])
            ->add('hall', EntityType::class, [
                'class' => Hall::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
        ]);
    }
}
