<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin1 = new User();
        $admin1->setUsername('admin');
        $admin1->setRoles(['ROLE_USER']);
        $admin1->setPassword($this->hasher->hashPassword($admin1, 'admin'));
        $manager->persist($admin1);

        $admin2 = new User();
        $admin2->setUsername('admin1');
        $admin2->setRoles(['ROLE_USER']);
        $admin2->setPassword($this->hasher->hashPassword($admin2, 'admin'));
        $manager->persist($admin2);

        $khaled = new User();
        $khaled->setUsername('khaled');
        $khaled->setRoles(['ROLE_USER']);
        $khaled->setPassword($this->hasher->hashPassword($khaled, '123'));
        $manager->persist($khaled);

        $manager->flush();
    }
}
