<?php 

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public const TAG_REFERENCE = 'tag';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $tag = new Tag();
        $times = 3;
        for ($i=1; $i <= $times; $i++) {
            $tag->setLibelle($faker->title);
            
        }
        $manager->persist($tag);
        $manager->flush();

        // other fixtures can get this object using the UserFixtures::TAG_REFERENCE constant
        $this->addReference(self::TAG_REFERENCE, $tag);
    }
}