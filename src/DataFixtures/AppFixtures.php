<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();


        $site = new Site();
        $site->setNom("Site1");
        $manager->persist($site);


        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setNom($faker->lastName);
            $participant->setPrenom($faker->firstName);
            $participant->setPseudo($faker->userName);
            $participant->setPassword($this->userPasswordHasher->hashPassword($participant, "123$i"));
            $participant->setTelephone($faker->phoneNumber);
            $participant->setMail($faker->email);
            $participant->setAdministrateur($faker->boolean);
            $participant->setActif($faker->boolean);
            $participant->setSite($site);

            $manager->persist($participant);
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
