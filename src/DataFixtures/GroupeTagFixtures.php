<?php 

namespace App\DataFixtures;

use App\Entity\GroupeTag;
use App\DataFixtures\TagFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GroupeTagFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $group = new GroupeTag('administrators');
        // this reference returns the User object created in UserFixtures
        $group->addTag($this->getReference(TagFixtures::TAG_REFERENCE));

        $manager->persist($group);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            TagFixtures::class,
        );
    }
}