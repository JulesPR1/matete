<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Categorie;
use App\Entity\Emplacement;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i < 10; $i++){
            $categorie = new Categorie();
            $categorie->setNom("cat".$i)
                      ->setDescription("");
            $manager->persist($categorie);

            $vendeur = new User();
            $vendeur->setUsername("user ".$i)
                    ->setPassword("")
                    ->setEmail("user".$i."@test.fr")
                    ->setNumtel("");
            $manager->persist($vendeur);
            

            $emplacement = new Emplacement();
            $emplacement->setAdresse('')
                        ->setCodePostal("")
                        ->setLeUser($vendeur);
            $manager->persist($emplacement);

            $annonce = new Annonce();
            $annonce->setNom('Annonce '.$i)
                    ->setDescription("")
                    ->setQuantite(0)
                    ->setImage("allo")
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setLaCategorie($categorie)
                    ->setLeUser($vendeur)
                    ->setEmplacement($emplacement);
            $manager->persist($annonce);
        } 
        $manager->flush();
    }
}
