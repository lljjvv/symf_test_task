<?php

namespace App\DataFixtures;

use App\Entity\CV;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    ## php bin/console doctrine:fixtures:load
    public function load(ObjectManager $manager)
    {
        $generator = Factory::create("en_EN");

        for ($i = 0; $i <= 100; $i++) {
            $newCv = new CV();
            $cv = $newCv->setName($generator->firstName);
            $cv = $newCv->setAddress($generator->address);
            $cv = $newCv->setEducation($generator->realText(150));
            $cv = $newCv->setExperience($generator->realText(200));
            $cv = $newCv->setWork($generator->text(20));
            $manager->persist($cv);
        }

        $manager->flush();
    }
}