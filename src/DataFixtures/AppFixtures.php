<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create();


        $site = new Site();
        $site->setNom("Site1");
        $manager->persist($site);



        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setNom($faker->lastName);
            $participant->setPrenom($faker->firstName);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setMail($faker->email);
            $participant->setAdministrateur($faker->boolean);
            $participant->setActif($faker->boolean);

            $manager->persist($participant);
        }



        for($i = 0; $i < 10; $i++){
            $etat = new Etat();
            $etat->setLibelle($faker->libelle);
            $manager->persist($etat);
        }



        for ($i = 0; $i < 10; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);

            $manager->persist($ville);

            // Créez plusieurs entités Lieu associées à la ville
            for ($j = 0; $j < 5; $j++) {
                $lieu = new Lieu();
                $lieu->setNom($faker->company);
                $lieu->setRue($faker->streetAddress);
                $lieu->setLatitude($faker->latitude);
                $lieu->setLongitude($faker->longitude);
                $lieu->setVille($ville);

                $manager->persist($lieu);
            }
        }



        $manager->flush();
    }
}
