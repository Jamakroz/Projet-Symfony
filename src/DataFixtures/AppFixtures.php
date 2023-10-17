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
        $site->setNom("Campus ENI Niort");
        $manager->persist($site);
        $site = new Site();
        $site->setNom("Campus ENI Nantes");
        $manager->persist($site);
        $site = new Site();
        $site->setNom("Campus ENI Quimper");
        $manager->persist($site);
        $site = new Site();
        $site->setNom("Campus ENI Rennes");
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

        // Créez les villes
        $villesData = [
            ['nom' => 'Nantes', 'codePostal' => '44800'],
            ['nom' => 'Rennes', 'codePostal' => '35131'],
            ['nom' => 'Quimper', 'codePostal' => '29000'],
            ['nom' => 'Niort', 'codePostal' => '79000'],
        ];

        foreach ($villesData as $index => $villeData) {
            $ville = new Ville();
            $ville->setNom($villeData['nom']);
            $ville->setCodePostal($villeData['codePostal']);
            $manager->persist($ville);

            // Créez un lieu associé à la ville
            $lieu = new Lieu();
            $lieu->setNom("ENI - Campus " . $villeData['nom']);
            $lieu->setRue($index === 0 ? '3 Rue Michael Faraday' : ($index === 1 ? '8 Rue Léo Lagrange' : ($index === 2 ? '2 Rue Georges Perros' : '19 Av. Léo Lagrange Bâtiment B et C')));
            $lieu->setVille($ville);

            $manager->persist($lieu);
        }

        $manager->flush();

    }
}
