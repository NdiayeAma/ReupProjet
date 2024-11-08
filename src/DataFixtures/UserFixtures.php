<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setLogin('admin')

            ->setPassword($this->passwordHasher->hashPassword(
                $user,'secret'
            ))
            ->setNom('Ndiaye')
            ->setPrenom('Amadou')
            ->setRoles(['ROLE_ADMIN']);




        $manager->persist($user);
        $manager->flush();
    }
}
