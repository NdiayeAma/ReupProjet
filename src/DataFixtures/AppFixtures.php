<?php

namespace App\DataFixtures;

use App\Entity\Bacs;
use App\Entity\Materiels;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $barrierages = new Materiels();
        $barrierages->setType('Barrières à louer')
            ->setBesoin(30)
            ->setPhoto('barriere.jpeg');
        $baches = new Materiels();
        $baches->setType('bâches noires')
            ->setBesoin(30)
            ->setPhoto('bachenoire.png');

        $tabledetri= new Materiels();
        $tabledetri->setType('Tables de tri')
            ->setBesoin(4)
            ->setPhoto('tabledetri.jpeg');

        $bom= new Materiels();
        $bom->setType('BOM Biodéchets à sous-traiter')
            ->setBesoin(1)
            ->setPhoto('Bombiodechet.jpeg');

        $compacteur = new Materiels();
        $compacteur->setType('Compacteurs avec lève-conteneur')
            ->setBesoin(4)
            ->setPhoto('compacteur.jpeg');

        $balances = new Materiels();
        $balances->setType('balances')
            ->setBesoin(3)
            ->setPhoto('balance.jpeg');

        $Marquage = new Materiels();
        $Marquage->setType('Marquage sol')
            ->setBesoin(1)
            ->setPhoto('Marquagesol.jpeg');

        $supportsachs = new Materiels();
        $supportsachs->setType('Support Sachs')
            ->setBesoin(8)
            ->setPhoto('supportsachs.jpeg');

        $Signaletiques = new Materiels();
        $Signaletiques->setType('Signalétique P24')
            ->setBesoin(21)
            ->setPhoto('Signaletiques.jpeg');

        $Tonnelle = new Materiels();
        $Tonnelle->setType('Tonnelle')
            ->setBesoin(21)
            ->setPhoto('tonnelle.jpeg');

        $bacdr660 = new Bacs();
        $bacdr660->setNom('Bac dr')
            ->setVolume(660)
            ->setPhotobac('bacsdr660.jpg');
        $bacdr240 = new Bacs();
        $bacdr240->setNom('Bac dr')
            ->setVolume(240)
            ->setPhotobac('bacdr240.jpg');
        $bacrecycle240 = new Bacs();
        $bacrecycle240->setNom('Bac recycle')
            ->setVolume(240)
            ->setPhotobac('bacrecycle240.jpg');
        $bacrecycle660 = new Bacs();
        $bacrecycle660->setNom('Bac recycle')
            ->setVolume(660)
            ->setPhotobac('bacrecycle660.jpg');
        $bacbiodechet120 = new Bacs();
        $bacbiodechet120->setNom('Bac biodechet')
            ->setVolume(120)
            ->setPhotobac('bacbiodechet120.jpg');

        $manager->persist($bacdr660);
        $manager->persist($bacdr240);
        $manager->persist($bacrecycle240);
        $manager->persist($bacrecycle660);
        $manager->persist($bacbiodechet120);
        $manager->persist($barrierages);
        $manager->persist($tabledetri);
        $manager->persist($bom);
        $manager->persist($compacteur);
        $manager->persist($balances);
        $manager->persist($Marquage);
        $manager->persist($supportsachs);
        $manager->persist($Signaletiques);
        $manager->persist($Tonnelle);
        $manager->persist($baches);

        $manager->flush();
    }
}
